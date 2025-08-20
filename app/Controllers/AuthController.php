<?php

namespace App\Controllers;

use App\Core\DotEnv;
use App\Models\User;

class AuthController 
{
    public function showLogin()
    {
        return $this->view('login');
    }
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $userModel = new User();
            $user = $userModel->findByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location:' . DotEnv::env('APP_URL') . '/users');
                exit;
            } else {
                $_SESSION['error'] = 'Invalid credentials.';
                header('Location:' . DotEnv::env('APP_URL') . '/users');
                exit;
            }
        }
        return $this->view('login');
    }
    public function showRegister()
    {
        return $this->view('register');
    }

    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $userModel = new User();
            if ($userModel->findByEmail($email)) {
                $_SESSION['error'] = 'Email already exists.';
                header('Location:' . DotEnv::env('APP_URL') . '/users');
                exit;
            }
            if ($password !== $confirm_password) {
                $_SESSION['error'] = 'Passwords do not match.';
                header('Location:' . DotEnv::env('APP_URL') . '/auth/register');
                exit;
            }
            if (strlen($password) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters.';
                header('Location:' . DotEnv::env('APP_URL') . '/auth/register');
                exit;
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $userModel->create($email, $hashedPassword);
            $_SESSION['success'] = 'Registration successful. Please login.';
            header('Location:' . DotEnv::env('APP_URL') . '/users');
            exit;
        }
        return $this->view('register');
    }
    public function logout()
    {
        // var_dump($_SESSION);
        session_destroy();
        header('Location' . DotEnv::env('APP_URL') . '/auth/login');
        exit;
    }
    private function view($view)
    {
        include __DIR__ . '/../Views/auth/' . $view . '.php';
    }
}
