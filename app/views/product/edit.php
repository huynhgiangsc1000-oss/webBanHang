<?php include_once 'app/views/shares/header.php'; ?>
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning py-3"><h5 class="mb-0 fw-bold">📝 Chỉnh Sửa Sản Phẩm (#<?php echo $product->id; ?>)</h5></div>
        <div class="card-body p-4">
        <form action="/HuynhVanGiang-4733/Product/update" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product->id; ?>">
    <input type="hidden" name="existing_image" value="<?php echo $product->image; ?>">

    <div class="mb-3">
        <label class="form-label fw-semibold">Tên sản phẩm</label>
        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Danh mục</label>
        <select name="category_id" class="form-select" required>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->id; ?>" 
                    <?php echo ($product->category_id == $cat->id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($cat->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái kho</label>
                    <select name="status" class="form-select">
                       <option value="1" <?php echo ($product->status == 1) ? 'selected' : ''; ?>>Còn hàng</option>
                       <option value="0" <?php echo ($product->status == 0) ? 'selected' : ''; ?>>Hết hàng</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giá bán (đ)</label>
                    <input type="number" name="price" class="form-control" value="<?php echo $product->price; ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($product->description); ?></textarea>
                </div>
                <div class="mb-3">
                    <label>Ảnh hiện tại:</label><br>
                    <img src="/HuynhVanGiang-4733/uploads/<?php echo $product->image; ?>" style="width: 100px;">
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="d-flex justify-content-between">
                    <a href="/HuynhVanGiang-4733/Product" class="btn btn-outline-secondary">⬅️ Hủy</a>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">🔄 Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once 'app/views/shares/footer.php'; ?>