<?php

namespace App\Support;

class JwtToken
{
    public static function issue(int $userId, string $email): string
    {
        $algorithm = config('app.jwt_algorithm', env('JWT_ALGORITHM', 'HS256'));
        $secret = (string) config('app.jwt_secret', env('JWT_SECRET', ''));

        if ($algorithm !== 'HS256' || $secret === '') {
            return '';
        }

        $ttl = (int) config('app.jwt_ttl', env('JWT_TTL', 3600));
        $now = time();

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'sub' => $userId,
            'email' => $email,
            'iat' => $now,
            'exp' => $now + max(1, $ttl),
            'iss' => config('app.url'),
        ];

        $encodedHeader = self::base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $encodedPayload = self::base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES));

        $signature = hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $secret, true);

        return $encodedHeader.'.'.$encodedPayload.'.'.self::base64UrlEncode($signature);
    }

    public static function validate(string $token): bool
    {
        $algorithm = config('app.jwt_algorithm', env('JWT_ALGORITHM', 'HS256'));
        $secret = (string) config('app.jwt_secret', env('JWT_SECRET', ''));

        if ($algorithm !== 'HS256' || $secret === '') {
            return false;
        }

        $parts = explode('.', $token);

        if (count($parts) !== 3) {
            return false;
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $header = json_decode((string) self::base64UrlDecode($encodedHeader), true);
        $payload = json_decode((string) self::base64UrlDecode($encodedPayload), true);
        $signature = self::base64UrlDecode($encodedSignature);

        if (! is_array($header) || ! is_array($payload) || ! is_string($signature)) {
            return false;
        }

        if (($header['alg'] ?? null) !== 'HS256') {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $secret, true);

        if (! hash_equals($expectedSignature, $signature)) {
            return false;
        }

        return is_int($payload['exp'] ?? null) && $payload['exp'] > time();
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string|false
    {
        $padding = 4 - (strlen($data) % 4);

        if ($padding < 4) {
            $data .= str_repeat('=', $padding);
        }

        return base64_decode(strtr($data, '-_', '+/'), true);
    }
}
