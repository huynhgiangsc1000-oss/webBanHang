<?php
class CategoryModel
{
    private $conn;
    private $table_name = "category";

    // Khai báo đầy đủ thuộc tính tương ứng với các cột trong Database
    public $id;
    public $name;
    public $description; // Đã thêm thuộc tính này để quản lý mô tả danh mục

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy toàn bộ danh sách danh mục sản phẩm (Laptop, Smart phone...)
     */
    public function getCategories()
    {
        // Câu lệnh SQL lấy tất cả bản ghi từ bảng category kèm theo cột description
        $query = "SELECT id, name, description FROM " . $this->table_name . " ORDER BY id DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Trả về mảng chứa các đối tượng tiêu chuẩn (stdClass Object) tương thích với View
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Hàm thêm mới một danh mục vào Cơ sở dữ liệu
     */
    public function createCategory($name, $description)
    {
        // Câu lệnh INSERT sử dụng các Placeholder an toàn để chống tấn công SQL Injection
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu đầu vào (Xóa các thẻ HTML lạ nếu có)
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        // Ràng buộc (Bind) tham số truyền từ Form vào câu lệnh SQL
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        // Thực thi câu lệnh và trả về kết quả true/false
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    /**
 * Lấy chi tiết một danh mục theo ID (Phục vụ cho trang Sửa)
 */
public function getCategoryById($id)
{
    $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

/**
 * Cập nhật thông tin danh mục
 */
public function updateCategory($id, $name, $description)
{
    $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    $name = htmlspecialchars(strip_tags($name));
    $description = htmlspecialchars(strip_tags($description));

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':description', $description);

    return $stmt->execute();
}

/**
 * Xóa danh mục kèm theo kiểm tra ràng buộc sản phẩm
 */
public function deleteCategory($id)
{
    // 1. Kiểm tra xem có sản phẩm nào thuộc danh mục này không
    $checkQuery = "SELECT COUNT(*) FROM product WHERE category_id = :id";
    $checkStmt = $this->conn->prepare($checkQuery);
    $checkStmt->bindParam(':id', $id);
    $checkStmt->execute();
    $productCount = $checkStmt->fetchColumn();

    // Nếu số sản phẩm lớn hơn 0, trả về false (Không cho xóa)
    if ($productCount > 0) {
        return false;
    }

    // 2. Nếu không có sản phẩm nào, tiến hành xóa bình thường
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':id', $id);
    return $stmt->execute();
}
}
?>