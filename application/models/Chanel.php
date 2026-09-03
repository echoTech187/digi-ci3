<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Chanel Model
 * Manages Cashin and Cashout channel definitions, merchant channel associations, PPOB categories, and global channel migrations.
 */
class Chanel extends CI_Model
{
    var $table = 'cashin_channel_x_merchant cc';
    var $column_order = [null, 'cc.id', 'cc.c_cashinChannelGroup', 'cc.c_description', 'cc.c_externalIdDefault', 'cc.c_feeType', 'cc.c_fee', null];
    var $column_search = ['cc.id', 'cc.c_cashinChannelGroup', 'cc.c_description', 'cc.c_externalIdDefault'];
    var $order = ['cc.id' => 'asc'];
    private static $cached_total = null;

    private function _get_datatables_query($table, $column_order, $column_search, $order, $where = [])
    {
        $this->db->query("SET SESSION max_execution_time = 30000");
        if (strpos($table, 'cashin_channel_x_merchant') !== false && strpos($table, 'merchant') === false) {
            $prefix = (strpos($table, ' ') !== false) ? explode(' ', $table)[1] . '.' : '';
            $this->db->select("{$prefix}id, {$prefix}c_cashinChannelGroup, {$prefix}c_description, {$prefix}c_externalIdDefault, {$prefix}c_feeType, {$prefix}c_fee, {$prefix}c_status");
        }
        $this->db->from($table);

        if (!empty($where)) {
            $this->db->where($where);
        }

        $i = 0;
        foreach ($column_search as $item) {
            if ($_POST['search']['value']) {
                if ($i === 0) {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($column_search) - 1 == $i) {
                    $this->db->group_end();
                }
            }
            $i++;
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } elseif (isset($order)) {
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function get_datatables($table, $column_order, $column_search, $order, $where = [])
    {
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        return $this->db->get()->result();
    }

    public function count_filtered($table, $column_order, $column_search, $order, $where = [])
    {
        $is_filtered = (!empty($where) || (isset($_POST['search']['value']) && !empty($_POST['search']['value'])));
        if (!$is_filtered) {
            return $this->count_all_dt($table, $where);
        }
        $this->db->select('count(id) as total');
        $this->_get_datatables_query($table, $column_order, $column_search, $order, $where);
        $query = $this->db->get();
        return $query->row()->total;
    }

    public function count_all_dt($table, $where = [])
    {
        if (empty($where) && self::$cached_total !== null) {
            return self::$cached_total;
        }

        if (!empty($where)) {
            $this->db->select('count(id) as total')->from($table)->where($where);
            return $this->db->get()->row()->total;
        }

        $table_name = explode(' ', $table)[0];
        $q = $this->db->query("SHOW TABLE STATUS LIKE '{$table_name}'");
        $res = $q->row();
        if ($res && isset($res->Rows)) {
            self::$cached_total = (int) $res->Rows;
            return self::$cached_total;
        }

        $query = $this->db->select("count(id) as total")->from($table_name)->get();
        self::$cached_total = $query->row() ? (int) $query->row()->total : 0;
        return self::$cached_total;
    }

    public function get_pulsa_reguler($limit, $start, $provider = null)
    {
        $this->db->limit($limit, $start)->from('cashout_external_x_channel')->where('c_cashoutChannelGroup', 'ppob');
        $this->db->like('c_cashoutChannelGroup2', $provider ?: 'pulsa');
        return $this->db->get()->result();
    }

    public function get_paket_data($limit, $start, $provider = null)
    {
        $this->db->limit($limit, $start)->from('cashout_external_x_channel')->where('c_cashoutChannelGroup', 'ppob');
        $this->db->like('c_cashoutChannelGroup2', $provider ?: 'paket_data');
        return $this->db->get()->result();
    }

    public function get_token_listrik($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'token_pln'])->result();
    }

