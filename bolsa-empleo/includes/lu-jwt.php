<?php

// Cargar autoload de Composer para JWT
require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
  
class LU_JWT {
    private static $expiration_time = 3600; // 1 hora
    private static $algo = 'HS256';

    private static function get_secret() {
        return defined('LOGGED_IN_SALT') ? LOGGED_IN_SALT : (defined('AUTH_KEY') ? AUTH_KEY : 'aess_secure_jwt_key_fallback');
    }

    // Generar token
    public static function generar_token($user) {
        $time = time();
        $payload = [
            'iat'  => $time,
            'exp'  => $time + self::$expiration_time,
            'data' => [
                'id'    => $user->id,
                'email' => $user->email,
                'role'  => $user->user_role ?? 'estudiante'
            ]
        ];
        return JWT::encode($payload, self::get_secret(), self::$algo);
    }

    // Validar token
    public static function validar_token($token) {
        try {
            return JWT::decode($token, new Key(self::get_secret(), self::$algo));
        } catch (Exception $e) {
            return false;
        }
    }
}
