<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NotificationModel
 * 
 * Mengelola notifikasi real-time untuk admin dashboard.
 * Dilengkapi pembungkus try-catch dan pengecekan keberadaan tabel agar aman dari 500 DB Error.
 * Tabel: admin_notifications, admin_known_ips
 */
class NotificationModel extends CI_Model
{
    const CLEANUP_DAYS = 30;

    public function __construct()
    {
        parent::__construct();
    }

    public function get_unread_count()
    {
        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return 0;
            }
            return (int) $this->db
                ->where('is_read', 0)
                ->count_all_results('admin_notifications');
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function get_recent($limit = 10, $unread_only = false)
    {
        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return [];
            }
            if ($unread_only) {
                $this->db->where('is_read', 0);
            }

            return $this->db
                ->order_by('created_at', 'DESC')
                ->limit($limit)
                ->get('admin_notifications')
                ->result_array();
        } catch (Throwable $e) {
            return [];
        }
    }

    public function insert_notification($type, $title, $message, array $ref_data = [])
    {
        $allowed_types = ['maintenance', 'login_new_ip', 'dlq_failed'];
        if (!in_array($type, $allowed_types)) {
            log_message('error', "NotificationModel: unknown type '{$type}'");
            return false;
        }

        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return false;
            }
            return $this->db->insert('admin_notifications', [
                'type'       => $type,
                'title'      => substr($title, 0, 255),
                'message'    => $message,
                'ref_data'   => !empty($ref_data) ? json_encode($ref_data, JSON_UNESCAPED_UNICODE) : null,
                'is_read'    => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (Throwable $e) {
            return false;
        }
    }

    public function mark_as_read($id)
    {
        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return true;
            }
            return $this->db
                ->where('id', (int) $id)
                ->update('admin_notifications', ['is_read' => 1]);
        } catch (Throwable $e) {
            return true;
        }
    }

    public function mark_all_read()
    {
        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return true;
            }
            return $this->db
                ->where('is_read', 0)
                ->update('admin_notifications', ['is_read' => 1]);
        } catch (Throwable $e) {
            return true;
        }
    }

    public function cleanup_old()
    {
        try {
            if (!$this->db->table_exists('admin_notifications')) {
                return 0;
            }
            $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::CLEANUP_DAYS . ' days'));
            $this->db->where('created_at <', $cutoff)->delete('admin_notifications');
            return $this->db->affected_rows();
        } catch (Throwable $e) {
            return 0;
        }
    }

    public function is_known_ip($admin_id, $ip_address)
    {
        try {
            if (!$this->db->table_exists('admin_known_ips')) {
                return true;
            }
            $count = $this->db
                ->where('admin_id', (int) $admin_id)
                ->where('ip_address', $ip_address)
                ->count_all_results('admin_known_ips');

            return $count > 0;
        } catch (Throwable $e) {
            return true;
        }
    }

    public function register_ip($admin_id, $ip_address)
    {
        try {
            if (!$this->db->table_exists('admin_known_ips')) {
                return true;
            }
            $sql = "INSERT INTO admin_known_ips (admin_id, ip_address, first_seen, last_seen)
                    VALUES (?, ?, NOW(), NOW())
                    ON DUPLICATE KEY UPDATE last_seen = NOW()";

            return $this->db->query($sql, [(int) $admin_id, $ip_address]);
        } catch (Throwable $e) {
            return true;
        }
    }

    public function get_known_ips($admin_id)
    {
        try {
            if (!$this->db->table_exists('admin_known_ips')) {
                return [];
            }
            return $this->db
                ->where('admin_id', (int) $admin_id)
                ->order_by('last_seen', 'DESC')
                ->get('admin_known_ips')
                ->result_array();
        } catch (Throwable $e) {
            return [];
        }
    }
}
