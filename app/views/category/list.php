<?php
$title = "Danh sách danh mục";
include_once 'app/views/shares/header.php';

// Khởi động session để đọc thông báo lỗi/thành công
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container my-4">
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <?php 
                echo $_SESSION['error_message']; 
                unset($_SESSION['error_message']); // Xóa sau khi hiển thị xong
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <?php 
                echo $_SESSION['success_message']; 
                unset($_SESSION['success_message']); 
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0 fw-bold">📁 Quản Lý Danh Mục Sản Phẩm</h5>
            <a href="/HuynhVanGiang-4733/Category/add" class="btn btn-light btn-sm fw-semibold shadow-sm">
                ➕ Thêm danh mục mới
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                          
                            <th>Tên danh mục</th>
                            <th>Mô tả danh mục</th>
                            <th class="text-center" style="width: 180px;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($categories)): ?>
                            <?php foreach($categories as $cat): ?>
                            <tr>
                             
                                <td><span class="fw-semibold text-dark"><?php echo htmlspecialchars($cat->name, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                <td class="text-muted"><?php echo htmlspecialchars($cat->description ?? 'Chưa có mô tả', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="text-center">
                                    <a href="/HuynhVanGiang-4733/Category/edit/<?php echo $cat->id; ?>" class="btn btn-warning btn-sm fw-semibold text-dark shadow-sm me-1">
                                        ✏️ Sửa
                                    </a>
                                    <a href="/HuynhVanGiang-4733/Category/delete/<?php echo $cat->id; ?>" 
                                       class="btn btn-danger btn-sm fw-semibold shadow-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
                                        🗑️ Xóa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    📭 Chưa có danh mục nào trong hệ thống.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include_once 'app/views/shares/footer.php'; ?>