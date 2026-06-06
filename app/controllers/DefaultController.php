<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php'); // Nhúng Model để lấy dữ liệu sản phẩm

class DefaultController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        // Khởi tạo session để đồng bộ giỏ hàng và trạng thái đăng nhập hiển thị trên header
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Kết nối cơ sở dữ liệu
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    /**
     * Hành động xử lý khi người dùng truy cập trang chủ
     */
    public function index()
    {
        // 1. Lấy danh sách toàn bộ sản phẩm từ Database thông qua Model
        $products = $this->productModel->getProducts();

        // 2. Thiết lập tiêu đề cho trang chủ
        $title = "Trang Chủ - My Store";

        // 3. --- GHÉP LAYOUT CHUẨN MVC CHO TRANG CHỦ ---
        include_once 'app/views/shares/header.php'; // Nạp thanh Navbar menu điều hướng bên trên
        include_once 'app/views/home/index.php';   // Nạp giao diện nội dung chính của trang chủ
        include_once 'app/views/shares/footer.php'; // Nạp chân trang và các thư viện JavaScript
    }
}
?>