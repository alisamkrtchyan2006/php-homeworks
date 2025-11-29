<?php
declare(strict_types=1);

include "classes/UserSave.php";
session_start();

$saved = new UserSave("usersData/users.csv");

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($email === '') {
        $errors[] = "Email is required";
    }
    if ($password === '') {
        $errors[] = "Password is required";
    }

    if (empty($errors)) {
        if ($saved->checkLogin($email, $password)) {
            $_SESSION["user"] = $email;

            header("Location: index.php");

            exit;
        } else {
            $errors[] = "Incorrect email or password";
        }
    }
}

echo '<h2>Login</h2>';

if (!empty($errors)){
    foreach ($errors as $e){
        echo '<p style="color:red">' . $e . '</p>';
    }
}

echo '<form method="POST">
    Email: <input type="text" name="email"><br><br>
    Password: <input type="password" name="password"><br><br>
    <button type="submit">Login</button>
</form>';
