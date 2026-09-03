<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * FluentDatatables
 * High-performance fluent DataTables server-side processor supporting split-query (late row lookup) and fulltext search.
 */
class FluentDatatables
{
    protected $CI;
    protected $table;
    protected $primary_key = 'id';
    protected $select = '*';
    protected $where = [];
    protected $where_in = [];
    protected $joins = [];
    protected $group_by = [];
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];
    protected $add_columns = [];
    protected $edit_columns = [];
    protected $result_processor = null;
    protected $manual_recordsTotal = null;
    protected $manual_recordsFiltered = null;
    protected $manual_data = null;
    protected $use_fulltext = false;
    protected $fulltext_mode = 'BOOLEAN MODE';
    protected $late_lookup_callback = null;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function of($table)
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    public function setPrimaryKey($key)
    {
        $this->primary_key = $key;
        return $this;
    }

    public function select($select)
    {
        $this->select = $select;
        return $this;
    }

    public function where($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->where[] = ['key' => $k, 'value' => $v, 'escape' => true];
            }
        } else {
            $this->where[] = ['key' => $key, 'value' => $value, 'escape' => true];
        }
        return $this;
    }

    public function where_in($key, $values)
    {
        $this->where_in[] = ['key' => $key, 'values' => $values];
        return $this;
    }

    public function join($table, $cond, $type = '')
    {
        $this->joins[] = ['table' => $table, 'cond' => $cond, 'type' => $type];
        return $this;
    }

    public function groupBy($column)
    {
        $this->group_by[] = $column;
        return $this;
    }

    public function set_column_order($columns)
    {
        $this->column_order = $columns;
        return $this;
    }

    public function set_orderable_columns($columns)
    {
        return $this->set_column_order($columns);
    }

    public function set_column_search($columns)
    {
        $this->column_search = $columns;
        return $this;
    }

    public function set_searchable_columns($columns)
    {
        return $this->set_column_search($columns);
    }

    public function enableFulltextSearch($mode = 'BOOLEAN MODE')
    {
        $this->use_fulltext = true;
        $this->fulltext_mode = $mode;
        return $this;
    }

    public function set_default_order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function addColumn($name, $callback)
    {
        $this->add_columns[$name] = $callback;
        return $this;
    }

    public function editColumn($name, $callback)
    {
        $this->edit_columns[$name] = $callback;
        return $this;
    }

    public function setRecordsTotal($total)
    {
        $this->manual_recordsTotal = $total;
        return $this;
    }

    public function setRecordsFiltered($count)
    {
        $this->manual_recordsFiltered = $count;
        return $this;
    }

    public function params($params)
    {
        if (is_array($params) && !empty($params)) {
            $_POST = array_merge($_POST, $params);
        }
        return $this;
    }

    public function setQuery($callback)
    {
        $this->CI->db->reset_query();
        call_user_func($callback, $this->CI->db);
        return $this;
    }

    public function filter($callback)
    {
        call_user_func($callback, $this->CI->db);
        return $this;
    }

    public function modifyResult($callback)
    {
        $this->result_processor = $callback;
        return $this;
    }

    public function setData($data)
    {
        $this->manual_data = $data;
        return $this;
    }

    public function withLateLookup(callable $callback)
    {
        $this->late_lookup_callback = $callback;
        return $this;
    }

    public function make($json = true)
    {
        return $this->generate(null, $json);
    }

    public function generate($late_lookup_callback = null, $json = true)
    {
        if ($late_lookup_callback !== null) {
            $this->late_lookup_callback = $late_lookup_callback;
        }
        $db = $this->CI->db;

        if ($this->manual_data !== null) {
            return $this->_handle_manual_data($json);
        }

        if ($this->manual_recordsTotal !== null) {
            $recordsTotal = $this->manual_recordsTotal;
        } else {
            $temp_db = clone $db;
            $this->apply_query($temp_db);
            $recordsTotal = $temp_db->count_all_results('', false);
        }

        $this->apply_query($db);
        $this->apply_search($db);
        $search_val = $_POST['search']['value'] ?? '';

        if ($this->manual_recordsFiltered !== null) {
            $recordsFiltered = $this->manual_recordsFiltered;
        } elseif ($search_val == '') {
            $recordsFiltered = $recordsTotal;
        } else {
            $temp_s = clone $db;
            $recordsFiltered = $temp_s->count_all_results('', false);
        }

        $this->apply_order($db);
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
            $db->limit((int) $_POST['length'], $start);
        }

        $result = ($this->late_lookup_callback !== null) ? $this->_execute_split_query($db) : $db->get()->result();

        if ($this->result_processor !== null) {
            $processor = $this->result_processor;
            $result = $processor($result);
        }

        return $this->_format_output($result, $recordsTotal, $recordsFiltered, $json);
    }

    protected function _execute_split_query($db)
    {
        $col = $this->table ? $this->table . '.' . $this->primary_key : $this->primary_key;
        $db->select($col);
        $id_results = $db->get()->result_array();

        if (empty($id_results)) {
            return [];
        }

        $ids = [];
        foreach ($id_results as $row) {
            $ids[] = reset($row);
        }

        $db->reset_query();
        if ($this->table) {
            $db->from($this->table);
        }
        $db->where_in($col, $ids);
        $this->apply_order($db);
        call_user_func($this->late_lookup_callback, $db, $ids);

        return $db->get()->result();
    }

    protected function _handle_manual_data($json)
    {
        $result = $this->manual_data;
        $recordsTotal = $this->manual_recordsTotal !== null ? $this->manual_recordsTotal : count($result);

        if ($this->manual_recordsFiltered === null) {
            $recordsFiltered = count($result);
            if (isset($_POST['length']) && $_POST['length'] != -1) {
                $start = isset($_POST['start']) ? (int) $_POST['start'] : 0;
                $result = array_slice($result, $start, (int) $_POST['length']);
            }
        } else {
            $recordsFiltered = $this->manual_recordsFiltered;
        }

        if ($this->result_processor !== null) {
            $processor = $this->result_processor;
            $result = $processor($result);
        }

        return $this->_format_output($result, $recordsTotal, $recordsFiltered, $json);
    }

    protected function apply_query($db)
    {
        $db->select($this->select);
        if ($this->table) {
            $db->from($this->table);
        }
        foreach ($this->joins as $j) {
            $db->join($j['table'], $j['cond'], $j['type']);
        }
        foreach ($this->where as $w) {
            $db->where($w['key'], $w['value'], $w['escape']);
        }
        foreach ($this->where_in as $win) {
            $db->where_in($win['key'], $win['values']);
        }
        foreach ($this->group_by as $g) {
            $db->group_by($g);
        }
    }

    protected function apply_search($db)
    {
        $search_value = $_POST['search']['value'] ?? '';
        if ($search_value != '' && !empty($this->column_search)) {
            $db->group_start();
            if ($this->use_fulltext) {
                $safe_search = $db->escape_str($search_value);
                if (strpos($this->fulltext_mode, 'BOOLEAN') !== false && !preg_match('/[+\-<>()~*\"@]+/', $safe_search)) {
                    $formatted = '';
                    foreach (explode(' ', $safe_search) as $term) {
                        if (trim($term) !== '') {
                            $formatted .= '+' . trim($term) . '* ';
                        }
                    }
                    $safe_search = trim($formatted);
                }

                $tables = [];
                foreach ($this->column_search as $item) {
                    $parts = explode('.', $item);
                    if (count($parts) > 1) {
                        $tables[$parts[0]][] = $parts[1];
                    } else {
                        $tables[''][] = $item;
                    }
                }

                $first = true;
                foreach ($tables as $table_prefix => $cols) {
                    $full_cols = [];
                    foreach ($cols as $c) {
                        $full_cols[] = ($table_prefix !== '') ? $table_prefix . '.' . $c : $c;
                    }
                    $match_query = "MATCH(" . implode(', ', $full_cols) . ") AGAINST('$safe_search' IN {$this->fulltext_mode})";
                    if ($first) {
                        $db->where($match_query, null, false);
                        $first = false;
                    } else {
                        $db->or_where($match_query, null, false);
                    }
                }
            } else {
                $i = 0;
                foreach ($this->column_search as $item) {
                    if ($i === 0) {
                        $db->like($item, $search_value);
                    } else {
                        $db->or_like($item, $search_value);
                    }
                    $i++;
                }
            }
            $db->group_end();
        }
    }

    protected function apply_order($db)
    {
        if (isset($_POST['order']) && !empty($this->column_order)) {
            $column_idx = $_POST['order']['0']['column'];
            $dir = $_POST['order']['0']['dir'];
            if (isset($this->column_order[$column_idx])) {
                $db->order_by($this->column_order[$column_idx], $dir);
            }
        } elseif (!empty($this->order)) {
            $order = $this->order;
            $db->order_by(key($order), $order[key($order)]);
        }
    }

    protected function _format_output($result, $recordsTotal, $recordsFiltered, $json = true)
    {
        $data = [];
        $no = (int) ($_POST['start'] ?? 0);

        foreach ($result as $row) {
            $no++;
            $item = (array) $row;
            foreach ($this->edit_columns as $col => $cb) {
                if (array_key_exists($col, $item)) {
                    $item[$col] = $cb($row);
                }
            }
            foreach ($this->add_columns as $col => $cb) {
                $item[$col] = $cb($row);
            }
            $data[] = $item;
        }

        $output = [
            "draw"            => intval($_POST['draw'] ?? 0),
            "recordsTotal"    => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data"            => $data
        ];

        if ($json) {
            $json_str = json_encode($output);
            $this->CI->output
                ->set_content_type('application/json')
                ->set_header('Content-Length: ' . strlen($json_str))
                ->set_output($json_str);
            return;
        }
        return $output;
    }

    protected function reset()
    {
        $this->table = null;
        $this->primary_key = 'id';
        $this->select = '*';
        $this->where = [];
        $this->where_in = [];
        $this->joins = [];
        $this->group_by = [];
        $this->column_order = [];
        $this->column_search = [];
        $this->order = [];
        $this->add_columns = [];
        $this->edit_columns = [];
        $this->manual_recordsTotal = null;
        $this->manual_recordsFiltered = null;
        $this->manual_data = null;
        $this->use_fulltext = false;
        $this->fulltext_mode = 'BOOLEAN MODE';
        $this->result_processor = null;
        $this->late_lookup_callback = null;
    }
}
