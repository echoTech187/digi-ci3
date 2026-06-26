<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NotificationModel
 * 
 * Mengelola notifikasi real-time untuk admin dashboard.
 * Tabel: admin_notifications, admin_known_ips
 */
class NotificationModel extends CI_Model
{
    const CLEANUP_DAYS = 30;

    public function __construct()
    {
        parent::__construct();
    }

    // ─────────────────────────────────────────────────────────────
    // READ
    // ─────────────────────────────────────────────────────────────

    /**
     * Hitung jumlah notifikasi yang belum dibaca.
     * Digunakan oleh badge merah di bell icon (polling setiap 30 detik).
     * Query: idx_unread_recent (is_read, created_at)
     */
    public function get_unread_count()
    {
        return (int) $this->db
            ->where('is_read', 0)
            ->count_all_results('admin_notifications');
    }

    /**
     * Ambil notifikasi terbaru (untuk dropdown bell icon).
     * Query: idx_unread_recent → ORDER BY created_at DESC LIMIT N
     * 
     * @param int $limit Jumlah notifikasi yang diambil
     * @param bool $unread_only Jika true, hanya ambil yang belum dibaca
     */
    public function get_recent($limit = 10, $unread_only = false)
    {
        if ($unread_only) {
            $this->db->where('is_read', 0);
        }

        return $this->db
            ->order_by('created_at', 'DESC')
            ->limit($limit)
            ->get('admin_notifications')
            ->result_array();
    }

    // ─────────────────────────────────────────────────────────────
    // WRITE
    // ─────────────────────────────────────────────────────────────

    /**
     * Tambah notifikasi baru.
     * 
     * @param string $type     'maintenance' | 'login_new_ip' | 'dlq_failed'
     * @param string $title    Judul singkat
     * @param string $message  Pesan lengkap
     * @param array  $ref_data Data konteks tambahan (akan di-encode sebagai JSON)
     * @return bool
     */
    public function insert_notification($type, $title, $message, array $ref_data = [])
    {
        $allowed_types = ['maintenance', 'login_new_ip', 'dlq_failed'];
        if (!in_array($type, $allowed_types)) {
            log_message('error', "NotificationModel: unknown type '{$type}'");
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
    }

    /**
     * Tandai satu notifikasi sebagai sudah dibaca.
     * 
     * @param int $id
     * @return bool
     */
    public function mark_as_read($id)
    {
        return $this->db
            ->where('id', (int) $id)
            ->update('admin_notifications', ['is_read' => 1]);
    }

    /**
     * Tandai SEMUA notifikasi sebagai sudah dibaca.
     * 
     * @return bool
     */
    public function mark_all_read()
    {
        return $this->db
            ->where('is_read', 0)
            ->update('admin_notifications', ['is_read' => 1]);
    }

    /**
     * Hapus notifikasi lama (lebih dari CLEANUP_DAYS hari).
     * Digunakan oleh cleanup otomatis di NotificationController.
     * Query: idx_created_at
     * 
     * @return int Jumlah baris yang dihapus
     */
    public function cleanup_old()
    {
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . self::CLEANUP_DAYS . ' days'));
        $this->db->where('created_at <', $cutoff)->delete('admin_notifications');
        return $this->db->affected_rows();
    }

    // ─────────────────────────────────────────────────────────────
    // IP TRACKING
    // ─────────────────────────────────────────────────────────────

    /**
     * Cek apakah IP address sudah pernah digunakan oleh admin ini.
     * Query: uq_admin_ip (admin_id, ip_address)
     * 
     * @param int    $admin_id
     * @param string $ip_address
     * @return bool  true = sudah dikenal, false = IP baru
     */
    public function is_known_ip($admin_id, $ip_address)
    {
        $count = $this->db
            ->where('admin_id', (int) $admin_id)
            ->where('ip_address', $ip_address)
            ->count_all_results('admin_known_ips');

        return $count > 0;
    }

    /**
     * Daftarkan IP address baru untuk admin ini.
     * Jika sudah ada (UNIQUE), update last_seen saja (INSERT ... ON DUPLICATE KEY UPDATE).
     * 
     * @param int    $admin_id
     * @param string $ip_address
     * @return bool
     */
    public function register_ip($admin_id, $ip_address)
    {
        $sql = "INSERT INTO admin_known_ips (admin_id, ip_address, first_seen, last_seen)
                VALUES (?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE last_seen = NOW()";

        return $this->db->query($sql, [(int) $admin_id, $ip_address]);
    }

    /**
     * Ambil semua IP yang dikenal untuk satu admin.
     * Query: idx_admin_id
     * 
     * @param int $admin_id
     * @return array
     */
    public function get_known_ips($admin_id)
    {
        return $this->db
            ->where('admin_id', (int) $admin_id)
            ->order_by('last_seen', 'DESC')
            ->get('admin_known_ips')
            ->result_array();
    }
}
