<?php
class CategoryController {
    private $db;
    private $categoryModel;

    public function __construct() {
        // 1. Kết nối database
        require_once 'app/config/database.php';
        $this->db = (new Database())->getConnection();

        // 2. Nạp Model Category
        require_once 'app/models/CategoryModel.php';
        $this->categoryModel = new CategoryModel($this->db);
        
        // Kích hoạt session toàn cục cho controller để truyền thông báo báo lỗi/thành công
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // 1. Trang hiển thị danh sách danh mục (URL: /Category hoặc /Category/index)
    public function index() {
        $categories = $this->categoryModel->getCategories();
        
        // --- GHÉP LAYOUT CHUẨN MVC CHO TRANG DANH SÁCH ---
        $title = "Quản Lý Danh Mục";
        include_once 'app/views/shares/header.php'; // Nạp thanh Navbar menu
        include_once 'app/views/category/list.php';   // Nạp bảng hiển thị danh mục
        include_once 'app/views/shares/footer.php'; // Nạp chân trang & Javascript
    }

    // 2. Hàm hiển thị Form thêm danh mục mới (URL: /Category/add)
    public function add() {
        // --- GHÉP LAYOUT CHUẨN MVC CHO TRANG THÊM ---
        $title = "Thêm Danh Mục Mới";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/category/add.php';
        include_once 'app/views/shares/footer.php';
    }

    // 3. Hàm xử lý lưu danh mục mới vào Database khi submit Form
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (!empty($name)) {
                $result = $this->categoryModel->createCategory($name, $description);

                if ($result) {
                    $_SESSION['success_message'] = "🎉 Thêm danh mục mới thành công!";
                    header('Location: /HuynhVanGiang-4733/Category');
                    exit;
                }
            }
            // Nếu có lỗi hoặc tên trống, quay lại trang add
            header('Location: /HuynhVanGiang-4733/Category/add');
            exit;
        }
    }

    // 4. Hàm hiển thị Form sửa danh mục (URL: /Category/edit/{id})
    public function edit($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            header('Location: /HuynhVanGiang-4733/Category');
            exit;
        }
        
        // --- GHÉP LAYOUT CHUẨN MVC CHO TRANG SỬA ---
        $title = "Chỉnh Sửa Danh Mục";
        include_once 'app/views/shares/header.php';
        include_once 'app/views/category/edit.php';
        include_once 'app/views/shares/footer.php';
    }

    // 5. Hàm xử lý cập nhật danh mục khi SUBMIT Form sửa
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? '';
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            if (!empty($id) && !empty($name)) {
                $this->categoryModel->updateCategory($id, $name, $description);
                $_SESSION['success_message'] = "🎉 Cập nhật thông tin danh mục thành công!";
            }
            header('Location: /HuynhVanGiang-4733/Category');
            exit;
        }
    }

    // 6. Hàm xử lý xóa danh mục (URL: /Category/delete/{id})
    public function delete($id) {
        $result = $this->categoryModel->deleteCategory($id);

        if ($result === false) {
            // Tạo thông báo lỗi lưu vào session
            $_SESSION['error_message'] = "⚠️ Không thể xóa! Danh mục này hiện đang có sản phẩm thuộc về.";
        } else {
            $_SESSION['success_message'] = "🎉 Xóa danh mục thành công!";
        }

        header('Location: /HuynhVanGiang-4733/Category');
        exit;
    }
}
?>