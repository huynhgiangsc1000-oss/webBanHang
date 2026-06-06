<?php
$title = "Danh sách sản phẩm";
include_once 'app/views/shares/header.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container my-5" style="max-width: 600px;">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-success text-white py-3">
                <h5 class="card-title mb-0 fw-bold">✨ Thêm Sản Phẩm Mới</h5>
            </div>
            <div class="card-body p-4">
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong class="d-block mb-1">⚠️ Vui lòng sửa các lỗi sau:</strong>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form action="/HuynhVanGiang-4733/Product/save" method="POST" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nhập từ 10 - 100 ký tự" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Giá bán (đ) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price" name="price" placeholder="Ví dụ: 150000" step="0.01" required>
                            <span class="input-group-text bg-secondary-subtle fw-medium">đ</span>
                        </div>
                    </div>

                    <?php if (isset($categories)): ?>
                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Danh mục sản phẩm <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="" disabled selected>-- Chọn danh mục sản phẩm --</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category->id; ?>">
                                    <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Hình ảnh sản phẩm</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label fw-semibold">Mô tả sản phẩm</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập mô tả chi tiết về sản phẩm..." required></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3">
                        <a href="/HuynhVanGiang-4733/Product" class="btn btn-outline-secondary fw-medium">
                            ⬅️ Quay lại danh sách
                        </a>
                        <button type="submit" class="btn btn-success px-4 fw-semibold shadow-sm">
                            💾 Lưu sản phẩm
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php 
// 2. NẠP FOOTER CHỨA CÁC FILE SCRIPT BOOTSTRAP 5
include_once 'app/views/shares/footer.php'; 
?>