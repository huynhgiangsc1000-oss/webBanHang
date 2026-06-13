<?php include_once 'app/views/shares/header.php'; ?>
<div class="container my-5" style="max-width: 600px;">
    <div class="card shadow-sm">
        <div class="card-header bg-warning py-3">
            <h5 class="mb-0 fw-bold">📝 Chỉnh Sửa Sản Phẩm (#<span id="product-id-title"><?php echo $product->id; ?></span>)</h5>
        </div>
        <div class="card-body p-4">
            
            <!-- Hiển thị thông báo lỗi (Client-side render) -->
            <div id="error-alert" class="alert alert-danger alert-dismissible fade show d-none" role="alert">
                <strong class="d-block mb-1">⚠️ Vui lòng sửa các lỗi sau:</strong>
                <ul id="error-list" class="mb-0 ps-3"></ul>
                <button type="button" class="btn-close" onclick="document.getElementById('error-alert').classList.add('d-none')" aria-label="Close"></button>
            </div>

            <form id="edit-product-form">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên sản phẩm</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Đang tải..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Danh mục</label>
                    <select id="category_id" name="category_id" class="form-select" required>
                        <option value="" disabled selected>-- Đang tải danh mục --</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái kho</label>
                    <select id="status" name="status" class="form-select">
                       <option value="1">Còn hàng</option>
                       <option value="0">Hết hàng</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giá bán (đ)</label>
                    <input type="number" id="price" name="price" class="form-control" placeholder="Đang tải..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <textarea id="description" name="description" class="form-control" rows="4" placeholder="Đang tải..."></textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/HuynhVanGiang-4733/Product" class="btn btn-outline-secondary">⬅️ Hủy</a>
                    <button type="submit" class="btn btn-warning px-4 fw-bold">🔄 Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const productId = <?php echo json_encode($product->id); ?>;
    const categorySelect = document.getElementById('category_id');
    const form = document.getElementById('edit-product-form');
    const errorAlert = document.getElementById('error-alert');
    const errorList = document.getElementById('error-list');

    // Các trường dữ liệu
    const nameInput = document.getElementById('name');
    const statusSelect = document.getElementById('status');
    const priceInput = document.getElementById('price');
    const descriptionInput = document.getElementById('description');

    // 1. Tải toàn bộ danh mục & nạp thông tin chi tiết sản phẩm
    Promise.all([
        fetch('/HuynhVanGiang-4733/api/categories').then(r => r.json()),
        fetch(`/HuynhVanGiang-4733/api/products/${productId}`).then(r => r.json())
    ])
    .then(([catRes, prodRes]) => {
        // Render danh mục vào select
        if (catRes.success && catRes.data) {
            categorySelect.innerHTML = '<option value="" disabled>-- Chọn danh mục sản phẩm --</option>';
            catRes.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id;
                option.textContent = category.name;
                categorySelect.appendChild(option);
            });
        }

        // Điền dữ liệu sản phẩm
        if (prodRes.success && prodRes.data) {
            const product = prodRes.data;
            nameInput.value = product.name || '';
            categorySelect.value = product.category_id || '';
            statusSelect.value = product.status !== undefined ? product.status : '1';
            priceInput.value = product.price || '';
            descriptionInput.value = product.description || '';
        } else {
            alert('Không thể tải thông tin sản phẩm: ' + (prodRes.message || 'Lỗi không xác định'));
        }
    })
    .catch(err => {
        console.error("Lỗi khi tải thông tin sản phẩm:", err);
        alert('Có lỗi xảy ra khi nạp dữ liệu từ server!');
    });

    // 2. Gửi cập nhật bằng phương thức PUT dạng JSON
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errorAlert.classList.add('d-none');
        errorList.innerHTML = '';

        const payload = {
            name: nameInput.value.trim(),
            category_id: parseInt(categorySelect.value),
            status: parseInt(statusSelect.value),
            price: parseFloat(priceInput.value),
            description: descriptionInput.value.trim()
        };

        fetch(`/HuynhVanGiang-4733/api/products/${productId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                alert('Cập nhật sản phẩm thành công!');
                window.location.href = '/HuynhVanGiang-4733/Product';
            } else {
                errorAlert.classList.remove('d-none');
                if (res.errors) {
                    Object.values(res.errors).forEach(errText => {
                        const li = document.createElement('li');
                        li.textContent = errText;
                        errorList.appendChild(li);
                    });
                } else {
                    const li = document.createElement('li');
                    li.textContent = res.message || 'Lỗi không xác định khi cập nhật sản phẩm.';
                    errorList.appendChild(li);
                }
            }
        })
        .catch(err => {
            console.error("Lỗi cập nhật sản phẩm:", err);
            errorAlert.classList.remove('d-none');
            errorList.innerHTML = '<li>Lỗi kết nối xảy ra. Không thể gửi yêu cầu cập nhật.</li>';
        });
    });
});
</script>

<?php include_once 'app/views/shares/footer.php'; ?>