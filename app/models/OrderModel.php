<?php
class OrderModel
{
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    /**
     * LẤY DANH SÁCH TẤT CẢ ĐƠN HÀNG (Hiển thị ở trang Quản lý Đơn hàng)
     */
    public function getAllOrders()
    {
        // Đảm bảo chọn tường minh cột status hoặc dùng * (nếu * mà vẫn lỗi thì do cấu trúc bảng)
        $query = "SELECT o.*, u.username AS customer_name 
                  FROM orders o 
                  LEFT JOIN users u ON o.user_id = u.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * CẬP NHẬT TRẠNG THÁI ĐƠN HÀNG (Chờ xử lý -> Đang giao -> Đã giao -> Đã hủy)
     */
    public function updateStatus($order_id, $status)
    {
        $query = "UPDATE orders SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }

    /**
     * THỐNG KÊ 1: TỔNG DOANH THU (Chỉ tính những đơn 'Đã giao')
     */
    public function getTotalRevenue()
    {
        $query = "SELECT SUM(total_price) AS total FROM orders WHERE status = 'Đã giao'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total ?? 0; // Nếu rỗng thì trả về số 0
    }

    /**
     * THỐNG KÊ 2: TỔNG SỐ ĐƠN THÀNH CÔNG
     */
    public function getTotalSuccessfulOrders()
    {
        $query = "SELECT COUNT(*) AS total FROM orders WHERE status = 'Đã giao'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    /**
     * THỐNG KÊ 3: TỔNG SỐ THÀNH VIÊN TRÊN HỆ THỐNG
     */
    public function getTotalUsers()
    {
        $query = "SELECT COUNT(*) AS total FROM users WHERE role = 'user'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row->total;
    }

    /**
     * LẤY DỮ LIỆU BIỂU ĐỒ: DOANH THU THEO NGÀY (Phục vụ vẽ Chart.js)
     */
    public function getRevenueByDay()
    {
        $query = "SELECT DATE(created_at) AS order_date, SUM(total_price) AS daily_revenue 
                  FROM orders 
                  WHERE status = 'Đã giao' 
                  GROUP BY DATE(created_at) 
                  ORDER BY order_date ASC 
                  LIMIT 7"; // Lấy tối đa dữ liệu của 7 ngày gần nhất
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function getOrderDetails($order_id)
    {
    // JOIN bảng order_details với bảng product để lấy tên sản phẩm và hình ảnh
    $query = "SELECT od.*, p.name AS product_name, p.image AS product_image 
              FROM order_details od
              JOIN product p ON od.product_id = p.id
              WHERE od.order_id = :order_id";
              
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function createOrder($name, $phone, $address, $cart, $user_id) {
        try {
            $this->conn->beginTransaction();
            
            $total = 0;
            foreach ($cart as $item) $total += ($item['price'] * $item['quantity']);
        
            // Hãy chắc chắn tên các cột ở đây khớp 100% với HeidiSQL
            $sql = "INSERT INTO orders (name, phone, address, total_price, user_id, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, 'Chờ xử lý', NOW())";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$name, $phone, $address, $total, $user_id]);
            $order_id = $this->conn->lastInsertId();
        
            foreach ($cart as $id => $item) {
                $stmt_detail = $this->conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt_detail->execute([$order_id, $id, $item['quantity'], $item['price']]);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            // Dòng này sẽ hiển thị thông báo lỗi cụ thể của MySQL lên màn hình
            die("Lỗi Database: " . $e->getMessage()); 
        }
    }
    public function restoreStock($order_id) {
        $items = $this->conn->query("SELECT product_id, quantity FROM order_details WHERE order_id = $order_id")->fetchAll();
        
        foreach ($items as $item) {
            $stmt = $this->conn->prepare("UPDATE product SET stock = stock + ? WHERE id = ?");
            $stmt->execute([$item->quantity, $item->product_id]);
        }
    }
    // Thêm vào cuối class OrderModel
public function getOrdersByUserId($user_id) {
    $stmt = $this->conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

public function getOrderById($order_id) {
    $stmt = $this->conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}
}