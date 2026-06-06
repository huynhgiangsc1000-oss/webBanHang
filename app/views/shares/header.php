<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Hệ Thống Quản Lý Store'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f7; color: #1d1d1f; }
        .nav-link { font-weight: 500; transition: all 0.2s ease; }
        .nav-link:hover { color: #ffc107 !important; }
        .dropdown-menu { border-radius: 12px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm mb-4 sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold text-uppercase text-warning" href="/HuynhVanGiang-4733/">
            <i class="fa-solid fa-store me-2"></i>My Store
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/HuynhVanGiang-4733/Product">
                        <i class="fa-solid fa-box-open me-1"></i> Sản phẩm
                    </a>
                </li>
                
                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/HuynhVanGiang-4733/Order">
                            <i class="fa-solid fa-chart-pie me-1"></i> Thống Kê
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/HuynhVanGiang-4733/Order/list">
                            <i class="fa-solid fa-list-check me-1"></i> Đơn Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/HuynhVanGiang-4733/Product/add">
                            <i class="fa-solid fa-circle-plus me-1"></i> Thêm sản phẩm
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-tags me-1"></i> Quản lý danh mục
                        </a>
                        <ul class="dropdown-menu shadow border-0" aria-labelledby="categoryDropdown">
                            <li>
                                <a class="dropdown-item py-2" href="/HuynhVanGiang-4733/Category">
                                    <i class="fa-solid fa-list-ul me-2 text-primary"></i> Xem danh mục
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item py-2" href="/HuynhVanGiang-4733/Category/add">
                                    <i class="fa-solid fa-plus me-2 text-success"></i> Thêm danh mục mới
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center gap-2">
    <!-- Nút Đơn hàng mới thêm -->
    <li class="nav-item">
        <a class="nav-link btn btn-outline-light btn-sm rounded-pill px-3 text-white border-secondary me-2" href="/HuynhVanGiang-4733/Order/myOrders">
            <i class="fa-solid fa-box me-1 text-warning"></i> Đơn hàng
        </a>
    </li>

    <!-- Nút Giỏ hàng cũ -->
    <li class="nav-item">
        <a class="nav-link btn btn-outline-light btn-sm rounded-pill px-3 text-white border-secondary position-relative me-2" href="/HuynhVanGiang-4733/Cart">
            <i class="fa-solid fa-cart-shopping me-1 text-warning"></i> Giỏ hàng
            <?php if(!empty($_SESSION['cart'])): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?php echo count($_SESSION['cart']); ?>
                </span>
            <?php endif; ?>
        </a>
    </li>

                <?php if(isset($_SESSION['username'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-warning border border-secondary rounded-pill px-3 py-1 small fw-medium" href="#" id="userMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-circle-user me-1 text-light"></i> Chào, <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" aria-labelledby="userMenu">
                            <li>
                                <a class="dropdown-item py-2 text-danger fw-medium" href="/HuynhVanGiang-4733/Account/logout">
                                    <i class="fa-solid fa-power-off me-2"></i>Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-medium me-1" href="/HuynhVanGiang-4733/Account/login">Đăng nhập</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-sm btn-warning text-dark rounded-pill px-3 fw-semibold" href="/HuynhVanGiang-4733/Account/register">Đăng ký</a>
                    </li>
                <?php endif; ?>
                
                <span class="navbar-text text-light bg-secondary px-3 py-1 rounded-pill small fw-medium ms-2">
                    Huỳnh Văn Giang - 4733
                </span>
            </ul>
        </div>
    </div>
</nav>