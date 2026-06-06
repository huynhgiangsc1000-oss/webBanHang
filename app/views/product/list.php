<?php 
/** @var stdClass[] $products */
/** @var bool $isAdmin */
include_once 'app/views/shares/header.php'; 
?>
<div class="container my-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h5 class="card-title mb-0 fw-bold">📦 Quản Lý Danh Sách Sản Phẩm</h5>
            <a href="/HuynhVanGiang-4733/Product/add" class="btn btn-light btn-sm fw-semibold shadow-sm">➕ Thêm sản phẩm mới</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Trạng thái</th>
                            <th>Giá bán</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <img src="/HuynhVanGiang-4733/uploads/<?php echo $product->image ?? 'default.jpg'; ?>" 
                                     style="width: 60px; height: 60px; object-fit: cover;" class="rounded">
                            </td>
                            <td class="fw-bold"><?php echo htmlspecialchars($product->name); ?></td>
                            <td>
                                <?php if (isset($product->status) && $product->status == 0): ?>
                                    <span class="badge bg-danger">Hết hàng</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Còn hàng</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-danger fw-bold"><?php echo number_format($product->price, 0, ',', '.'); ?> đ</td>
                            
                            <?php if ($isAdmin): ?>
                            <td class="text-center">
                                <a href="/HuynhVanGiang-4733/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                                <a href="/HuynhVanGiang-4733/Product/delete/<?php echo $product->id; ?>" 
                                   class="btn btn-danger btn-sm" onclick="return confirm('Xác nhận xóa?')">🗑️ Xóa</a>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="5" class="text-center py-4">Chưa có sản phẩm nào.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>