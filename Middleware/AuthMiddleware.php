<?php

namespace Middleware;

class AuthMiddleware
{
    private static array $publicRoutes = [
        '/login',
        '/users',
        '/login/authenticate',
        '/users/store',
    ];

    /**
     * Check if the current path is public (doesn't require login)
     */
    public static function isPublicRoute(string $path): bool
    {
        return in_array($path, self::$publicRoutes, true);
    }

    /**
     * Check if user is logged in
     */
    public static function isAuthenticated(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login - redirect to login if not authenticated
     */
    public static function requireLogin(string $currentPath): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (self::isPublicRoute($currentPath)) {
            return;
        }

        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
}