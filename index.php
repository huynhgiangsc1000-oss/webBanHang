<?php
// BẬT SESSION ĐỂ DÙNG CHO GIỎ HÀNG VÀ TÀI KHOẢN ĐĂNG NHẬP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Lấy URL hiện tại từ biến $_GET (do .htaccess cấu hình chuyển hướng về)
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// 2. XỬ LÝ ĐƯỜNG DẪN THƯ MỤC
if (isset($url[0]) && (strtolower($url[0]) == 'huynhvangiang-4733' || strtolower($url[0]) == 'project1' || strtolower($url[0]) == 'cos340' || strtolower($url[0]) == 'webbanhang')) {
    array_shift($url); 
}

// 3. XỬ LÝ CONTROLLER
if (isset($url[0]) && $url[0] != '') {
    $rawController = ucfirst($url[0]);
    
    if (strtolower($rawController) === 'products' || strtolower($rawController) === 'product') {
        $rawController = 'Product';
    }
    if (strtolower($rawController) === 'categories' || strtolower($rawController) === 'category') {
        $rawController = 'Category';
    }
    if (strtolower($rawController) === 'carts' || strtolower($rawController) === 'cart') {
        $rawController = 'Cart';
    }
    if (strtolower($rawController) === 'accounts' || strtolower($rawController) === 'account') {
        $rawController = 'Account';
    }
    
    $controllerName = $rawController . 'Controller';
} else {
    $controllerName = 'DefaultController';
}

// 4. XỬ LÝ ACTION
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// 5. KIỂM TRA FILE CONTROLLER CÓ TỒN TẠI KHÔNG
$controllerPath = __DIR__ . '/app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerPath)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Controller <b>{$controllerName}</b> không tồn tại.";
    exit;
}

// 6. NHÚNG FILE CONTROLLER
require_once $controllerPath;

// 7. KIỂM TRA VÀ KHỞI TẠO CLASS CONTROLLER
if (!class_exists($controllerName)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Class <b>{$controllerName}</b> chưa được định nghĩa.";
    exit;
}
$controller = new $controllerName();

// 8. KIỂM TRA ACTION (HÀM) CÓ TỒN TẠI KHÔNG
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Action <b>{$action}</b> không tồn tại trong controller <b>{$controllerName}</b>.";
    exit;
}

// 9. --- ĐÃ TỐI ƯU CHUẨN: TRUYỀN THAM SỐ ĐA DẠNG QUA HÀM NATIVE CỦA PHP ---
// Lấy toàn bộ các tham số đứng sau action (nếu có)
$parameters = array_slice($url, 2);
call_user_func_array([$controller, $action], $parameters);
?>