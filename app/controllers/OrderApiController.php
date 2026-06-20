<?php
require_once('app/config/database.php');
require_once('app/models/OrderModel.php');
require_once('app/models/ProductModel.php');
require_once('app/controllers/BaseApiController.php');

class OrderApiController extends BaseApiController
{
    private $orderModel;
    private $productModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
        $this->productModel = new ProductModel($this->db);
    }


    /**
     * GET /api/orders/{id}
     * Lấy thông tin chi tiết đơn hàng kèm các sản phẩm
     */
    public function show($id)
    {
        // Yêu cầu xác thực JWT
        $this->protect();

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
            ], 404);
        }

        $details = $this->orderModel->getOrderDetails($id);
        
        $orderData = [
            'id' => $order->id,
            'name' => $order->name,
            'phone' => $order->phone,
            'address' => $order->address,
            'total_price' => $order->total_price,
            'user_id' => $order->user_id,
            'status' => $order->status,
            'created_at' => $order->created_at,
            'products' => $details
        ];

        $this->json([
            'success' => true,
            'data' => $orderData
        ]);
    }

    /**
     * POST /api/orders
     * Tạo đơn hàng mới từ dữ liệu JSON
     */
    public function store()
    {
        // Yêu cầu xác thực JWT
        $user = $this->protect();

        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu request không hợp lệ hoặc không phải định dạng JSON.'
            ], 400);
        }

        $shipping_name = $body['shipping_name'] ?? '';
        $shipping_address = $body['shipping_address'] ?? '';
        $shipping_phone = $body['shipping_phone'] ?? '';
        $products = $body['products'] ?? [];

        if (empty($shipping_name) || empty($shipping_address) || empty($shipping_phone) || empty($products)) {
            $this->json([
                'success' => false,
                'message' => 'Các trường thông tin giao hàng và sản phẩm không được để trống.'
            ], 422);
        }

        // Kiểm tra tính tồn tại của các sản phẩm
        $cart = [];
        foreach ($products as $p) {
            $productId = $p['product_id'] ?? null;
            $quantity = $p['quantity'] ?? 1;

            if (!$productId) {
                $this->json([
                    'success' => false,
                    'message' => 'Mã sản phẩm (product_id) không hợp lệ.'
                ], 400);
            }

            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                $this->json([
                    'success' => false,
                    'message' => 'Sản phẩm với ID ' . $productId . ' không tồn tại.'
                ], 404);
            }

            $cart[$productId] = [
                'price' => $product->price,
                'quantity' => $quantity
            ];
        }

        // Lấy user_id thực tế từ payload JWT đã xác thực
        $user_id = $user->id;

        $success = $this->orderModel->createOrder($shipping_name, $shipping_phone, $shipping_address, $cart, $user_id);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo đơn hàng.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Tạo đơn hàng thành công.'
        ], 201);
    }

    /**
     * PUT /api/orders/{id}
     * Cập nhật trạng thái đơn hàng và kiểm tra logic lùi trạng thái
     */
    public function update($id)
    {
        // Yêu cầu xác thực JWT
        $this->protect();

        $order = $this->orderModel->getOrderById($id);
        if (!$order) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng với ID: ' . $id
            ], 404);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body || !isset($body['status'])) {
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu request không hợp lệ. Cần truyền trạng thái status.'
            ], 400);
        }

        $newStatus = trim($body['status']);
        $oldStatus = trim($order->status);

        // Quy đổi độ ưu tiên trạng thái để kiểm tra lùi trạng thái
        // Chờ xử lý / Chờ xác nhận / Chờ duyệt -> Đang giao hàng / Đang giao -> Đã giao / Đã giao hàng
        $statusPriority = [
            'chờ xử lý' => 1,
            'chờ xác nhận' => 1,
            'chờ duyệt' => 1,
            'đang giao hàng' => 2,
            'đang giao' => 2,
            'đã giao hàng' => 3,
            'đã giao' => 3,
            'đã hủy' => 4
        ];

        $oldPriority = $statusPriority[mb_strtolower($oldStatus)] ?? 1;
        $newPriority = $statusPriority[mb_strtolower($newStatus)] ?? 1;

        // Logic chống lùi trạng thái (ví dụ từ Đang giao lùi về Chờ xử lý)
        // Lưu ý: Trạng thái "Đã hủy" (4) không được lùi về các trạng thái khác, 
        // và đơn hàng đã "Đã giao" (3) thì không thể đổi trạng thái khác trừ khi hủy
        if ($oldPriority > 1 && $newPriority < $oldPriority) {
            $this->json([
                'success' => false,
                'message' => 'Không thể lùi trạng thái đơn hàng từ "' . $oldStatus . '" về "' . $newStatus . '".'
            ], 400);
        }

        if ($oldStatus === 'Đã hủy') {
            $this->json([
                'success' => false,
                'message' => 'Đơn hàng đã hủy không thể thay đổi trạng thái.'
            ], 400);
        }

        $success = $this->orderModel->updateStatus($id, $newStatus);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái đơn hàng thành công.'
        ]);
    }
}
?>
