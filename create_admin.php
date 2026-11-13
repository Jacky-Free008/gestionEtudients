<?php 

require_once 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format";
    } else if (strlen($password) < 8) {
        $message = "Password must be at least 8 characters";
    } else {

        $check = $pdo->prepare(
            "SELECT id FROM admins WHERE email = :email"
        );
        $check->execute(['email' => $email]);

        if ($check->fetch()) {
            $message = "Email already exists";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO admins (email, password)
                 VALUES (:email, :password)"
            );

            $stmt->execute([
                'email' => $email,
                'password' => $hashedPassword
            ]);

            $message = "Admin Created Successfuly";

        }
    }
    



}





?>















<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin</title>
</head>
<body>
    <h2>Create Admin</h2>


    <form method="POST">

        <label>Email :</label>
        <input type="email" name="email" required>

        <br><br>
        <label>Password :</label>
        <input type="password" name="password" required>

        <br><br>
        <button type="submit">Create Admin</button>

    </form>
</body>
</html>