<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
require_once('app/utils/JWTHandler.php');

class AccountController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        // Kích hoạt session nếu chưa có để duy trì trạng thái đăng nhập toàn hệ thống
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }

    /**
     * 1. XỬ LÝ ĐĂNG NHẬP
     */
    public function login()
    {
        // Nếu đã đăng nhập thành công trước đó, đá thẳng người dùng về trang Sản phẩm
        if (isset($_SESSION['user_id'])) {
            header('Location: /HuynhVanGiang-4733/Product');
            exit();
        }

        $error = ''; // Khởi tạo biến thông báo lỗi trống ban đầu

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username_or_email = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Gọi hàm xử lý logic từ AccountModel để đối chiếu dữ liệu DB
            $user = $this->accountModel->login($username_or_email, $password);

            if ($user) {
                // Đăng nhập thành công -> Đúc thông tin cốt lõi vào bộ nhớ Session
                $_SESSION['user_id'] = $user->id;
                $_SESSION['username'] = $user->username;
                $_SESSION['user_role'] = $user->role; // Phân quyền (admin hoặc user)

                // Điều hướng sang trang quản lý/xem sản phẩm
                header('Location: /HuynhVanGiang-4733/Product');
                exit();
            } else {
                $error = "Tên đăng nhập, email hoặc mật khẩu không chính xác.";
            }
        }

        // --- GHÉP KHUNG GIAO DIỆN CHUẨN MVC CHO TRANG ĐĂNG NHẬP ---
        $title = "Đăng Nhập Hệ Thống";
        include_once 'app/views/shares/header.php'; // Đọc thanh Navbar điều hướng
        include_once 'app/views/account/login.php';   // Đọc nội dung Form đăng nhập
        include_once 'app/views/shares/footer.php'; // Đọc chân trang và nạp Javascript
    }

    /**
     * 1b. API ĐĂNG NHẬP (Lấy Token/JWT giả lập & thiết lập Session)
     * POST /account/checkLogin
     */
    public function checkLogin()
    {
        // Nhận dữ liệu JSON từ body request hoặc $_POST thông thường
        $body = json_decode(file_get_contents('php://input'), true);
        
        $username = $body['username'] ?? $_POST['username'] ?? '';
        $password = $body['password'] ?? $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            header('Content-Type: application/json; charset=utf-8');
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Tên đăng nhập và mật khẩu không được để trống.'
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $user = $this->accountModel->login($username, $password);

        header('Content-Type: application/json; charset=utf-8');
        if ($user) {
            // Đăng nhập thành công -> Thiết lập session để các hành động MVC khác hoạt động đồng bộ
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;
            $_SESSION['user_role'] = $user->role;

            // Tạo mã token JWT thực tế bằng thư viện firebase/php-jwt
            $token = JWTHandler::generateToken($user->id, $user->username, $user->role);

            echo json_encode([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role
                ]
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            http_response_code(401);
            echo json_encode([
                'success' => false,
                'message' => 'Tên đăng nhập hoặc mật khẩu không chính xác.'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit();
    }

    /**
     * 2. XỬ LÝ ĐĂNG KÝ
     */
   // app/controllers/AccountController.php
   public function register() {
    // Nếu là GET thì hiển thị form
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        include_once 'app/views/shares/header.php';
        include_once 'app/views/account/register.php';
        include_once 'app/views/shares/footer.php';
        return;
    }

    // Nếu là POST thì xử lý dữ liệu
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm) {
        $this->renderAlert('error', 'Lỗi!', 'Xác nhận mật khẩu không khớp.');
        return;
    }

    $result = $this->accountModel->register($username, $email, $password);

    if ($result === true) {
        $this->renderAlert('success', 'Thành công!', 'Đăng ký tài khoản thành công!');
    } else {
        $this->renderAlert('error', 'Lỗi!', $result);
    }
}

private function renderAlert($icon, $title, $text) {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '$icon',
                title: '$title',
                text: '$text',
                confirmButtonText: 'Đồng ý'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Chuyển hướng về trang sản phẩm thay vì quay lại trang cũ
                    window.location.href = '/HuynhVanGiang-4733/Product';
                }
            });
        });
    </script>";
    exit(); 
}
// Hàm hỗ trợ hiển thị SweetAlert2
    /**
     * 3. XỬ LÝ ĐĂNG XUẤT
     */
    public function logout()
    {
        // Làm sạch mảng Session hiện tại
        $_SESSION = array();

        // Xóa hoàn toàn dấu vết Cookie của Session trên trình duyệt Client
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Hủy bỏ phiên làm việc
        session_destroy();

        // Trả người dùng về trang đăng nhập ban đầu
        header('Location: /HuynhVanGiang-4733/Account/login');
        exit();
    }
}
?>