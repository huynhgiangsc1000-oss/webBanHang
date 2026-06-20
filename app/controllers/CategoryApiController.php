<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');
require_once('app/controllers/BaseApiController.php');

class CategoryApiController extends BaseApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }


    /**
     * GET /CategoryApi/index
     * Trả về danh sách tất cả danh mục sản phẩm (phục vụ cho select box / dropdown)
     * Mỗi item gồm: { id, name, description }
     */
    public function index()
    {
        // Yêu cầu xác thực JWT cho người dùng đã đăng nhập
        $this->protect();

        $categories = $this->categoryModel->getCategories();
        $this->json([
            'success' => true,
            'data'    => $categories
        ]);
    }

    /**
     * POST /api/categories
     * Thêm danh mục mới
     */
    public function store()
    {
        // Yêu cầu xác thực JWT với vai trò admin
        $this->protect('admin');

        $body = json_decode(file_get_contents('php://input'), true);

        if (!$body) {
            $this->json([
                'success' => false,
                'message' => 'Dữ liệu request không hợp lệ hoặc không phải định dạng JSON.'
            ], 400);
        }

        $name = $body['name'] ?? '';
        $description = $body['description'] ?? '';

        if (empty($name)) {
            $this->json([
                'success' => false,
                'message' => 'Tên danh mục không được để trống.'
            ], 422);
        }

        $success = $this->categoryModel->createCategory($name, $description);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tạo danh mục mới.'
            ], 500);
        }

        $this->json([
            'success' => true,
            'message' => 'Thêm danh mục mới thành công.'
        ], 201);
    }

    /**
     * DELETE /api/categories/{id}
     * Xóa danh mục theo ID
     */
    public function destroy($id)
    {
        // Yêu cầu xác thực JWT với vai trò admin
        $this->protect('admin');

        $category = $this->categoryModel->getCategoryById($id);
        if (!$category) {
            $this->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục với ID: ' . $id
            ], 404);
        }

        $success = $this->categoryModel->deleteCategory($id);

        if (!$success) {
            $this->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục này! Có thể danh mục này vẫn còn sản phẩm đang trực thuộc.'
            ], 409);
        }

        $this->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công.'
        ]);
    }
}
?>
