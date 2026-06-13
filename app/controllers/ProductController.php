<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        // Khởi động session nếu chưa có để đọc thông tin phân quyền
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    /**
     * Hàm kiểm tra quyền Admin để bảo vệ các chức năng CRUD
     */
    private function checkAdmin()
    {
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            // Nếu không phải admin, đá về trang chủ Product và dừng script
            header('Location: /HuynhVanGiang-4733/Product');
            exit();
        }
    }

    /**
     * TRANG DANH SÁCH / TRANG CHỦ (PHÂN QUYỀN HIỂN THỊ)
     */
    public function index() {
        $category_id = $_GET['category_id'] ?? null;
        $keyword = $_GET['keyword'] ?? null;
        
        // Lấy danh sách sản phẩm
        $products = $this->productModel->getProductsByCategory($category_id, $keyword);
        $categories = (new CategoryModel($this->db))->getCategories();
        
        // Kiểm tra quyền Admin
        $isAdmin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
        
        include_once 'app/views/shares/header.php';
        // Nếu là Admin thì hiện list quản lý, không thì hiện trang chủ
        if ($isAdmin) {
            include 'app/views/product/list.php'; 
        } else {
            include 'app/views/home/index.php';
        }
        include_once 'app/views/shares/footer.php';
    }
    /**
     * THÊM SẢN PHẨM (CHỈ ADMIN)
     */
    public function add()
    {
        $this->checkAdmin(); // Chặn user thường
        $categories = (new CategoryModel($this->db))->getCategories();
        
        $title = "Thêm Sản Phẩm Mới";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/product/add.php';
        include_once 'app/views/shares/footer.php';
    }

    /**
     * LƯU SẢN PHẨM (CHỈ ADMIN)
     */
    public function save()
    {
        $this->checkAdmin(); // Chặn user thường
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            
            $result = $this->productModel->addProduct($name, $description, $price, $category_id);

            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                
                // Trường hợp form lỗi dữ liệu đầu vào: Nạp lại giao diện kèm thông báo lỗi
                $title = "Thêm Sản Phẩm Mới";
                include_once 'app/views/shares/header.php';
                include 'app/views/product/add.php';
                include_once 'app/views/shares/footer.php';
            } else {
                header('Location: /HuynhVanGiang-4733/Product');
                exit();
            }
        }
    }

    /**
     * SỬA SẢN PHẨM (CHỈ ADMIN)
     */
    public function edit($id)
    {
        $this->checkAdmin();
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories(); // Dòng này là bắt buộc
        
        include_once 'app/views/shares/header.php';
        include 'app/views/product/edit.php'; // Đảm bảo đường dẫn đúng
        include_once 'app/views/shares/footer.php';
    }

    /**
     * CẬP NHẬT SẢN PHẨM (CHỈ ADMIN)
     */
    public function update()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id']; // Phải lấy từ form
            $status = $_POST['status'];
    
            // Gọi model cập nhật
            $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $status);
    
            if ($edit) {
                header('Location: /HuynhVanGiang-4733/Product');
                exit();
            } else {
                die("Có lỗi xảy ra trong quá trình cập nhật database.");
            }
        }
    }
   public function delete($id) {
    $this->checkAdmin(); 
    
    // Kiểm tra trong bảng order_details (đã có hàm isProductPurchased bạn viết sẵn)
    if ($this->productModel->isProductPurchased($id)) {
        // Trả về thông báo không thể xóa
        echo "<script>
                alert('Không thể xóa! Sản phẩm này đã có người mua.');
                window.location.href='/HuynhVanGiang-4733/Product';
              </script>";
        exit();
    }
    
    // Nếu chưa ai mua thì mới xóa
    $this->productModel->deleteProduct($id);
    header('Location: /HuynhVanGiang-4733/Product');
}
 
}
?>