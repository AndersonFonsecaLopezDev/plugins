<?php

// Cargar autoload de Composer para JWT
require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
  
class LU_JWT {
    private static $secret_key = 'TU_CLAVE_SUPER_SECRETA_123!@#'; 
    private static $expiration_time = 3600; // 1 hora
    private static $algo = 'HS256';

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
        return JWT::encode($payload, self::$secret_key, self::$algo);
    }

    // Validar token
    public static function validar_token($token) {
        try {
            return JWT::decode($token, new Key(self::$secret_key, self::$algo));
        } catch (Exception $e) {
            return false;
        }
    }
}
