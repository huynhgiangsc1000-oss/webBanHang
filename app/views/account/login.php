<?php include_once 'app/views/shares/header.php'; ?>

<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="card border-0 shadow-sm p-4 p-md-5 bg-white" style="border-radius: 20px; width: 100%; max-width: 450px;">
        
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.03rem;">Đăng nhập My Store.</h2>
            <p class="text-muted small">Quản lý đơn hàng và lưu địa chỉ mua sắm thông minh.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger border-0 small py-2 rounded-3 text-center mb-3">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="/HuynhVanGiang-4733/Account/login" method="POST">
            
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Tên đăng nhập hoặc Email</label>
                <input type="text" name="username" class="form-control px-3 py-2.5 rounded-3" 
                       placeholder="Nhập tên tài khoản của bạn" required 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label fw-semibold text-secondary small mb-0">Mật khẩu</label>
                    <a href="#" class="text-decoration-none small text-muted">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" class="form-control px-3 py-2.5 rounded-3" 
                       placeholder="Nhập mật khẩu" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold mb-3" style="background-color: #0071e3; border: none;">
                Đăng nhập
            </button>

            <div class="text-center mt-4">
                <span class="text-muted small">Bạn chưa có tài khoản Apple ID? </span>
                <a href="/HuynhVanGiang-4733/Account/register" class="text-primary text-decoration-none small fw-semibold">
                    Tạo tài khoản mới ngay
                </a>
            </div>
        </form>
    </div>
</div>

<?php include_once 'app/views/shares/footer.php'; ?>