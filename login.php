<?php 

require_once 'db.php';
require_once 'vendor/autoload.php';



use firebase\JWT\JWT;

$jwtConfig = require 'config/jwt.php';

$error = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');


    $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {


        $issuedAt = time();

        $payload = [
            'iat' => $issuedAt,
            'exp' => $issuedAt + $jwtConfig['expiration'],
            'admin_id' => $admin['id'],
            'email' =>$admin['email']
        ];

        $token = JWT::encode(
            $payload,
            $jwtConfig['secret'],
            $jwtConfig['algorithm']
        );

        setcookie('admin_token', $token, [
            'expires' => $payload['exp'],
            'path' => '/',
            'httponly' => true,
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax'
        ]);

        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Invalid email or password.';
    }

}















?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="login_style.css">
</head>
<body>
  <div class="container">
      <h2>Admin Login</h2>

    <form>
        <label>Email :</label>
        <input
            type = "email"
            name = "email"
            required
        >

        <label>Password :</label>
        <input 
            type = "password"
            name = "password"
            required    
        >

        <button type = "submit">Login</button>
    </form>
  </div>
</body>
</html>