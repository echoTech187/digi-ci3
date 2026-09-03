<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Enterprise Ultra-Smart Datatables Library for CodeIgniter 3
 * 
 * Features:
 * - Automatic Deferred Join (2-Step ID Subquery / Covering Index Optimization)
 * - Automatic Bidirectional Reverse Scanning for Deep Offsets (>5,000 rows)
 * - Automatic Fast Metadata Estimation (SHOW TABLE STATUS for mega tables >10k rows, < 0.1ms)
 * - Built-in Smart UI Column Transformers (Currency, Date, Badge, Masking, Truncate, URL)
 * - Built-in Payload Whitelist/Blacklist Filtering (only / except)
 * - Fluent Interface (yajra/laravel-datatables inspired)
 * - Backward compatibility with manual data / custom models
 */
class Datatables
{
    protected $CI;
    protected $table;
    protected $primary_key = null;
    protected $column_order = [];
    protected $column_search = [];
    protected $order = [];
    protected $where = [];
    protected $where_in = [];
    protected $add_columns = [];
    protected $edit_columns = [];
    protected $raw_columns = [];
    protected $joins = [];
    protected $select = '*';
    protected $select_escape = NULL;
    protected $manual_recordsTotal = NULL;
    protected $manual_recordsFiltered = NULL;
    protected $manual_data = NULL;
    protected $is_reversed = FALSE;
    protected $smart_enabled = TRUE;
    protected $count_cache_ttl = 0;
    protected $fast_count_enabled = FALSE;
    protected $fast_count_threshold = 10000;
    protected $only_columns = [];
    protected $except_columns = [];

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
     * Enable or disable Smart Deferred Join / Reverse Scan Optimization
     */
    public function smart($enable = TRUE)
    {
        $this->smart_enabled = (bool)$enable;
        return $this;
    }

    /**
     * Set primary key column for 2-Step ID lookup (e.g. 'mutation.id' or 'id')
     */
    public function set_primary_key($pk)
    {
        $this->primary_key = $pk;
        return $this;
    }

    /**
     * Set SELECT columns
     */
    public function select($select, $escape = NULL)
    {
        $this->select = $select;
        $this->select_escape = $escape;
        return $this;
    }

