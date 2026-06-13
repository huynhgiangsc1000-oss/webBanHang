<?php 
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
                            <th>Tên sản phẩm</th>
                            <th>Trạng thái</th>
                            <th>Giá bán</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="product-list-tbody">
                        <tr><td colspan="4" class="text-center py-4">Đang tải danh sách sản phẩm...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const isAdmin = <?php echo json_encode($isAdmin); ?>;
    const tbody = document.getElementById('product-list-tbody');

    // Hàm tải danh sách sản phẩm từ API
    function loadProducts() {
        fetch('/HuynhVanGiang-4733/api/products')
            .then(response => response.json())
            .then(res => {
                if (res.success && res.data) {
                    renderProducts(res.data);
                } else {
                    tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">Không thể tải dữ liệu: ${res.message || 'Lỗi hệ thống'}</td></tr>`;
                }
            })
            .catch(err => {
                console.error("Lỗi khi tải sản phẩm:", err);
                tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-4">Có lỗi kết nối xảy ra khi tải dữ liệu!</td></tr>`;
            });
    }

    // Hàm hiển thị sản phẩm lên bảng
    function renderProducts(products) {
        if (products.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4">Chưa có sản phẩm nào.</td></tr>';
            return;
        }

        tbody.innerHTML = '';
        products.forEach(product => {
            const tr = document.createElement('tr');
            
            // Format giá tiền Việt Nam
            const priceFormatted = new Intl.NumberFormat('vi-VN').format(product.price) + ' đ';
            
            // Trạng thái kho hàng
            const statusBadge = (product.status == 0) 
                ? '<span class="badge bg-danger">Hết hàng</span>' 
                : '<span class="badge bg-success">Còn hàng</span>';

            let actionButtons = '';
            if (isAdmin) {
                actionButtons = `
                    <td class="text-center">
                        <a href="/HuynhVanGiang-4733/Product/edit/${product.id}" class="btn btn-warning btn-sm">✏️ Sửa</a>
                        <button class="btn btn-danger btn-sm btn-delete" data-id="${product.id}">🗑️ Xóa</button>
                    </td>
                `;
            } else {
                actionButtons = '<td class="text-center">-</td>';
            }

            tr.innerHTML = `
                <td class="fw-bold">${escapeHtml(product.name)}</td>
                <td>${statusBadge}</td>
                <td class="text-danger fw-bold">${priceFormatted}</td>
                ${actionButtons}
            `;
            tbody.appendChild(tr);
        });

        // Gắn sự kiện click cho các nút Xóa
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function () {
                const productId = this.getAttribute('data-id');
                if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
                    deleteProduct(productId);
                }
            });
        });
    }

    // Hàm xóa sản phẩm sử dụng Fetch API (DELETE)
    function deleteProduct(id) {
        fetch(`/HuynhVanGiang-4733/api/products/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                alert('Xóa sản phẩm thành công!');
                loadProducts(); // Tải lại danh sách
            } else {
                alert('Lỗi: ' + res.message);
            }
        })
        .catch(err => {
            console.error("Lỗi khi xóa sản phẩm:", err);
            alert('Có lỗi xảy ra khi thực hiện yêu cầu xóa!');
        });
    }

    // Hàm tránh lỗi XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    // Tải dữ liệu lần đầu tiên
    loadProducts();
});
</script>