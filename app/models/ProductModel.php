<?php
class ProductModel
{
    private $conn;
    private $table_name = "product";

    public function __construct($db)
    {
        $this->conn = $db;
    }
    public function getProducts()
    {
        $query = "SELECT p.id, p.name, p.description, p.price, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductById($id)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addProduct($name, $description, $price, $category_id)
    {
        $errors = [];
        if (empty($name)) $errors['name'] = 'Tên sản phẩm không được để trống';
        if (empty($description)) $errors['description'] = 'Mô tả không được để trống';
        if (!is_numeric($price) || $price < 0) $errors['price'] = 'Giá sản phẩm không hợp lệ';
        
        if (count($errors) > 0) return $errors;

        $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id) 
                  VALUES (:name, :description, :price, :category_id)";
        $stmt = $this->conn->prepare($query);

        // Bảo mật: Sử dụng cả htmlspecialchars và strip_tags để làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags(trim($name)));
        $description = htmlspecialchars(strip_tags(trim($description)));
        $price = htmlspecialchars(strip_tags(trim($price)));
        $category_id = htmlspecialchars(strip_tags(trim($category_id)));

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);

        return $stmt->execute() ? true : false;
    }

   // Thêm vào ProductModel.php
public function updateProduct($id, $name, $description, $price, $category_id, $status)
{
    $query = "UPDATE " . $this->table_name . " 
              SET name = :name, description = :description, price = :price, 
                  category_id = :category_id, status = :status 
              WHERE id = :id";
    $stmt = $this->conn->prepare($query);

    // Bảo mật: Làm sạch dữ liệu trước khi lưu
    $name = htmlspecialchars(strip_tags(trim($name)));
    $description = htmlspecialchars(strip_tags(trim($description)));
    $price = htmlspecialchars(strip_tags(trim($price)));
    $category_id = htmlspecialchars(strip_tags(trim($category_id)));
    $status = htmlspecialchars(strip_tags(trim($status)));

    return $stmt->execute([
        ':name' => $name, ':description' => $description, ':price' => $price,
        ':category_id' => $category_id, ':status' => $status, ':id' => $id
    ]);
}
    public function deleteProduct($id)
    {
        // ĐÃ SỬA: Bọc try-catch để xử lý chặn lỗi khóa ngoại an toàn
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            // Trả về false nếu sản phẩm này đã nằm trong hóa đơn đặt hàng của khách (không cho phép xóa)
            return false;
        }
    }

public function isProductPurchased($product_id)
{
    // Kiểm tra xem sản phẩm có nằm trong bảng order_details không
    $query = "SELECT COUNT(*) FROM order_details WHERE product_id = ?";
    $stmt = $this->conn->prepare($query);
    $stmt->execute([$product_id]);
    
    // Nếu kết quả trả về > 0, nghĩa là đã có người mua sản phẩm này
    return $stmt->fetchColumn() > 0;
}
public function getProductsByCategory($category_id = null, $keyword = null) {
    $query = "SELECT * FROM product WHERE 1=1";
    $params = [];

    if ($category_id) {
        $query .= " AND category_id = ?";
        $params[] = $category_id;
    }
    if ($keyword) {
        $query .= " AND name LIKE ?";
        $params[] = "%$keyword%";
    }

    $stmt = $this->conn->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
// Thêm vào ProductModel.php
public function updateStatus($id, $status) {
    $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
    $stmt = $this->conn->prepare($query);
    return $stmt->execute([':status' => $status, ':id' => $id]);
}
}
?>