<?php
// BẬT SESSION ĐỂ DÙNG CHO GIỎ HÀNG VÀ TÀI KHOẢN ĐĂNG NHẬP
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// NẠP COMPOSER AUTOLOADER
require_once __DIR__ . '/vendor/autoload.php';


// 1. Lấy URL hiện tại từ biến $_GET (do .htaccess cấu hình chuyển hướng về)
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// 2. XỬ LÝ ĐƯỜNG DẪN THƯ MỤC
if (isset($url[0]) && (strtolower($url[0]) == 'huynhvangiang-4733' || strtolower($url[0]) == 'project1' || strtolower($url[0]) == 'cos340' || strtolower($url[0]) == 'webbanhang')) {
    array_shift($url); 
}

// =====================================================================
// 3. XỬ LÝ API ROUTER (Tiền tố /api/)
// =====================================================================
if (isset($url[0]) && strtolower($url[0]) === 'api') {
    // Tất cả response của API đều là JSON
    header('Content-Type: application/json; charset=utf-8');

    $apiResource = strtolower($url[1] ?? '');  // products | categories
    if ($apiResource === 'product') {
        $apiResource = 'products';
    }
    if ($apiResource === 'category') {
        $apiResource = 'categories';
    }
    if ($apiResource === 'order') {
        $apiResource = 'orders';
    }
    $resourceId  = $url[2] ?? null;             // /{id} nếu có
    $httpMethod  = strtoupper($_SERVER['REQUEST_METHOD']);

    // Hàm trợ giúp trả về JSON lỗi cho API router
    $apiError = function(int $code, string $message) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
        exit();
    };

    // ---- ĐỊNH TUYẾN CHO /api/products ----
    if ($apiResource === 'products') {
        require_once __DIR__ . '/app/controllers/ProductApiController.php';
        $controller = new ProductApiController();

        if ($resourceId !== null) {
            // /api/products/{id}
            switch ($httpMethod) {
                case 'GET':
                    $controller->show($resourceId);
                    break;
                case 'PUT':
                case 'PATCH':
                    $controller->update($resourceId);
                    break;
                case 'DELETE':
                    $controller->destroy($resourceId);
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho endpoint này. Chỉ chấp nhận: GET, PUT, DELETE.");
            }
        } else {
            // /api/products
            switch ($httpMethod) {
                case 'GET':
                    $controller->index();
                    break;
                case 'POST':
                    $controller->store();
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho endpoint này. Chỉ chấp nhận: GET, POST.");
            }
        }
        exit();
    }

    // ---- ĐỊNH TUYẾN CHO /api/categories ----
    if ($apiResource === 'categories') {
        require_once __DIR__ . '/app/controllers/CategoryApiController.php';
        $controller = new CategoryApiController();

        if ($resourceId !== null) {
            // /api/categories/{id}
            switch ($httpMethod) {
                case 'DELETE':
                    $controller->destroy($resourceId);
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho danh mục này. Chỉ chấp nhận: DELETE.");
            }
        } else {
            // /api/categories
            switch ($httpMethod) {
                case 'GET':
                    $controller->index();
                    break;
                case 'POST':
                    $controller->store();
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho danh mục này. Chỉ chấp nhận: GET, POST.");
            }
        }
        exit();
    }

    // ---- ĐỊNH TUYẾN CHO /api/orders ----
    if ($apiResource === 'orders') {
        require_once __DIR__ . '/app/controllers/OrderApiController.php';
        $controller = new OrderApiController();

        if ($resourceId !== null) {
            // /api/orders/{id}
            switch ($httpMethod) {
                case 'GET':
                    $controller->show($resourceId);
                    break;
                case 'PUT':
                case 'PATCH':
                    $controller->update($resourceId);
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho đơn hàng này. Chỉ chấp nhận: GET, PUT.");
            }
        } else {
            // /api/orders
            switch ($httpMethod) {
                case 'POST':
                    $controller->store();
                    break;
                default:
                    $apiError(405, "Phương thức HTTP '$httpMethod' không được hỗ trợ cho endpoint này. Chỉ chấp nhận: POST.");
            }
        }
        exit();
    }

    // Không tìm thấy API resource phù hợp
    $apiError(404, "API endpoint '/api/{$apiResource}' không tồn tại.");
}
// =====================================================================
// KẾT THÚC API ROUTER
// =====================================================================

// 4. XỬ LÝ CONTROLLER (MVC thông thường)
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
    if (strtolower($rawController) === 'productapi' || strtolower($rawController) === 'product-api') {
        $rawController = 'ProductApi';
    }
    if (strtolower($rawController) === 'categoryapi' || strtolower($rawController) === 'category-api') {
        $rawController = 'CategoryApi';
    }
    
    $controllerName = $rawController . 'Controller';
} else {
    $controllerName = 'DefaultController';
}

// 5. XỬ LÝ ACTION
$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// 6. KIỂM TRA FILE CONTROLLER CÓ TỒN TẠI KHÔNG
$controllerPath = __DIR__ . '/app/controllers/' . $controllerName . '.php';
if (!file_exists($controllerPath)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Controller <b>{$controllerName}</b> không tồn tại.";
    exit;
}

// 7. NHÚNG FILE CONTROLLER
require_once $controllerPath;

// 8. KIỂM TRA VÀ KHỞI TẠO CLASS CONTROLLER
if (!class_exists($controllerName)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Class <b>{$controllerName}</b> chưa được định nghĩa.";
    exit;
}
$controller = new $controllerName();

// 9. KIỂM TRA ACTION (HÀM) CÓ TỒN TẠI KHÔNG
if (!method_exists($controller, $action)) {
    header("HTTP/1.0 404 Not Found");
    echo "<h3>404 Not Found</h3>";
    echo "Action <b>{$action}</b> không tồn tại trong controller <b>{$controllerName}</b>.";
    exit;
}

// 10. --- TRUYỀN THAM SỐ ĐA DẠNG QUA HÀM NATIVE CỦA PHP ---
$parameters = array_slice($url, 2);
call_user_func_array([$controller, $action], $parameters);
?>