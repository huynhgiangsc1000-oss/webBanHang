<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/controllers/BaseApiController.php');

class ProductApiController extends BaseApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }


    /**
     * GET /ProductApi/index
     * Trả về danh sách tất cả sản phẩm kèm tên danh mục
     */
    public function index()
    {
        // Yêu cầu xác thực JWT cho người dùng đã đăng nhập
        $this->protect();

        $products = $this->productModel->getProducts();
        $this->json([
            'success' => true,
            'data'    => $products
        ]);
    }

    /**
     * GET /ProductApi/show/{id}
     * Trả về thông tin chi tiết một sản phẩm theo ID
     */
    public function show($id)
    {
        // Yêu cầu xác thực JWT cho người dùng đã đăng nhập
        $this->protect();

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm với ID: ' . $id
            ], 404);
        }
        $this->json([
            'success' => true,
            'data'    => $product
        ]);
    }

    /**
     * POST /ProductApi/store
     * Thêm sản phẩm mới từ dữ liệu JSON trong request body
     * Body JSON: { "name": "...", "description": "...", "price": 0, "category_id": 1 }
     */
    public function store()
    {
        // Yêu cầu xác thực JWT với vai trò admin
        $this->protect('admin');

        // Đọc dữ liệu JSON từ request body
        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu request không hợp lệ hoặc không phải định dạng JSON.'
            ], 400);
        }

        $name        = $body['name']        ?? '';
        $description = $body['description'] ?? '';
        $price       = $body['price']       ?? '';
        $category_id = $body['category_id'] ?? null;

        $result = $this->productModel->addProduct($name, $description, $price, $category_id);

        if (is_array($result)) {
            // Trả về danh sách lỗi validate
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu đầu vào không hợp lệ.',
                'errors'  => $result
            ], 422);
        }

        $this->json([
            'success' => true,
            'message' => 'Thêm sản phẩm mới thành công.'
        ], 201);
    }

    /**
     * PUT/PATCH /ProductApi/update/{id}
     * Cập nhật sản phẩm từ dữ liệu JSON trong request body
     * Body JSON: { "name": "...", "description": "...", "price": 0, "category_id": 1, "status": 1 }
     */
    public function update($id)
    {
        // Yêu cầu xác thực JWT với vai trò admin
        $this->protect('admin');

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm với ID: ' . $id
            ], 404);
        }

        // Đọc dữ liệu JSON từ request body
        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu request không hợp lệ hoặc không phải định dạng JSON.'
            ], 400);
        }

        // Cho phép cập nhật từng phần (PATCH), lấy giá trị hiện tại nếu không truyền
        $name        = $body['name']        ?? $product->name;
        $description = $body['description'] ?? $product->description;
        $price       = $body['price']       ?? $product->price;
        $category_id = $body['category_id'] ?? $product->category_id;
        $status      = $body['status']      ?? $product->status;

        $success = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $status);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi cập nhật sản phẩm.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Cập nhật sản phẩm thành công.'
        ]);
    }

    /**
     * DELETE /ProductApi/destroy/{id}
     * Xóa sản phẩm theo ID
     */
    public function destroy($id)
    {
        // Yêu cầu xác thực JWT với vai trò admin
        $this->protect('admin');

        $product = $this->productModel->getProductById($id);
        if (!$product) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy sản phẩm với ID: ' . $id
            ], 404);
        }

        // Kiểm tra xem sản phẩm đã có trong đơn hàng chưa
        if ($this->productModel->isProductPurchased($id)) {
            $this->json([
                'success' => false,
                'message' => 'Không thể xóa! Sản phẩm này đã có người mua.'
            ], 409);
        }

        $success = $this->productModel->deleteProduct($id);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa sản phẩm.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công.'
        ]);
    }
}
?>
