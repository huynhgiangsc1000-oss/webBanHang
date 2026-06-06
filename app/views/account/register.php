<?php include_once 'app/views/shares/header.php'; ?>

<div class="container my-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
    <div class="card border-0 shadow-sm p-4 p-md-5 bg-white" style="border-radius: 20px; width: 100%; max-width: 480px;">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-dark mb-1">Tạo tài khoản mới</h2>
            <p class="text-muted small">Tối thiểu 8 ký tự, gồm chữ hoa, thường, số và ký tự đặc biệt.</p>
        </div>

        <form action="/HuynhVanGiang-4733/Account/register" method="POST" id="registerForm">
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Tên đăng nhập *</label>
                <input type="text" name="username" id="username" class="form-control px-3 py-2.5 rounded-3" required>
                <small class="text-danger" id="username-error"></small>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Địa chỉ Email *</label>
                <input type="email" name="email" class="form-control px-3 py-2.5 rounded-3" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary small">Mật khẩu *</label>
                <input type="password" name="password" id="password" class="form-control px-3 py-2.5 rounded-3" required>
                <small class="text-danger" id="password-error"></small>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold text-secondary small">Xác nhận mật khẩu *</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control px-3 py-2.5 rounded-3" required>
            </div>

            <button type="submit" class="btn btn-success w-100 py-2.5 rounded-pill fw-bold">Đăng ký tài khoản</button>
        </form>
    </div>
</div>

<script>
    const passwordInput = document.getElementById('password');
    const passwordError = document.getElementById('password-error');
    // Regex: 8 ký tự, có chữ hoa, chữ thường, số, ký tự đặc biệt
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    passwordInput.addEventListener('input', function() {
        if (this.value.length > 0 && !regex.test(this.value)) {
            passwordError.textContent = "Mật khẩu chưa đủ mạnh (cần 8 ký tự, chữ hoa, thường, số, ký tự đặc biệt).";
        } else {
            passwordError.textContent = "";
        }
    });
</script>

<?php include_once 'app/views/shares/footer.php'; ?>