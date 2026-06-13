<?php
$title = "Thêm sản phẩm";
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
                
                <!-- Hiển thị thông báo lỗi (Client-side render) -->
                <div id="error-alert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                    <strong class="d-block mb-1">⚠️ Vui lòng sửa các lỗi sau:</strong>
                    <ul id="error-list" class="mb-0 ps-3"></ul>
                    <button type="button" class="btn-close" onclick="document.getElementById('error-alert').classList.add('d-none')" aria-label="Close"></button>
                </div>

                <form id="add-product-form">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nhập tên sản phẩm" required>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label fw-semibold">Giá bán (đ) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="price" name="price" placeholder="Ví dụ: 150000" step="0.01" required>
                            <span class="input-group-text bg-secondary-subtle fw-medium">đ</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold">Danh mục sản phẩm <span class="text-danger">*</span></label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="" disabled selected>-- Đang tải danh mục sản phẩm --</option>
                        </select>
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

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const categorySelect = document.getElementById('category_id');
        const form = document.getElementById('add-product-form');
        const errorAlert = document.getElementById('error-alert');
        const errorList = document.getElementById('error-list');

        // 1. Tải danh sách danh mục qua Fetch API
        fetch('/HuynhVanGiang-4733/api/categories')
            .then(response => response.json())
            .then(res => {
                if (res.success && res.data) {
                    categorySelect.innerHTML = '<option value="" disabled selected>-- Chọn danh mục sản phẩm --</option>';
                    res.data.forEach(category => {
                        const option = document.createElement('option');
                        option.value = category.id;
                        option.textContent = category.name;
                        categorySelect.appendChild(option);
                    });
                } else {
                    categorySelect.innerHTML = '<option value="" disabled>Lỗi tải danh mục</option>';
                }
            })
            .catch(err => {
                console.error("Lỗi tải danh mục:", err);
                categorySelect.innerHTML = '<option value="" disabled>Lỗi kết nối server</option>';
            });

        // 2. Gửi dữ liệu form dạng JSON lên server bằng phương thức POST
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            errorAlert.classList.add('d-none');
            errorList.innerHTML = '';

            const payload = {
                name: document.getElementById('name').value.trim(),
                price: parseFloat(document.getElementById('price').value),
                category_id: parseInt(categorySelect.value),
                description: document.getElementById('description').value.trim()
            };

            fetch('/HuynhVanGiang-4733/api/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(payload)
            })
            .then(response => response.json())
            .then(res => {
                if (res.success) {
                    alert('Thêm sản phẩm thành công!');
                    window.location.href = '/HuynhVanGiang-4733/Product';
                } else {
                    errorAlert.classList.remove('d-none');
                    if (res.errors) {
                        // Res.errors là một Object dạng { field: error_msg }
                        Object.values(res.errors).forEach(errText => {
                            const li = document.createElement('li');
                            li.textContent = errText;
                            errorList.appendChild(li);
                        });
                    } else {
                        const li = document.createElement('li');
                        li.textContent = res.message || 'Lỗi không xác định khi thêm sản phẩm.';
                        errorList.appendChild(li);
                    }
                }
            })
            .catch(err => {
                console.error("Lỗi thêm sản phẩm:", err);
                errorAlert.classList.remove('d-none');
                errorList.innerHTML = '<li>Lỗi kết nối xảy ra. Không thể gửi sản phẩm.</li>';
            });
        });
    });
    </script>
</body>
</html>
<?php 
include_once 'app/views/shares/footer.php'; 
?>