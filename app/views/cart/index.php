<?php include_once 'app/views/shares/header.php'; ?>

<div class="container my-5">
    <h2 class="fw-bold mb-4" style="letter-spacing: -0.03rem;">Xem giỏ hàng của bạn.</h2>

    <?php if (!empty($cart)): ?>
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
                    <h5 class="fw-bold mb-4 text-secondary">Các món hàng đã chọn</h5>
                    
                    <?php 
                    $total = 0;
                    foreach ($cart as $id => $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <div class="row align-items-center mb-4 pb-3 border-bottom">
                            <div class="col-3 col-md-2">
                                <img src="/HuynhVanGiang-4733/uploads/<?php echo $item['image']; ?>" class="img-fluid rounded-3 bg-light p-1" style="max-height: 70px; object-fit: contain;">
                            </div>
                            <div class="col-9 col-md-4">
                                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <span class="text-muted small">Đơn giá: <?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                            </div>
                            <div class="col-6 col-md-3 my-2 my-md-0">
                                <div class="d-flex align-items-center">
                                    <a href="/HuynhVanGiang-4733/Cart/decrease/<?php echo $id; ?>" class="btn btn-sm btn-outline-secondary rounded-circle px-2 py-0 fw-bold">-</a>
                                    <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                    <a href="/HuynhVanGiang-4733/Cart/increase/<?php echo $id; ?>" class="btn btn-sm btn-outline-secondary rounded-circle px-2 py-0 fw-bold">+</a>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 text-end">
                                <span class="fw-bold d-block text-dark mb-1"><?php echo number_format($subtotal, 0, ',', '.'); ?>đ</span>
                                <a href="/HuynhVanGiang-4733/Cart/remove/<?php echo $id; ?>" class="text-danger small text-decoration-none" onclick="return confirm('Xóa món này?');">Xóa</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm p-4 bg-white mb-4" style="border-radius: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-location-dot text-danger me-2"></i>Địa chỉ nhận hàng</h5>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <button type="button" id="btn-edit-address" class="btn btn-link p-0 text-decoration-none text-primary fw-semibold small">
                                <i class="fa-regular fa-pen-to-square"></i> Chỉnh sửa
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if(!isset($_SESSION['user_id'])): ?>
                        <div class="alert alert-warning small border-0 p-2 mb-0 rounded-3">
                            <i class="fa-solid fa-circle-info me-1"></i> Vui lòng <a href="/HuynhVanGiang-4733/Account/login" class="fw-bold text-dark">Đăng nhập</a> để hệ thống tự động lưu và nhớ địa chỉ cho lần sau.
                        </div>
                    <?php endif; ?>

                    <div id="address-display-box" class="bg-light p-3 rounded-3 mt-2">
                        <p class="mb-1 small"><strong>SĐT:</strong> <span id="lbl-phone"><?php echo !empty($user_phone) ? htmlspecialchars($user_phone) : 'Chưa cập nhật'; ?></span></p>
                        <p class="mb-0 small text-secondary"><strong>Địa chỉ:</strong> <span id="lbl-address"><?php echo !empty($user_address) ? htmlspecialchars($user_address) : 'Vui lòng bấm nút chỉnh sửa để thêm địa chỉ giao hàng.'; ?></span></p>
                    </div>

                    <div id="address-edit-form" class="d-none mt-3">
                        <div class="mb-2">
                            <input type="tel" id="txt-phone" class="form-control form-control-sm rounded-3" placeholder="Nhập số điện thoại mới" value="<?php echo htmlspecialchars($user_phone); ?>">
                        </div>
                        <div class="mb-2">
                            <textarea id="txt-address" class="form-control form-control-sm rounded-3" rows="2" placeholder="Số nhà, tên đường, phường/xã..."><?php echo htmlspecialchars($user_address); ?></textarea>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" id="btn-cancel-address" class="btn btn-sm btn-light rounded-pill px-3">Hủy</button>
                            <button type="button" id="btn-save-address" class="btn btn-sm btn-primary rounded-pill px-3">Lưu lại luôn</button>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm p-4" style="background-color: #f5f5f7; border-radius: 16px;">
                    <h5 class="fw-bold mb-3">Tóm tắt đơn hàng</h5>
                    <div class="d-flex justify-content-between mb-2 small text-secondary">
                        <span>Tạm tính sản phẩm:</span>
                        <span class="fw-medium text-dark"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3 small text-secondary">
                        <span>Phí giao hàng:</span>
                        <span class="text-success fw-medium">Miễn phí toàn quốc</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="fw-bold fs-5">Tổng tiền:</span>
                        <span class="fw-bold fs-4 text-danger"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                    </div>

                    <form action="/HuynhVanGiang-4733/Cart/checkout" method="POST">
                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'Khách vãng lai'); ?>">
                        <input type="hidden" id="final-phone" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>">
                        <input type="hidden" id="final-address" name="address" value="<?php echo htmlspecialchars($user_address); ?>">

                        <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold" style="background-color: #0071e3; border: none;">
                            🛍️ Xác Nhận Thanh Toán Ngay
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <i class="fa-solid fa-bag-shopping display-2 text-muted mb-3" style="opacity: 0.3;"></i>
            <h5 class="text-secondary fw-bold">Giỏ hàng trống trơn!</h5>
            <a href="/HuynhVanGiang-4733/Product" class="btn btn-sm btn-primary rounded-pill px-4 mt-2">Bắt đầu mua sắm</a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const btnEdit = document.getElementById("btn-edit-address");
    const btnCancel = document.getElementById("btn-cancel-address");
    const btnSave = document.getElementById("btn-save-address");
    
    const displayBox = document.getElementById("address-display-box");
    const editForm = document.getElementById("address-edit-form");

    if(btnEdit) {
        // Khi ấn nút "Chỉnh sửa" -> Ẩn chữ hiển thị, Hiện form nhập
        btnEdit.addEventListener("click", function () {
            displayBox.classList.add("d-none");
            editForm.classList.remove("d-none");
        });

        // Khi ấn "Hủy" -> Đóng form nhập, hiện lại chữ cũ
        btnCancel.addEventListener("click", function () {
            editForm.classList.add("d-none");
            displayBox.classList.remove("d-none");
        });

        // Khi ấn "Lưu lại luôn" -> Gửi AJAX lên lưu vào database ngay lập tức
        btnSave.addEventListener("click", function () {
            const phoneVal = document.getElementById("txt-phone").value.trim();
            const addressVal = document.getElementById("txt-address").value.trim();

            if(phoneVal === "" || addressVal === "") {
                alert("Vui lòng không để trống số điện thoại và địa chỉ!");
                return;
            }

            // Tạo đối tượng dữ liệu gửi đi
            let formData = new FormData();
            formData.append("phone", phoneVal);
            formData.append("address", addressVal);

            // Gọi AJAX gửi ngầm lên CartController/saveAddress
            fetch("/HuynhVanGiang-4733/Cart/saveAddress", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === "success") {
                    // Cập nhật lại giao diện text bên ngoài trực quan luôn
                    document.getElementById("lbl-phone").innerText = phoneVal;
                    document.getElementById("lbl-address").innerText = addressVal;
                    
                    // Cập nhật giá trị vào form ẩn để phục vụ nút Xác nhận thanh toán
                    document.getElementById("final-phone").value = phoneVal;
                    document.getElementById("final-address").value = addressVal;

                    // Thu form lại về trạng thái hiển thị
                    editForm.classList.add("d-none");
                    displayBox.classList.remove("d-none");
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error("Lỗi:", error);
                alert("Đã xảy ra lỗi kết nối mạng!");
            });
        });
    }
});
</script>

<?php include_once 'app/views/shares/footer.php'; ?>