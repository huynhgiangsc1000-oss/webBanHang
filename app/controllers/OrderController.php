<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');

class OrderController
{
    private $orderModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
    }

    // --- BẢO MẬT: Kiểm tra Admin (Chỉ gọi khi cần) ---
    private function checkAdmin() {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            echo "<script>alert('Bạn không có quyền truy cập vùng quản trị!'); window.location.href='/HuynhVanGiang-4733/Account/login';</script>";
            exit();
        }
    }

    // --- CÁC HÀM CỦA ADMIN ---
    public function index() {
        $this->checkAdmin();
        $totalRevenue = $this->orderModel->getTotalRevenue();
        $totalOrders  = $this->orderModel->getTotalSuccessfulOrders();
        $totalUsers   = $this->orderModel->getTotalUsers();
        $chartData    = $this->orderModel->getRevenueByDay();

        $title = "Báo Cáo Thống Kê";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/order/dashboard.php';
        include_once 'app/views/shares/footer.php';
    }

    public function list() {
        $this->checkAdmin();
        $orders = $this->orderModel->getAllOrders();
        $title = "Quản Lý Đơn Hàng";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/order/list.php';
        include_once 'app/views/shares/footer.php';
    }

    public function updateStatus() {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = $_POST['order_id'] ?? '';
            $status   = $_POST['status'] ?? '';
            if (!empty($order_id) && !empty($status)) {
                $this->orderModel->updateStatus($order_id, $status);
            }
        }
        header('Location: /HuynhVanGiang-4733/Order/list');
        exit();
    }

    // --- CÁC HÀM CỦA KHÁCH HÀNG ---
    public function myOrders() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /HuynhVanGiang-4733/Account/login');
            exit();
        }
        $orders = $this->orderModel->getOrdersByUserId($_SESSION['user_id']);
        $title = "Đơn hàng của tôi";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/customer/my_orders.php';
        include_once 'app/views/shares/footer.php';
    }

    public function cancelOrder($order_id) {
        if (!isset($_SESSION['user_id'])) exit();
        $order = $this->orderModel->getOrderById($order_id);
        if ($order && $order->user_id == $_SESSION['user_id'] && $order->status === 'Chờ xử lý') {
            $this->orderModel->updateStatus($order_id, 'Đã hủy');
            $this->orderModel->restoreStock($order_id);
        }
        header('Location: /HuynhVanGiang-4733/Order/myOrders');
    }

    public function requestReturn($order_id) {
        if (!isset($_SESSION['user_id'])) exit();
        $order = $this->orderModel->getOrderById($order_id);
        if ($order && $order->user_id == $_SESSION['user_id'] && $order->status === 'Đã giao') {
            $this->orderModel->updateStatus($order_id, 'Yêu cầu trả hàng');
        }
        header('Location: /HuynhVanGiang-4733/Order/myOrders');
    }
}