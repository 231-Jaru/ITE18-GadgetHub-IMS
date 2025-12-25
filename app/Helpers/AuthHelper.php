<?php

namespace App\Helpers;

class AuthHelper
{
    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool
    {
        return (session('api_token') !== null) || (session('user_id') !== null);
    }

    /**
     * Check if user is admin
     */
    public static function isAdmin(): bool
    {
        return session('user_type') === 'admin';
    }

    /**
     * Check if user is buyer
     */
    public static function isBuyer(): bool
    {
        return session('user_type') === 'buyer';
    }

    /**
     * Get current user ID
     */
    public static function getUserId(): ?int
    {
        return session('user_id');
    }

    /**
     * Get current user name
     */
    public static function getUserName(): ?string
    {
        return session('user_name');
    }

    /**
     * Get current user type
     */
    public static function getUserType(): ?string
    {
        return session('user_type');
    }

    /**
     * Get current user role
     */
    public static function getUserRole(): ?string
    {
        return session('user_role');
    }

    /**
     * Check if session is still valid (not expired)
     */
    public static function isSessionValid(): bool
    {
        if (!self::isAuthenticated()) {
            return false;
        }

        $lastActivity = session('last_activity');
        if (!$lastActivity) {
            return false;
        }

        // Check if session has timed out (2 hours)
        return now()->diffInMinutes($lastActivity) <= 120;
    }

    /**
     * Get login time
     */
    public static function getLoginTime(): ?string
    {
        return session('login_time');
    }

    /**
     * Get last activity time
     */
    public static function getLastActivity(): ?string
    {
        return session('last_activity');
    }
}
