<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark mb-0">Hệ Thống Thống Kê Quản Trị</h2>
        <a href="/HuynhVanGiang-4733/Order/list" class="btn btn-primary rounded-pill px-4" style="background-color: #0071e3; border: none;">
            <i class="fa-solid fa-list-check me-2"></i>Quản lý Đơn hàng
        </a>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-success bg-opacity-10 rounded-3 text-success me-3">
                        <i class="fa-solid fa-money-bill-trend-up fa-2xl"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1 fw-medium">TỔNG DOANH THU</p>
                        <h3 class="fw-bold text-dark mb-0"><?php echo number_format($totalRevenue, 0, ',', '.'); ?>đ</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-3 text-primary me-3">
                        <i class="fa-solid fa-box-open fa-2xl"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1 fw-medium">ĐƠN HÀNG HOÀN THÀNH</p>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $totalOrders; ?> đơn</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-radius: 15px;">
                <div class="d-flex align-items-center">
                    <div class="p-3 bg-warning bg-opacity-10 rounded-3 text-warning me-3">
                        <i class="fa-solid fa-users fa-2xl"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1 fw-medium">KHÁCH HÀNG ĐĂNG KÝ</p>
                        <h3 class="fw-bold text-dark mb-0"><?php echo $totalUsers; ?> tài khoản</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm p-4 bg-white mb-4" style="border-radius: 20px;">
        <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-line me-2 text-primary"></i>Biểu đồ tăng trưởng doanh thu (7 ngày qua)</h5>
        <div style="width: 100%; height: 350px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Nhận dữ liệu mảng thô từ PHP ép kiểu mã hóa thành JSON để Javascript đọc
    const rawData = <?php echo json_encode($chartData); ?>;
    
    // Tách nhãn ngày và mảng số tiền ra làm 2 mảng độc lập
    const labels = rawData.map(item => item.order_date);
    const dataValues = rawData.map(item => item.daily_revenue);

    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'line', // Kiểu biểu đồ đường mượt mà
        data: {
            labels: labels,
            datasets: [{
                label: 'Doanh thu thực tế (đ)',
                data: dataValues,
                borderColor: '#0071e3',
                backgroundColor: 'rgba(0, 113, 227, 0.1)',
                borderWidth: 3,
                tension: 0.3, // Độ cong mượt của đường nét kẻ đồ thị
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>