    public function get_topup_gopay($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'topup_gopay'])->result();
    }

    public function get_topup_dana($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'topup_dana'])->result();
    }

    public function get_topup_ovo($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'topup_ovo'])->result();
    }

    public function insert_cashout_chanel($data)
    {
        return $this->db->insert('cashout_external_x_channel', $data);
    }

    public function update_cashout_chanel($id, $data)
    {
        return $this->db->where('id', $id)->update('cashout_external_x_channel', $data);
    }

    public function get_mobile_legend($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'diamond_mlbb'])->result();
    }

    public function get_pubg_mobile($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'pubg_mobile'])->result();
    }

    public function get_free_fire($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'free_fire'])->result();
    }

    public function get_hago($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'hago'])->result();
    }

    public function get_google_play($limit, $start)
    {
        return $this->db->get_where('cashout_external_x_channel', ['c_cashoutChannelGroup2' => 'google_play'])->result();
    }

    public function get_cashin_chanel($limit, $start)
    {
        return $this->db->query("SELECT * FROM cashin_channel_x_merchant LIMIT ?, ?", [(int) $start, (int) $limit])->result();
    }

    public function get_cashin_chanel_group()
    {
        return $this->db->query("SELECT c_cashinChannelGroup as c_channelGroup FROM cashin_channel_x_merchant GROUP BY c_cashinChannelGroup")->result();
    }

    public function get_cashin_chanel_id()
    {
        return $this->db->query("SELECT ref_cashinChannelId as id FROM cashin_channel_x_merchant GROUP BY id")->result();
    }

    public function get_cashin_chanel_external_id_default()
    {
        return $this->db->query("SELECT c_cashinExternalId as c_externalIdDefault FROM cashin_external_x_channel GROUP BY c_cashinExternalId")->result();
    }

    public function get_cashout_chanel($limit, $start)
    {
        return $this->db->query("SELECT * FROM cashout_external_x_channel WHERE c_cashoutChannelGroup != 'ppob' LIMIT ?, ?", [(int) $start, (int) $limit])->result();
    }

    public function get_cashout_chanel_group()
    {
        return $this->db->query("SELECT c_cashoutChannelGroup as c_channelGroup FROM cashout_external_x_channel WHERE c_cashoutChannelGroup IS NOT NULL AND c_cashoutChannelGroup != '' GROUP BY c_cashoutChannelGroup")->result();
    }

    public function get_cashout_chanel_id()
    {
        return $this->db->query("SELECT ref_cashoutChannelId as id FROM cashout_external_x_channel GROUP BY id")->result();
    }

    public function get_cashout_chanel_external_id_default()
    {
        return $this->db->query("SELECT c_cashoutExternalId as c_externalIdDefault FROM cashout_external_x_channel GROUP BY c_cashoutExternalId")->result();
    }

    public function get_cashout_channels($externalId, $channelGroup)
    {
        return $this->db->select('ref_cashoutChannelId as id')->from('cashout_external_x_channel')->where('c_cashoutExternalId', $externalId)->where('c_cashoutChannelGroup', $channelGroup)->get()->result();
    }

    public function get_cashout_channels_all()
    {
        return $this->db->from('cashout_external_x_channel')->where('c_cashoutChannelGroup !=', 'ppob')->get()->result();
    }

    public function get_cashin_channels($externalId, $channelGroup)
    {
        return $this->db->select('ref_cashinChannelId as id')->from('cashin_external_x_channel')->where('c_cashinExternalId', $externalId)->where('c_cashinChannelGroup', $channelGroup)->get()->result();
    }

    public function createCashinChannel($data)
    {
        return $this->db->insert('cashin_external_x_channel', $data) ? true : $this->db->error();
    }

    public function updateCashinChannel($id, $data)
    {
        return $this->db->where('id', $id)->update('cashin_external_x_channel', $data) ? true : $this->db->error();
    }

    public function deleteCashinChannel($id)
    {
        return $this->db->where('id', $id)->delete('cashin_external_x_channel') ? true : $this->db->error();
    }

    public function createCashoutChannel($data)
    {
        return $this->db->insert('cashout_external_x_channel', $data) ? true : $this->db->error();
    }

    public function updateCashoutChannel($id, $data)
    {
        return $this->db->where('id', $id)->update('cashout_external_x_channel', $data) ? true : $this->db->error();
    }

    public function deleteCashoutChannel($id)
    {
        return $this->db->where('id', $id)->delete('cashout_external_x_channel') ? true : $this->db->error();
    }

    public function insertPaketData($data)
    {
        return $this->db->insert('cashout_external_x_channel', $data);
    }

    public function createCashinChannelXMerchant($data)
    {
        return $this->db->insert('cashin_channel_x_merchant', $data) ? true : $this->db->error();
    }

    public function updateCashinChannelXMerchant($id, $data)
    {
        return $this->db->where('id', $id)->update('cashin_channel_x_merchant', $data) ? true : $this->db->error();
    }

    public function deleteCashinChannelXMerchant($id)
    {
        return $this->db->where('id', $id)->delete('cashin_channel_x_merchant') ? true : $this->db->error();
    }

    public function createCashoutChannelXMerchant($data)
    {
        return $this->db->insert('cashout_channel_x_merchant', $data) ? true : $this->db->error();
    }

    public function bulkCreateCashoutChannelXMerchant($data)
    {
        $this->db->trans_begin();
        foreach ($data as $row) {
            if (!$this->db->insert('cashout_channel_x_merchant', $row)) {
                $err = $this->db->error();
                $this->db->trans_rollback();
                return ['code' => $err['code'], 'message' => $err['message']];
            }
        }
        $this->db->trans_commit();
        return true;
    }

    public function updateCashoutChannelXMerchant($id, $data)
    {
        return $this->db->where('id', $id)->update('cashout_channel_x_merchant', $data) ? true : $this->db->error();
    }

    public function deleteCashoutChannelXMerchant($id)
    {
        return $this->db->where('id', $id)->delete('cashout_channel_x_merchant') ? true : $this->db->error();
    }

    public function bulkCreateCashinChannelXMerchant($data)
    {
        $this->db->trans_begin();
        foreach ($data as $row) {
            if (!$this->db->insert('cashin_channel_x_merchant', $row)) {
                $err = $this->db->error();
                $this->db->trans_rollback();
                return ['code' => $err['code'], 'message' => $err['message']];
            }
        }
        $this->db->trans_commit();
        return true;
    }

    public function get_cashin_summary()
    {
        return $this->db->select("COUNT(*) as qty, COUNT(DISTINCT c_cashinChannelGroup) as total_groups, COUNT(DISTINCT c_cashinExternalId) as providers, AVG(c_fee) as avg_fee")
            ->from("cashin_external_x_channel")
            ->get()->row();
    }

    public function get_cashout_summary()
    {
        return $this->db->select("COUNT(*) as qty, COUNT(DISTINCT c_cashoutChannelGroup) as total_groups, COUNT(DISTINCT c_cashoutExternalId) as providers, AVG(c_fee) as avg_fee")
            ->from("cashout_external_x_channel")
            ->where("c_cashoutChannelGroup !=", "ppob")
            ->get()->row();
    }

    public function updateCashinChannelGlobal($data)
    {
        return $this->_updateChannelGlobal('cashin_channel_x_merchant', 'cashin_external_x_channel', 'c_cashinChannelGroup', 'ref_cashinChannelId', 'c_externalIdDefault', 'c_cashinExternalId', $data);
    }

    public function updateCashoutChannelGlobal($data)
    {
        return $this->_updateChannelGlobal('cashout_channel_x_merchant', 'cashout_external_x_channel', 'c_cashoutChannelGroup', 'ref_cashoutChannelId', 'c_externalIdDefault', 'c_cashoutExternalId', $data);
    }

    private function _updateChannelGlobal($table, $masterTable, $colGroup, $colChannel, $colExt, $colMasterExt, $data)
    {
        $updateType = $data['update_type'];
        $merchantId = $data['merchant_id'] ?? null;
        $currentGroup = $data['current_group'];
        $currentExternal = $data['current_external'] ?? null;
        $currentChannel = $data['current_channel'] ?? null;
        $currentStatus = $data['current_status'] ?? null;
        $newGroup = $data['new_group'];
        $newExternal = $data['new_external'] ?? null;
        $newChannel = $data['new_channel'] ?? null;
        $newStatus = $data['new_status'] ?? null;

        if ($updateType === 'merchant' && !empty($merchantId)) {
            is_array($merchantId) ? $this->db->where_in('ref_merchantId', $merchantId) : $this->db->where('ref_merchantId', $merchantId);
        }
        $this->db->where($colGroup, $currentGroup);
        if (!empty($currentExternal)) $this->db->where($colExt, $currentExternal);
        if (!empty($currentChannel)) $this->db->where($colChannel, $currentChannel);
        if (!empty($currentStatus)) $this->db->where('c_status', $currentStatus);

        $targetedRows = $this->db->select("ref_merchantId, {$colChannel}, {$colExt}")->from($table)->get()->result_array();
        if (empty($targetedRows)) {
            return ['code' => 400, 'message' => "Update failed: No matching configurations found."];
        }

        $masterRows = $this->db->get_where($masterTable, [$colGroup => $newGroup])->result_array();
        $validCombinations = [];
        foreach ($masterRows as $mr) {
            $validCombinations[] = $mr[$colMasterExt] . '|' . $mr[$colChannel];
        }

        foreach ($targetedRows as $row) {
            $finalExt = !empty($newExternal) ? $newExternal : $row[$colExt];
            $finalCh = !empty($newChannel) ? $newChannel : $row[$colChannel];
            if (!in_array($finalExt . '|' . $finalCh, $validCombinations)) {
                return ['code' => 400, 'message' => "Provider '{$finalExt}' does not support Channel '{$finalCh}'."];
            }
        }

        $this->db->trans_start();
        if ($updateType === 'merchant' && !empty($merchantId)) {
            is_array($merchantId) ? $this->db->where_in('ref_merchantId', $merchantId) : $this->db->where('ref_merchantId', $merchantId);
        }
        $this->db->where($colGroup, $currentGroup);
        if (!empty($currentExternal)) $this->db->where($colExt, $currentExternal);
        if (!empty($currentChannel)) $this->db->where($colChannel, $currentChannel);
        if (!empty($currentStatus)) $this->db->where('c_status', $currentStatus);

        $update = [$colGroup => $newGroup];
        if (!empty($newExternal)) $update[$colExt] = $newExternal;
        if (!empty($newChannel)) $update[$colChannel] = $newChannel;
        if (!empty($newStatus)) $update['c_status'] = $newStatus;

        $this->db->update($table, $update);
        $this->db->trans_complete();

        return ($this->db->trans_status() === false) ? $this->db->error() : true;
    }

    public function get_datatables_handler($table, $column_order, $column_search, $order, $where = [])
    {
        $this->load->library('datatables');
        $tableName = explode(' ', $table)[0];
        $alias = count(explode(' ', $table)) > 1 ? explode(' ', $table)[1] : null;
        $prefix = $alias ? $alias . '.' : '';

        $cols = '*';
        if ($tableName == 'cashout_external_x_channel') {
            $cols = "{$prefix}id, {$prefix}ref_cashoutChannelId, co.c_caption, co.c_description, {$prefix}c_fee, {$prefix}c_cashoutChannelGroup as c_channelGroup, {$prefix}c_cashoutChannelGroup2 as c_channelGroup2, {$prefix}c_cashoutExternalId as c_externalIdDefault, {$prefix}c_feeType, {$prefix}c_amountMin, {$prefix}c_amountMax";
        } elseif ($tableName == 'cashin_external_x_channel') {
            $cols = "{$prefix}id, {$prefix}ref_cashinChannelId, ci.c_description, {$prefix}c_cashinChannelGroup as c_channelGroup, {$prefix}c_cashinExternalId as c_externalIdDefault, {$prefix}c_feeType, {$prefix}c_fee, {$prefix}c_feePercetange, {$prefix}c_settlementInterval, {$prefix}c_amountMin, {$prefix}c_amountMax, {$prefix}c_status";
        }

        $dt = $this->datatables->of($table)->select($cols)->set_column_order($column_order)->set_column_search($column_search)->set_default_order($order);

        if ($tableName == 'cashout_external_x_channel') {
            $dt->join('cashout_channel co', "co.id = " . ($alias ?: 'cashout_external_x_channel') . ".ref_cashoutChannelId", 'left');
        } elseif ($tableName == 'cashin_external_x_channel') {
            $dt->join('cashin_channel ci', "ci.id = " . ($alias ?: 'cashin_external_x_channel') . ".ref_cashinChannelId", 'left');
        }

        return $dt->where($where)->addColumn('no', function ($row) {
            static $no = null;
            if ($no === null) $no = intval($this->input->post('start'));
            return ++$no;
        })->make(true);
    }

    public function getCashinExternalDataTable($custom_search = null)
    {
        $this->load->library('datatables');
        $where = [];
        foreach (['merchant_id' => 'cxm.ref_merchantId', 'channel_group' => 'cxm.c_cashinChannelGroup', 'channel_id' => 'cxm.ref_cashinChannelId', 'provider' => 'cxm.c_externalIdDefault', 'status' => 'cxm.c_status'] as $p => $c) {
            if ($this->input->post($p)) $where[$c] = $this->input->post($p);
        }

        $dt = $this->datatables->of('cashin_channel_x_merchant cxm')
            ->select('cxm.id, cxm.ref_merchantId, cxm.c_cashinChannelGroup, cxm.ref_cashinChannelId, cxm.c_externalIdDefault, cxm.c_feeType, cxm.c_fee, cxm.c_feePercetange, cxm.c_settlementInterval, cxm.c_amountMin, cxm.c_amountMax, cxm.c_status, m.c_name as merchant_name')
            ->join('merchant m', 'm.id = cxm.ref_merchantId')
            ->set_column_order([null, 'm.c_name', 'cxm.c_cashinChannelGroup', 'cxm.c_fee', 'cxm.c_status', null])
            ->set_column_search(['m.c_name', 'cxm.c_cashinChannelGroup', 'cxm.ref_cashinChannelId', 'cxm.c_externalIdDefault'])
            ->set_default_order(['cxm.id' => 'desc'])
            ->where($where);

        return $dt->addColumn('no', function ($row) {
            static $no = null;
            if ($no === null) $no = intval($this->input->post('start'));
            return ++$no;
        })->make(true);
    }

    public function getCashoutExternalDataTable($custom_search = null)
    {
        $this->load->library('datatables');
        $where = [];
        foreach (['merchant_id' => 'cxm.ref_merchantId', 'channel_group' => 'cxm.c_cashoutChannelGroup', 'channel_id' => 'cxm.ref_cashoutChannelId', 'provider' => 'cxm.c_externalIdDefault', 'status' => 'cxm.c_status'] as $p => $c) {
            if ($this->input->post($p)) $where[$c] = $this->input->post($p);
        }

        $dt = $this->datatables->of('cashout_channel_x_merchant cxm')
            ->select('cxm.id, cxm.ref_merchantId, cxm.c_cashoutChannelGroup, cxm.ref_cashoutChannelId, cxm.c_externalIdDefault, cxm.c_feeType, cxm.c_fee, cxm.c_feePercetange, cxm.c_amountMin, cxm.c_amountMax, cxm.c_status, m.c_name as merchant_name')
            ->join('merchant m', 'm.id = cxm.ref_merchantId')
            ->set_column_order([null, 'm.c_name', 'cxm.c_cashoutChannelGroup', 'cxm.c_fee', 'cxm.c_status', null])
            ->set_column_search(['m.c_name', 'cxm.c_cashoutChannelGroup', 'cxm.ref_cashoutChannelId', 'cxm.c_externalIdDefault'])
            ->set_default_order(['cxm.id' => 'desc'])
            ->where($where);

        return $dt->addColumn('no', function ($row) {
            static $no = null;
            if ($no === null) $no = intval($this->input->post('start'));
            return ++$no;
        })->make(true);
    }
}