<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Helpers\Helpers;
use App\Helpers\FlashMessage;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private UserModel $userModel;

    public function __construct(UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function showLogin()
    {
        require_once __DIR__ . '/../../views/auth/login.php';
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember']) ? true : false;

        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            // Create session as before
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']
            ];

            // Generate JWT token
            $issuedAt = time();
            $expiration = $issuedAt + intval($_ENV['JWT_EXPIRE']);
            $payload = [
                'iss' => $_ENV['APP_URL'] ?? 'localhost',
                'iat' => $issuedAt,
                'exp' => $expiration,
                'data' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ]
            ];

            $token = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

            // Optionally store token in session or send it via response
            $_SESSION['jwt_token'] = $token;

            // Set remember me cookie if checked
            $this->rememberMe($user, $remember);
            // Redirect or return JSON depending on use case
            Helpers::redirect('/');
            exit;
        } else {
            FlashMessage::set('error', 'Invalid email or password.');
            Helpers::redirect('/login');
            exit;
        }
    }

    public function showRegister()
    {
        require_once __DIR__ . '/../../views/auth/register.php';
    }

    public function register()
    {
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$username || !$email || !$password) {
            FlashMessage::set('error', 'All fields are required.');
            Helpers::redirect(path: '/register');
            exit;
        }

        if ($this->userModel->getUserByEmail($email)) {
            FlashMessage::set('error', 'Email already taken.');
            Helpers::redirect('/register');
            exit;
        }

        $this->userModel->register($name, $username, $email, $password);
        FlashMessage::set('success', 'Registration successful. Please log in.');
        Helpers::redirect('/login');
        exit;
    }

    private function generateToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length));
    }

    public function rememberMe($user, $remember = false)
    {
        if ($remember) {
            $refreshToken = $this->generateToken();
            $expiresAt = date('Y-m-d H:i:s', strtotime('+30 days'));

            // Store refresh token in DB
            $this->userModel->setRefreshToken($user['id'], $refreshToken, $expiresAt);

            // Set secure cookie with just the refresh token
            setcookie(
                'remember_me',
                $refreshToken,
                [
                    'expires' => strtotime($expiresAt),
                    'path' => '/',
                    'domain' => '',
                    'secure' => false, // Set true in production
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]
            );
        }
    }

    public function autoLogin()
    {
        if (isset($_COOKIE['remember_me'])) {
            $token = $_COOKIE['remember_me'];
            $user = $this->userModel->getUserByRememberToken($token);

            if ($user) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                return true;
            }
        }
        return false;
    }

    public function showForgotPassword()
    {
        require_once __DIR__ . '/../../views/auth/forgot-password.php';
    }

    public function handleForgotPassword()
    {
        $email = $_POST['email'] ?? '';

        if (!$email) {
            FlashMessage::set('error', 'Email is required.');
            Helpers::redirect('/forgot-password');
            exit;
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            FlashMessage::set('error', 'No account found with that email.');
            Helpers::redirect('/forgot-password');
            exit;
        }

        $token = bin2hex(random_bytes(50));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->userModel->setPasswordResetToken($user['id'], $token, $expiry);

        $resetLink = Helpers::getBaseUrl() . "/reset-password?token=$token";

        $mailer = new \App\Mailers\Mailer();

        $subject = "Password Reset Request";
        $htmlBody = '
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: auto; padding: 20px;">
                <h2 style="color: #E53E3E;">Password Reset Request</h2> 
                <p>We received a request to reset your password. Click the button below to proceed:</p>
                <a href="' . $resetLink . '" 
                style="display: inline-block; padding: 12px 24px; background-color: #E53E3E; color: white; text-decoration: none; border-radius: 5px;">
                Reset Password
                </a>
                <p style="margin-top: 20px; font-size: 0.9em; color: #555;">
                This link will expire in 1 hour. If you did not request a password reset, please ignore this email.
                </p>
            </div>
        ';

        if ($mailer->send($user['email'], $subject, $htmlBody)) {
            FlashMessage::set('success', 'A password reset link has been sent to your email.');
        } else {
            FlashMessage::set('error', 'Failed to send reset email. Please try again later.');
        }

        Helpers::redirect('/login');
        exit;
    }

    public function showResetPassword()
    {
        $token = $_GET['token'] ?? '';
        $valid = $this->userModel->checkPasswordResetToken($token);

        if (!$valid) {
            FlashMessage::set('error', 'Invalid or expired token.');
            Helpers::redirect('/forgot-password');
            exit;
        }

        require_once __DIR__ . '/../../views/auth/reset-password.php';
    }

    public function handleResetPassword()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$token || !$password) {
            FlashMessage::set('error', 'Token and password are required.');
            Helpers::redirect("/reset-password?token=$token");
            exit;
        }

        $user = $this->userModel->getUserByPasswordResetToken($token);

        if (!$user) {
            FlashMessage::set('error', 'Invalid or expired token.');
            Helpers::redirect('/forgot-password');
            exit;
        }

        $this->userModel->updatePassword($user['id'], $password);
        $this->userModel->clearPasswordResetToken($user['id']);
        FlashMessage::set('success', 'Password successfully reset.');
        Helpers::redirect('/login');
        exit;
    }

    public function logout()
    {
        session_destroy();
        Helpers::redirect('/');
        exit;
    }
}
