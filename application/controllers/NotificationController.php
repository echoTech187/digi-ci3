<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * NotificationController
 * 
 * Menyediakan endpoint AJAX untuk sistem notifikasi real-time.
 * Semua endpoint hanya menerima AJAX request (is_ajax_request).
 * 
 * Routes:
 *   GET  notifications/count      → jumlah unread (polling badge)
 *   GET  notifications/list       → list notifikasi terbaru
 *   POST notifications/read/:id   → tandai 1 notif sudah dibaca
 *   POST notifications/read-all   → tandai semua sudah dibaca
 */
class NotificationController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('NotificationModel');
        is_logged_in();
    }

    // ─────────────────────────────────────────────────────────────
    // GET: Jumlah notifikasi belum dibaca (dipanggil polling setiap 30 detik)
    // ─────────────────────────────────────────────────────────────
    public function count()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        session_write_close(); // non-blocking

        $count = $this->NotificationModel->get_unread_count();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['unread' => $count]));
    }

    // ─────────────────────────────────────────────────────────────
    // GET: List notifikasi terbaru untuk dropdown bell
    // ─────────────────────────────────────────────────────────────
    public function list()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }

        session_write_close();

        $notifications = $this->NotificationModel->get_recent(15);

        // Format data untuk frontend
        $formatted = array_map(function ($n) {
            $ref = !empty($n['ref_data']) ? json_decode($n['ref_data'], true) : [];
            return [
                'id'         => (int) $n['id'],
                'type'       => $n['type'],
                'title'      => htmlspecialchars($n['title']),
                'message'    => htmlspecialchars($n['message']),
                'is_read'    => (bool) $n['is_read'],
                'time_ago'   => $this->_time_ago($n['created_at']),
                'created_at' => $n['created_at'],
                'icon'       => $this->_get_icon($n['type']),
                'color'      => $this->_get_color($n['type']),
                'ref_data'   => $ref,
            ];
        }, $notifications);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'unread' => $this->NotificationModel->get_unread_count(),
                'items'  => $formatted,
            ]));
    }

    // ─────────────────────────────────────────────────────────────
    // POST: Tandai satu notifikasi sudah dibaca
    // ─────────────────────────────────────────────────────────────
    public function read($id = null)
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
            return;
        }

        $id = (int) ($id ?: $this->input->post('id'));
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID required']);
            return;
        }

        $ok = $this->NotificationModel->mark_as_read($id);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => (bool) $ok,
                'unread'  => $this->NotificationModel->get_unread_count(),
            ]));
    }

    // ─────────────────────────────────────────────────────────────
    // POST: Tandai semua notifikasi sudah dibaca
    // ─────────────────────────────────────────────────────────────
    public function read_all()
    {
        if (!$this->input->is_ajax_request() || $this->input->method() !== 'post') {
            show_404();
            return;
        }

        $this->NotificationModel->mark_all_read();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => true, 'unread' => 0]));
    }

    // ─────────────────────────────────────────────────────────────
    // POST: Terima notifikasi dari consumer_dlq_monitor.py (Python)
    // Endpoint: POST notifications/push-dlq
    // Header wajib: X-DLQ-Monitor: 1
    // ─────────────────────────────────────────────────────────────
    public function push_dlq()
    {
        // Hanya menerima POST
        if ($this->input->method() !== 'post') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
            return;
        }

        // Validasi header marker dari DLQ monitor
        $marker = $this->input->get_request_header('X-DLQ-Monitor');
        if ($marker !== '1') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            return;
        }

        // Parse JSON body
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);

        if (empty($data) || !is_array($data)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid JSON body']);
            return;
        }

        $type     = $data['type']    ?? 'dlq_failed';
        $title    = $data['title']   ?? 'Transaksi Gagal';
        $message  = $data['message'] ?? '-';
        $ref_data = $data['ref_data'] ?? [];

        // Hanya izinkan type dlq_failed dari endpoint ini
        if ($type !== 'dlq_failed') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid type']);
            return;
        }

        $ok = $this->NotificationModel->insert_notification(
            'dlq_failed',
            substr($title, 0, 255),
            $message,
            is_array($ref_data) ? $ref_data : []
        );

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['success' => (bool) $ok]));
    }

    // ─────────────────────────────────────────────────────────────
    // HELPERS PRIVATE
    // ─────────────────────────────────────────────────────────────

    private function _time_ago($datetime)
    {
        $now  = new DateTime();
        $then = new DateTime($datetime);
        $diff = $now->diff($then);

        if ($diff->days > 0)      return $diff->days . ' hari lalu';
        if ($diff->h > 0)         return $diff->h . ' jam lalu';
        if ($diff->i > 0)         return $diff->i . ' menit lalu';
        return 'Baru saja';
    }

    private function _get_icon($type)
    {
        $icons = [
            'maintenance'  => 'fas fa-network-wired',
            'login_new_ip' => 'fas fa-map-marker-alt',
            'dlq_failed'   => 'fas fa-exclamation-triangle',
        ];
        return $icons[$type] ?? 'fas fa-bell';
    }

    private function _get_color($type)
    {
        $colors = [
            'maintenance'  => 'info',
            'login_new_ip' => 'warning',
            'dlq_failed'   => 'danger',
        ];
        return $colors[$type] ?? 'secondary';
    }
}
