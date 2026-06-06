<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/OrderModel.php'); 
require_once('app/models/AccountModel.php'); // Dùng để truy vấn & lưu địa chỉ thông minh kiểu TikTok Shop

class CartController
{
    private $productModel;
    private $orderModel;
    private $accountModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->orderModel = new OrderModel($this->db);
        $this->accountModel = new AccountModel($this->db);
    }

    /**
     * 1. HIỂN THỊ TRANG GIỎ HÀNG
     */
    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        
        // Mặc định thông tin trống ban đầu
        $user_phone = '';
        $user_address = '';

        // Nếu người dùng đã đăng nhập, tự động lấy SĐT và Địa chỉ đã lưu từ Database
        if (isset($_SESSION['user_id'])) {
            $query = "SELECT phone, address FROM users WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            if ($user) {
                $user_phone = $user->phone ?? '';
                $user_address = $user->address ?? '';
            }
        }

        include_once 'app/views/cart/index.php';
    }

    /**
     * 2. THÊM SẢN PHẨM VÀO GIỎ HÀNG
     */
    public function addToCart($id)
    {
        // Lấy thông tin sản phẩm từ Database theo ID
        $product = $this->productModel->getProductById($id);

        if (!$product) {
            echo "<script>alert('Sản phẩm không tồn tại!'); window.location.href='/HuynhVanGiang-4733/';</script>";
            exit();
        }

        // Cấu trúc một món hàng trong Session giỏ hàng
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$id])) {
            // Nếu sản phẩm đã có sẵn, tăng số lượng lên 1
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            // Nếu chưa có, tiến hành thêm mới vào mảng session
            $_SESSION['cart'][$id] = [
                'name' => $product->name,
                'price' => $product->price,
                'image' => $product->image,
                'quantity' => 1
            ];
        }

        // Sau khi thêm thành công, chuyển hướng về lại trang giỏ hàng để xem trực quan
        header('Location: /HuynhVanGiang-4733/Cart');
        exit();
    }

    /**
     * 3. TĂNG SỐ LƯỢNG SẢN PHẨM (+1) TẠI GIAO DIỆN
     */
    public function increase($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        }
        header('Location: /HuynhVanGiang-4733/Cart');
        exit();
    }

    /**
     * 4. GIẢM SỐ LƯỢNG SẢN PHẨM (-1) TẠI GIAO DIỆN
     */
    public function decrease($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] -= 1;
            
            // Nếu số lượng tụt xuống bằng 0 hoặc nhỏ hơn, tự động xóa sản phẩm ra khỏi giỏ
            if ($_SESSION['cart'][$id]['quantity'] <= 0) {
                unset($_SESSION['cart'][$id]);
            }
        }
        
        // Hủy bỏ session giỏ hàng nếu trong giỏ hoàn toàn không còn món nào
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
        
        header('Location: /HuynhVanGiang-4733/Cart');
        exit();
    }

    /**
     * 5. XÓA HOÀN TOÀN MỘT SẢN PHẨM KHỎI GIỎ HÀNG
     */
    public function remove($id)
    {
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        
        if (empty($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }

        header('Location: /HuynhVanGiang-4733/Cart');
        exit();
    }

    /**
     * 6. HÀM LƯU ĐỊA CHỈ QUA AJAX (KHÔNG BỊ LOAD LẠI TRANG)
     */
    public function saveAddress()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            
            // Gọi AccountModel để thực thi câu lệnh SQL UPDATE ngược lại bảng users
            $result = $this->accountModel->updateAddress($_SESSION['user_id'], $phone, $address);
            
            header('Content-Type: application/json');
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Đã lưu thông tin giao hàng thành công!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Lỗi hệ thống, không thể lưu địa chỉ.']);
            }
            exit();
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ hoặc phiên làm việc hết hạn.']);
        exit();
    }

    /**
     * 7. XỬ LÝ SUBMIT HOÀN TẤT ĐẶT HÀNG (LƯU ĐƠN HÀNG VÀO DATABASE)
     */
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {
            // Thu thập thông tin giao nhận từ Form ẩn hoặc Form hiển thị truyền sang
            $name = $_POST['name'] ?? 'Khách hàng';
            $phone = $_POST['phone'] ?? '';
            $address = $_POST['address'] ?? '';
            $user_id = $_SESSION['user_id'] ?? null;

            // Chặn đứng hành vi bấm thanh toán khi thông tin liên hệ trống rỗng
            if (empty($phone) || empty($address)) {
                echo "<script>alert('Vui lòng cập nhật số điện thoại và địa chỉ nhận hàng trước khi bấm đặt hàng!'); window.location.href='/HuynhVanGiang-4733/Cart';</script>";
                exit();
            }

            // Gọi OrderModel tạo hóa đơn cùng danh sách sản phẩm chi tiết đơn hàng
            $result = $this->orderModel->createOrder($name, $phone, $address, $_SESSION['cart'], $user_id);

            if ($result) {
                // Xóa sạch giỏ hàng hiện tại sau khi mua thành công
                unset($_SESSION['cart']);
                echo "<script>alert('🛒 Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại My Store.'); window.location.href='/HuynhVanGiang-4733/';</script>";
                exit();
            } else {
                echo "Đã xảy ra sự cố nghiêm trọng trong quá trình xử lý lưu đơn hàng.";
            }
        }
    }
}
?>