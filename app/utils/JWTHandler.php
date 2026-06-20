<?php
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTHandler
{
    private static $secret_key = "ntech_store_secret_key_2026_jwt_token_auth_system_random_chars";
    private static $algorithm = "HS256";
    private static $issuer = "NTECH_STORE";

    /**
     * Tạo token JWT từ thông tin người dùng
     *
     * @param int $userId ID người dùng
     * @param string $username Tên đăng nhập
     * @param string $role Quyền (admin/user)
     * @param int $expirySeconds Thời hạn token (mặc định 2 tiếng)
     * @return string Token JWT
     */
    public static function generateToken($userId, $username, $role, $expirySeconds = 7200)
    {
        $issuedAt = time();
        $expireAt = $issuedAt + $expirySeconds;

        $payload = [
            'iss'  => self::$issuer,
            'iat'  => $issuedAt,
            'exp'  => $expireAt,
            'data' => [
                'id'       => $userId,
                'username' => $username,
                'role'     => $role
            ]
        ];

        return JWT::encode($payload, self::$secret_key, self::$algorithm);
    }

    /**
     * Xác thực JWT và trả về thông tin data (object) hoặc false nếu không hợp lệ
     *
     * @param string $token Token JWT
     * @return object|false
     */
    public static function validateToken($token)
    {
        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$algorithm));
            return $decoded->data ?? $decoded;
        } catch (\Exception $e) {
            return false;
        }
    }
}
?>