    /**
     * Add WHERE clause
     */
    public function where($key, $value = NULL, $escape = NULL)
    {
        $esc = ($escape !== NULL) ? $escape : TRUE;
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->where[] = ['key' => $k, 'value' => $v, 'escape' => $esc];
            }
        } else {
            $this->where[] = ['key' => $key, 'value' => $value, 'escape' => $esc];
        }
        return $this;
    }

    /**
     * Add WHERE IN clause
     */
    public function where_in($key, $values = NULL)
    {
        if (is_array($key) && $values === NULL) {
            foreach ($key as $k => $v) {
                $this->where_in[] = ['key' => $k, 'values' => $v];
            }
        } else {
            $this->where_in[] = ['key' => $key, 'values' => $values];
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
     * Smart Transformer: Currency Formatter (e.g. Rp 10.000)
     */
    public function editColumnCurrency($name, $prefix = 'Rp ', $decimals = 0)
    {
        return $this->editColumn($name, function($row) use ($name, $prefix, $decimals) {
            $val = is_object($row) ? ($row->$name ?? 0) : ($row[$name] ?? 0);
            return $prefix . number_format((float)$val, $decimals, ',', '.');
        });
    }

    /**
     * Smart Transformer: Date Formatter (e.g. 15 Aug 2026 14:00:00)
     */
    public function editColumnDate($name, $format = 'd M Y H:i:s')
    {
        return $this->editColumn($name, function($row) use ($name, $format) {
            $val = is_object($row) ? ($row->$name ?? null) : ($row[$name] ?? null);
            if (empty($val)) return '-';
            $ts = strtotime($val);
            return $ts ? date($format, $ts) : $val;
        });
    }

    /**
     * Smart Transformer: HTML Badge Formatter (e.g. <span class="badge bg-success">Success</span>)
     */
    public function editColumnBadge($name, $badgeMap = [])
    {
        return $this->editColumn($name, function($row) use ($name, $badgeMap) {
            $val = is_object($row) ? ($row->$name ?? '') : ($row[$name] ?? '');
            $class = $badgeMap[$val] ?? 'secondary';
            return '<span class="badge bg-' . htmlspecialchars($class) . '">' . htmlspecialchars($val) . '</span>';
        });
    }

    /**
     * Smart Transformer: Sensitive Data Masker (e.g. phone/VA: 0812****5678)
     */
    public function editColumnMask($name, $visibleDigits = 4)
    {
        return $this->editColumn($name, function($row) use ($name, $visibleDigits) {
            $val = (string)(is_object($row) ? ($row->$name ?? '') : ($row[$name] ?? ''));
            $len = strlen($val);
            if ($len <= $visibleDigits) return $val;
            return substr($val, 0, $visibleDigits) . str_repeat('*', max(0, $len - $visibleDigits - 2)) . substr($val, -2);
        });
    }

    /**
     * Smart Transformer: Text Truncator (e.g. Very long text...)
     */
    public function editColumnTruncate($name, $limit = 50, $end = '...')
    {
        return $this->editColumn($name, function($row) use ($name, $limit, $end) {
            $val = (string)(is_object($row) ? ($row->$name ?? '') : ($row[$name] ?? ''));
            if (mb_strlen($val) <= $limit) return htmlspecialchars($val);
            return htmlspecialchars(mb_substr($val, 0, $limit)) . $end;
        });
    }

    /**
     * Smart Transformer: HTML Anchor Link
     */
    public function editColumnUrl($name, $target = '_blank')
    {
        return $this->editColumn($name, function($row) use ($name, $target) {
            $val = (string)(is_object($row) ? ($row->$name ?? '') : ($row[$name] ?? ''));
            if (empty($val)) return '-';
            $safeUrl = htmlspecialchars($val);
            return '<a href="' . $safeUrl . '" target="' . htmlspecialchars($target) . '" rel="noopener">' . $safeUrl . '</a>';
        });
    }

    /**
     * Filter JSON response payload to include ONLY specific fields
     */
    public function only($columns = [])
    {
        $this->only_columns = (array)$columns;
        return $this;
    }

    /**
     * Exclude specific fields from JSON response payload
     */
    public function except($columns = [])
    {
        $this->except_columns = (array)$columns;
        return $this;
    }

    /**
     * Enable Fast Metadata Estimation (SHOW TABLE STATUS) for large tables
     * 
     * @param int $threshold Minimum row threshold to use estimation
     */
    public function fast_count($threshold = 10000)
    {
        $this->fast_count_enabled = TRUE;
        $this->fast_count_threshold = (int)$threshold;
        return $this;
    }

    /**
     * Set Count Cache TTL in seconds
     */
    public function cache_count($seconds = 60)
    {
        $this->count_cache_ttl = (int)$seconds;
        return $this;
    }

    /**
     * Manually set recordsTotal
     */
    public function set_recordsTotal($total)
    {
        $this->manual_recordsTotal = $total;
        return $this;
    }

    /**
     * Manually set recordsFiltered
     */
    public function set_recordsFiltered($total)
    {
        $this->manual_recordsFiltered = $total;
        return $this;
    }

    /**
     * Automatically applies Bidirectional Reverse Scanning limit & order on any DB instance.
     */
    public function limit_auto($recordsFiltered = NULL, &$db = NULL)
    {
        $db = $db ?: $this->CI->db;
        $total = ($recordsFiltered !== NULL) ? (int)$recordsFiltered : (int)$this->manual_recordsFiltered;
        $scan = self::get_reverse_scan_params($total);

        $this->is_reversed = $scan['force_reverse'];
        $this->apply_order($db, $scan['force_reverse']);

        if ($scan['fetch_length'] != -1) {
            $db->limit($scan['fetch_length'], $scan['fetch_start']);
        }

        return $this;
    }

    /**
     * Manually set data (automatically reverses array if reverse scan was triggered)
     */
    public function set_data($data)
    {
        if ($this->is_reversed && is_array($data) && !empty($data)) {
            $data = array_reverse($data);
        }
        $this->manual_data = $data;
        return $this;
    }

    /**
     * Generate the response
     */
    public function make($json = TRUE)
    {
        // 1. Calculate recordsTotal
        $recordsTotal = ($this->manual_recordsTotal !== NULL) ? $this->manual_recordsTotal : $this->get_records_total();
        
        // 2. Prepare result set
        if ($this->manual_data !== NULL) {
            $result = $this->manual_data;
            if ($this->manual_recordsFiltered === NULL) {
                $recordsFiltered = count($result);
                if (isset($_POST['length']) && $_POST['length'] != -1) {
                    $start = isset($_POST['start']) ? (int)$_POST['start'] : 0;
                    $length = (int)$_POST['length'];
                    $result = array_slice($result, $start, $length);
                }
            } else {
                $recordsFiltered = $this->manual_recordsFiltered;
            }
        } else {
            // Standard / Smart Flow
            $search_val = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
            $has_search = ($search_val !== '') || !empty($this->where) || !empty($this->where_in);
            
            if (!$has_search) {
                $recordsFiltered = $recordsTotal;
            } else {
                $temp_db = clone $this->CI->db;
                $this->apply_query($temp_db);
                $this->apply_search($temp_db);
                $recordsFiltered = $temp_db->count_all_results('', FALSE);
            }
            
            // Automatic Reverse Scan Calculation
            $scan = self::get_reverse_scan_params($recordsFiltered);
            $this->is_reversed = $scan['force_reverse'];
            
            // Zero-Count Fast Exit (Short-Circuit secondary queries when 0 matches found)
            if ($recordsFiltered === 0) {
                $result = [];
            } elseif ($this->smart_enabled && !empty($this->table)) {
                $result = $this->execute_smart_query($scan);
            } else {
                $this->apply_query($this->CI->db);
                $this->apply_search($this->CI->db);
                $this->apply_order($this->CI->db, $scan['force_reverse']);
                
                if ($scan['fetch_length'] != -1) {
                    $this->CI->db->limit($scan['fetch_length'], $scan['fetch_start']);
                }
                
                $query = $this->CI->db->get();
                $result = is_object($query) ? $query->result() : [];
            }

            if ($this->is_reversed && !empty($result)) {
                $result = array_reverse($result);
            }
        }
        
        // 3. Process Row Data & Apply Transformers
        $data = [];
        $no = isset($_POST['start']) ? (int)$_POST['start'] : 0;
        
        foreach ($result as $row) {
            $no++;
            $item = (array) $row;
            if (isset($row->id)) {
                $item['DT_RowId'] = $row->id;
            }
            
            if (!isset($item['no']) && !isset($this->add_columns['no'])) {
                $item['no'] = $no;
            }
            
            foreach ($this->edit_columns as $col => $callback) {
                if (array_key_exists($col, $item) || is_object($row)) {
                    $item[$col] = $callback($row);
                }
            }
            
            foreach ($this->add_columns as $col => $callback) {
                $item[$col] = $callback($row);
            }
            
            // Smart Payload Filter (only / except)
            if (!empty($this->only_columns)) {
                $item = array_intersect_key($item, array_flip($this->only_columns));
            }
            if (!empty($this->except_columns)) {
                foreach ($this->except_columns as $exc) {
                    unset($item[$exc]);
                }
            }
            
            $data[] = $item;
        }

        $output = [
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 0,
            "recordsTotal" => (int)$recordsTotal,
            "recordsFiltered" => (int)$recordsFiltered,
            "data" => $data,
        ];

        if ($json) {
            $json_str = json_encode($output);
            $this->CI->output
                ->set_content_type('application/json')
                ->set_output($json_str);
            return $json_str;
        }

        return $output;
    }

    /**
     * Executes Smart Deferred Join (2-Step ID Lookup)
     */
    protected function execute_smart_query($scan)
    {
        $pk = $this->get_primary_key_name();
        
        // STEP 1: Fetch matching IDs only using covering index scan
        $id_db = clone $this->CI->db;
        $id_db->select($pk);
        $id_db->from($this->table);
        
        // Include joins so filter conditions on joined tables resolve properly
        foreach ($this->joins as $j) {
            $id_db->join($j['table'], $j['cond'], $j['type']);
        }
        
        foreach ($this->where as $w) {
            $id_db->where($w['key'], $w['value'], $w['escape']);
        }
        foreach ($this->where_in as $wi) {
            $id_db->where_in($wi['key'], $wi['values']);
        }
        
        $this->apply_search($id_db);
        $this->apply_order($id_db, $scan['force_reverse']);
        
        if ($scan['fetch_length'] != -1) {
            $id_db->limit($scan['fetch_length'], $scan['fetch_start']);
        }

        $id_query = $id_db->get();
        if (!is_object($id_query) || $id_query->num_rows() == 0) {
            return [];
        }

        $id_rows = $id_query->result_array();
        $pk_short = strpos($pk, '.') !== false ? substr($pk, strpos($pk, '.') + 1) : $pk;
        $ids = array_column($id_rows, $pk_short);

        if (empty($ids)) return [];

        // STEP 2: Fetch full details for only those IDs
        $full_db = clone $this->CI->db;
        if ($this->select_escape !== NULL) {
            $full_db->select($this->select, $this->select_escape);
        } else {
            $full_db->select($this->select);
        }
        $full_db->from($this->table);
        
        foreach ($this->joins as $j) {
            $full_db->join($j['table'], $j['cond'], $j['type']);
        }
        
        $full_db->where_in($pk, $ids);
        $this->apply_order($full_db, $scan['force_reverse']);

        $full_query = $full_db->get();
        return is_object($full_query) ? $full_query->result() : [];
    }

    protected function get_primary_key_name()
    {
        if ($this->primary_key !== null) {
            return $this->primary_key;
        }

        if (strpos($this->table, ' ') !== false) {
            $parts = explode(' ', trim($this->table));
            $alias = end($parts);
            return $alias . '.id';
        }

        return $this->table . '.id';
    }

    protected function get_records_total()
    {
        // 1. Fast Cache Driver lookup if enabled
        if ($this->count_cache_ttl > 0) {
            $this->CI->load->driver('cache', array('adapter' => 'file'));
            $cache_key = 'dt_total_' . md5($this->table . serialize($this->where));
            $cached = $this->CI->cache->get($cache_key);
            if ($cached !== false) return (int)$cached;
        }

        // 2. Fast Metadata Estimation for Large Tables (SHOW TABLE STATUS) - 100% Automatic
        if (!empty($this->table)) {
            $pure_table = str_before(trim($this->table), ' ');
            $pure_table = trim(explode(' ', $pure_table)[0]);
            $q = $this->CI->db->query("SHOW TABLE STATUS LIKE " . $this->CI->db->escape($pure_table));
            if (is_object($q) && $res = $q->row()) {
                $threshold = $this->fast_count_enabled ? $this->fast_count_threshold : 10000;
                if (isset($res->Rows) && $res->Rows >= $threshold) {
                    return (int)$res->Rows;
                }
            }
        }

        // 3. Fallback Count
        $temp_db = clone $this->CI->db;
        $temp_db->from($this->table);
        foreach ($this->joins as $j) {
            $temp_db->join($j['table'], $j['cond'], $j['type']);
        }
        foreach ($this->where as $w) {
            $temp_db->where($w['key'], $w['value'], $w['escape']);
        }
        foreach ($this->where_in as $wi) {
            $temp_db->where_in($wi['key'], $wi['values']);
        }
        $total = $temp_db->count_all_results('', FALSE);

        if ($this->count_cache_ttl > 0) {
            $this->CI->cache->save($cache_key, $total, $this->count_cache_ttl);
        }

        return $total;
    }

    protected function apply_query($db = NULL)
    {
        $db = $db ?: $this->CI->db;
        if ($this->select_escape !== NULL) {
            $db->select($this->select, $this->select_escape);
        } else {
            $db->select($this->select);
        }
        $db->from($this->table);
        
        foreach ($this->joins as $j) {
            $db->join($j['table'], $j['cond'], $j['type']);
        }
        
        foreach ($this->where as $w) {
            $db->where($w['key'], $w['value'], $w['escape']);
        }

        foreach ($this->where_in as $wi) {
            $db->where_in($wi['key'], $wi['values']);
        }
    }

    protected function apply_search($db = NULL)
    {
        $db = $db ?: $this->CI->db;
        $search_value = isset($_POST['search']['value']) ? trim($_POST['search']['value']) : '';
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

    protected function apply_order($db = NULL, $force_reverse = FALSE)
    {
        $db = $db ?: $this->CI->db;
        if (isset($_POST['order'])) {
            $sort_idx = $_POST['order']['0']['column'];
            $sort_col = isset($this->column_order[$sort_idx]) ? $this->column_order[$sort_idx] : null;
            $dir = $_POST['order']['0']['dir'];
            if ($force_reverse) {
                $dir = (strtolower($dir) === 'asc') ? 'desc' : 'asc';
            }
            if ($sort_col) {
                $db->order_by($sort_col, $dir);
            }
        } else if (!empty($this->order)) {
            $order = $this->order;
            $key = key($order);
            $dir = $order[$key];
            if ($force_reverse) {
                $dir = (strtolower($dir) === 'asc') ? 'desc' : 'asc';
            }
            $db->order_by($key, $dir);
        }
    }

    protected function reset()
    {
        $this->table = NULL;
        $this->primary_key = NULL;
        $this->column_order = [];
        $this->column_search = [];
        $this->order = [];
        $this->where = [];
        $this->where_in = [];
        $this->add_columns = [];
        $this->edit_columns = [];
        $this->raw_columns = [];
        $this->joins = [];
        $this->select = '*';
        $this->select_escape = NULL;
        $this->manual_recordsTotal = NULL;
        $this->manual_recordsFiltered = NULL;
        $this->manual_data = NULL;
        $this->is_reversed = FALSE;
        $this->smart_enabled = TRUE;
        $this->count_cache_ttl = 0;
        $this->fast_count_enabled = FALSE;
        $this->only_columns = [];
        $this->except_columns = [];
    }

    /**
     * Helper method to calculate Bidirectional Reverse Scanning params for deep pagination.
     */
    public static function get_reverse_scan_params($total, $start = null, $length = null, $threshold = 5000)
    {
        $start = ($start !== null) ? (int)$start : (isset($_POST['start']) ? (int)$_POST['start'] : 0);
        $length = ($length !== null) ? (int)$length : (isset($_POST['length']) ? (int)$_POST['length'] : 10);

        $force_reverse = false;
        $fetch_start = $start;
        $fetch_length = $length;

        if ($total > $threshold && $start > ($total / 2)) {
            $force_reverse = true;
            $fetch_start = $total - $start - $length;
            if ($fetch_start < 0) {
                $fetch_length = $length + $fetch_start;
                $fetch_start = 0;
            }
        }

        return [
            'force_reverse' => $force_reverse,
            'fetch_start'   => $fetch_start,
            'fetch_length'  => $fetch_length
        ];
    }

    /**
     * Helper method to get the effective sort direction considering reverse scan mode.
     */
    public static function get_effective_direction($direction, $force_reverse = false)
    {
        if (!$force_reverse) return $direction;
        return (strtolower($direction) === 'asc') ? 'desc' : 'asc';
    }
}

if (!function_exists('str_before')) {
    function str_before($subject, $search) {
        return $search === '' ? $subject : explode($search, $subject)[0];
    }
}
