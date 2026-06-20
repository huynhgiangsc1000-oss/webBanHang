<?php
require_once 'app/utils/JWTHandler.php';

class BaseApiController
{
    /**
     * Trả về JSON và dừng thực thi
     *
     * @param mixed $data Dữ liệu cần trả về
     * @param int $status Mã trạng thái HTTP
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit();
    }

    /**
     * Lấy token Bearer từ Header hoặc Server variables
     *
     * @return string|null
     */
    protected function getBearerToken()
    {
        $headers = null;
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                return trim($headers['Authorization']);
            } elseif (isset($headers['authorization'])) {
                return trim($headers['authorization']);
            }
        }
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            return trim($_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
        }
        return null;
    }

    /**
     * Xác thực JWT và lấy payload thông tin user
     *
     * @return object|false
     */
    protected function getAuthenticatedUser()
    {
        $authHeader = $this->getBearerToken();
        if ($authHeader && preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];
            return JWTHandler::validateToken($token);
        }
        return false;
    }

    /**
     * Bảo mật endpoint bằng JWT và phân quyền
     *
     * @param string|null $requiredRole Quyền yêu cầu (ví dụ: 'admin')
     * @return object Trả về thông tin user đã xác thực thành công
     */
    protected function protect($requiredRole = null)
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            $this->json([
                'success' => false,
                'message' => 'Yêu cầu token xác thực hợp lệ (Unauthorized).'
            ], 401);
        }

        if ($requiredRole !== null && (!isset($user->role) || strtolower($user->role) !== strtolower($requiredRole))) {
            $this->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này (Forbidden).'
            ], 403);
        }

        return $user;
    }
}
?>
