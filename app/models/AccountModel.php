<?php
class AccountModel
{
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Hàm duy nhất dùng cho đăng ký
    public function register($username, $email, $password)
    {
        // 1. Kiểm tra tồn tại
        $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE email = :email OR username = :username LIMIT 1";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->execute([':email' => $email, ':username' => $username]);

        if ($checkStmt->rowCount() > 0) {
            return "Email hoặc tên đăng nhập đã được sử dụng!";
        }

        // 2. Insert tài khoản
        $query = "INSERT INTO " . $this->table_name . " (username, email, password, role) VALUES (:username, :email, :password, 'user')";
        $stmt = $this->conn->prepare($query);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        return $stmt->execute([
            ':username' => strip_tags(trim($username)),
            ':email'    => strip_tags(trim($email)),
            ':password' => $hashed_password
        ]);
    }
    /**
     * 2. ĐĂNG NHẬP HỆ THỐNG
     */
    public function login($username_or_email, $password)
    {
        // Tìm kiếm tài khoản dựa trên Username hoặc Email nhập vào
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :input OR email = :input LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':input', $username_or_email);
        $stmt->execute();

        // Lấy dữ liệu dưới dạng Object
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // So khớp mật khẩu thô người dùng gõ vào với chuỗi hash đã mã hóa trong DB
            if (password_verify($password, $user->password)) {
                return $user; // Mật khẩu đúng, trả về toàn bộ thông tin User
            }
        }
        return false; // Sai tài khoản hoặc sai mật khẩu
    }

    /**
     * 3. CẬP NHẬT ĐỊA CHỈ & SĐT (PHỤC VỤ GIỎ HÀNG TIKTOK SHOP)
     */
    public function updateAddress($user_id, $phone, $address)
    {
        $query = "UPDATE " . $this->table_name . " SET phone = :phone, address = :address WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu văn bản tiếng Việt
        $phone = strip_tags(trim($phone));
        $address = strip_tags(trim($address));
        
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':id', $user_id);
        
        return $stmt->execute() ? true : false;
    }
    
    
}
?>