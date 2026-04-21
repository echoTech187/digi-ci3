<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Datatables Library for CodeIgniter 3
 * 
 * A fluent interface for DataTables server-side processing,
 * inspired by yajra/laravel-datatables.
 */
class Datatables
{
    protected $CI;
    protected $table;
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];
    protected $where = [];
    protected $add_columns = [];
    protected $edit_columns = [];
    protected $joins = [];
    protected $select = '*';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    /**
     * Set the table name
     */
    public function of($table)
    {
        $this->reset();
        $this->table = $table;
        return $this;
    }

    /**
     * Set SELECT columns
     */
    public function select($select)
    {
        $this->select = $select;
        return $this;
    }

    /**
     * Add WHERE clause
     */
    public function where($key, $value = NULL)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->where[] = ['key' => $k, 'value' => $v, 'escape' => TRUE];
            }
        } else {
            $this->where[] = ['key' => $key, 'value' => $value, 'escape' => TRUE];
        }
        return $this;
    }

    /**
     * Add JOIN clause
     */
    public function join($table, $cond, $type = '')
    {
        $this->joins[] = ['table' => $table, 'cond' => $cond, 'type' => $type];
        return $this;
    }

    /**
     * Set columns for ordering
     */
    public function set_column_order($columns)
    {
        $this->column_order = $columns;
        return $this;
    }

    /**
     * Set columns for searching
     */
    public function set_column_search($columns)
    {
        $this->column_search = $columns;
        return $this;
    }

    /**
     * Set default order
     */
    public function set_default_order($order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Add a virtual column
     */
    public function addColumn($name, $callback)
    {
        $this->add_columns[$name] = $callback;
        return $this;
    }

    /**
     * Edit an existing column
     */
    public function editColumn($name, $callback)
    {
        $this->edit_columns[$name] = $callback;
        return $this;
    }

    /**
     * Generate the response
     */
    public function make($json = TRUE)
    {
        // 1. Calculate recordsTotal (Unfiltered by search, but filtered by static where/joins)
        $recordsTotal = $this->count_all();
        
        // 2. Prepare the main query on the CI builder
        $this->apply_query($this->CI->db);
        
        // 3. Apply search filter
        $this->apply_search($this->CI->db);
        
        // 4. Calculate recordsFiltered (Count after search)
        // If no search is applied, recordsFiltered is the same as recordsTotal
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if ($search_value == '') {
            $recordsFiltered = $recordsTotal;
        } else {
            // We clone the current builder state to perform the count without clearing it
            $temp_db = clone $this->CI->db;
            $recordsFiltered = $temp_db->count_all_results('', FALSE);
        }
        
        // 5. Apply order and limit to the main builder
        $this->apply_order($this->CI->db);
        
        // 6. Limit
        if (isset($_POST['length']) && $_POST['length'] != -1) {
            $this->CI->db->limit($_POST['length'], $_POST['start']);
        }
        
        $query = $this->CI->db->get();
        $result = $query->result();
        
        $data = [];
        $no = isset($_POST['start']) ? $_POST['start'] : 0;
        
        foreach ($result as $row) {
            $no++;
            $item = (array) $row;
            $item['DT_RowId'] = isset($row->id) ? $row->id : null;
            
            // Apply editColumn
            foreach ($this->edit_columns as $col => $callback) {
                if (isset($item[$col])) {
                    $item[$col] = $callback($row);
                }
            }
            
            // Apply addColumn
            foreach ($this->add_columns as $col => $callback) {
                $item[$col] = $callback($row);
            }
            
            $data[] = $item;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
        ];

        if ($json) {
            $this->CI->output
                ->set_content_type('application/json')
                ->set_output(json_encode($output));
            return;
        }

        return $output;
    }

    protected function apply_query($db = NULL)
    {
        $db = $db ?: $this->CI->db;
        $db->select($this->select);
        $db->from($this->table);
        
        foreach ($this->joins as $j) {
            $db->join($j['table'], $j['cond'], $j['type']);
        }
        
        foreach ($this->where as $w) {
            $db->where($w['key'], $w['value'], $w['escape']);
        }
    }

    protected function apply_search($db = NULL)
    {
        $db = $db ?: $this->CI->db;
        $search_value = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
        if ($search_value != '') {
            $db->group_start();
            $i = 0;
            foreach ($this->column_search as $item) {
                if ($i === 0) {
                    $db->like($item, $search_value);
                } else {
                    $db->or_like($item, $search_value);
                }
                $i++;
            }
            $db->group_end();
        }
    }

    protected function apply_order($db = NULL)
    {
        $db = $db ?: $this->CI->db;
        if (isset($_POST['order'])) {
            $db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (!empty($this->order)) {
            $order = $this->order;
            $db->order_by(key($order), $order[key($order)]);
        }
    }

    protected function count_all()
    {
        // Optimization for very large tables if no complex where is set
        if (empty($this->where) && empty($this->joins)) {
            $table_name = explode(' ', $this->table)[0];
            $query = $this->CI->db->query("SELECT TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table_name}'");
            $result = $query->row();
            if ($result) return (int) $result->TABLE_ROWS;
        }

        // Use a temporary clone to avoid polluting the main builder
        $temp_db = clone $this->CI->db;
        $this->apply_query($temp_db);
        return $temp_db->count_all_results('', FALSE);
    }

    protected function reset()
    {
        $this->table = NULL;
        $this->column_order = [];
        $this->column_search = [];
        $this->order = [];
        $this->where = [];
        $this->add_columns = [];
        $this->edit_columns = [];
        $this->joins = [];
        $this->select = '*';
    }
}
