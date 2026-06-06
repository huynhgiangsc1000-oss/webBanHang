<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Quản Lý Đơn Hàng Hệ Thống</h2>
            <p class="text-muted small mb-0">Xem lịch sử đặt hàng và xử lý quy trình vận đơn khách hàng.</p>
        </div>
        <a href="/HuynhVanGiang-4733/Order" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="fa-solid fa-chart-pie me-2"></i>Xem Thống Kê
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Mã đơn</th>
                        <th class="py-3">Tên Khách hàng</th>
                        <th class="py-3">Ngày đặt đơn</th>
                        <th class="py-3">Tổng giá trị</th>
                        <th class="py-3">Trạng thái hiện tại</th>
                        <th class="py-3 text-end pe-4">Thao tác xử lý</th>
                    </tr>
                </thead>
                <tbody class="small text-dark">
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Chưa có đơn hàng nào được đặt trong hệ thống!</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="javascript:void(0);" onclick="showOrderDetails(<?php echo $order->id; ?>)" class="fw-bold text-primary text-decoration-none">
                                        <i class="fa-solid fa-eye me-1"></i>#ORD-<?php echo $order->id; ?>
                                    </a>
                                </td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($order->customer_name); ?></td>
                                <td class="text-muted"><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                                <td class="fw-bold text-danger"><?php echo number_format($order->total_price, 0, ',', '.'); ?>đ</td>
                                <td>
                                    <?php if ($order->status == 'Chờ xử lý'): ?>
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">Chờ xử lý</span>
                                    <?php elseif ($order->status == 'Đang giao'): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2 rounded-pill">Đang giao</span>
                                    <?php elseif ($order->status == 'Đã giao'): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Đã giao</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Đã hủy</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4">
                                    <form action="/HuynhVanGiang-4733/Order/updateStatus" method="POST" class="d-inline-flex align-items-center gap-2">
                                        <input type="hidden" name="order_id" value="<?php echo $order->id; ?>">
                                        <select name="status" class="form-select form-select-sm rounded-3" style="width: 130px;">
                                            <option value="Chờ xử lý" <?php echo $order->status == 'Chờ xử lý' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                            <option value="Đang giao" <?php echo $order->status == 'Đang giao' ? 'selected' : ''; ?>>Đang giao</option>
                                            <option value="Đã giao" <?php echo $order->status == 'Đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                                            <option value="Đã hủy" <?php echo $order->status == 'Đã hủy' ? 'selected' : ''; ?>>Đã hủy</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-dark rounded-3 px-2.5">Duyệt</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header bg-dark text-white" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                <h5 class="modal-title fw-bold" id="modalTitle">Chi Tiết Sản Phẩm Đã Mua</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="text-secondary small">
                            <tr>
                                <th>Hình ảnh</th>
                                <th>Tên Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Đơn giá lúc mua</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                            </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-modal="modal" data-bs-dismiss="modal">Đóng cửa sổ</button>
            </div>
        </div>
    </div>
</div>

<script>
function showOrderDetails(orderId) {
    // Sét tiêu đề popup kèm mã đơn hàng
    document.getElementById('modalTitle').innerText = 'Chi Tiết Sản Phẩm Đã Mua (Đơn hàng #' + orderId + ')';
    
    const tableBody = document.getElementById('detailTableBody');
    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang tải dữ liệu sản phẩm...</td></tr>';
    
    // Khởi tạo và mở Modal của Bootstrap
    const myModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    myModal.show();

    // Gọi API AJAX lấy dữ liệu chi tiết
    fetch('/HuynhVanGiang-4733/Order/getDetailsAjax?order_id=' + orderId)
        .then(response => response.json())
        .then(data => {
            tableBody.innerHTML = ''; // Xóa dòng chữ đang tải đi
            
            if (data.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted py-3">Không tìm thấy dữ liệu chi tiết sản phẩm.</td></tr>';
                return;
            }

            // Duyệt mảng sản phẩm trả về và đắp vào bảng dữ liệu
            data.forEach(item => {
                const price = parseInt(item.price);
                const quantity = parseInt(item.quantity);
                const subtotal = price * quantity;

                // Định dạng hiển thị tiền VNĐ
                const formattedPrice = price.toLocaleString('vi-VN') + 'đ';
                const formattedSubtotal = subtotal.toLocaleString('vi-VN') + 'đ';
                
                // Đường dẫn ảnh sản phẩm (Giang tự chỉnh lại thư mục chứa ảnh nếu cần nhé)
                const imgUrl = item.product_image ? '/HuynhVanGiang-4733/public/uploads/' + item.product_image : 'https://placehold.co/60x60?text=No+Image';

                const row = `
                    <tr>
                        <td><img src="${imgUrl}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='https://placehold.co/50x60?text=Product'"></td>
                        <td class="fw-semibold">${item.product_name}</td>
                        <td class="text-center fw-bold">${quantity}</td>
                        <td class="text-end text-muted">${formattedPrice}</td>
                        <td class="text-end fw-bold text-danger">${formattedSubtotal}</td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });
        })
        .catch(error => {
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center text-danger py-3">Lỗi kết nối hệ thống không thể lấy dữ liệu!</td></tr>';
            console.error('Error:', error);
        });
}
</script>