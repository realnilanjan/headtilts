<?php

namespace App\Helpers;

class FlashMessage
{
    public static function get(): array
    {
        $messages = [];

        if (isset($_SESSION['flash'])) {
            $messages = $_SESSION['flash'];
            unset($_SESSION['flash']); // Clear after reading
        }

        return $messages;
    }

    public static function set(string $type, string $message): void
    {
        $_SESSION['flash'][$type][] = $message;
    }
}