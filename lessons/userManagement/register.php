<?php

declare(strict_types=1);

include "classes/User.php";
include "classes/UserValidation.php";
include "classes/UserSave.php";

$storage = new UserSave("usersData/users.csv");
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname  = trim($_POST["lastname"]);
    $email     = trim($_POST["email"]);
    $password  = trim($_POST["password"]);

    $errors = UserValidation::validate($firstname, $lastname, $email, $password);

    if ($storage->emailExists($email)) {
        $errors[] = "Email already exists";
    }

    if (empty($errors)) {
        $user = new User($firstname, $lastname, $email, $password);
        $storage->saveUser($user);
        header("Location: login.php");
        exit;
    }
}

echo '<h2>Register</h2>';

foreach ($errors as $e)
{
    echo '<p style="color:red">' . $e . '</p>';
};

echo '<form method="POST">
    Firstname: <input name="firstname"><br><br>
    Lastname: <input name="lastname"><br><br>
    Email: <input name="email"><br><br>
    Password: <input type="password" name="password"><br><br>
    <button type="submit">Register</button>
</form>';