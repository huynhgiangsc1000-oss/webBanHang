<?php
$title = "Chỉnh sửa danh mục";
include_once 'app/views/shares/header.php';
?>

<div class="container my-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title mb-0 fw-bold">✏️ Chỉnh Sửa Danh Mục</h5>
                </div>
                <div class="card-body p-4">
                    <form action="/HuynhVanGiang-4733/Category/update" method="POST">
                        <input type="hidden" name="id" value="<?php echo $category->id; ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Mô tả danh mục</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($category->description ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top pt-3">
                            <a href="/HuynhVanGiang-4733/Category" class="btn btn-outline-secondary fw-medium">Quay lại</a>
                            <button type="submit" class="btn btn-warning px-4 fw-semibold shadow-sm text-dark">Cập nhật danh mục</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'app/views/shares/footer.php'; ?>