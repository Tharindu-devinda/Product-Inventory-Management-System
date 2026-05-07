<?php

namespace Middleware;

class AuthMiddleware
{
    // List of public routes that don't require login
    private static array $publicRoutes = [
        '/login',
        '/users',  // register page
        '/login/authenticate',  // authenticate endpoint
        '/users/store',  // register store endpoint
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
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // If route is public, allow access
        if (self::isPublicRoute($currentPath)) {
            return;
        }

        // If route is protected and user not logged in, redirect to login
        if (!self::isAuthenticated()) {
            header('Location: /login');
            exit;
        }
    }
}