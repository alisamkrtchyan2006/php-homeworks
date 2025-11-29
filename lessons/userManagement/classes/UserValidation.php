<?php

declare(strict_types=1);

class UserValidation {
    public static function validate($firstname, $lastname, $email, $password): array 
    {
        $errors = [];

        if (trim($firstname) === '') $errors[] = "Firstname is required";
        if (trim($lastname) === '')  $errors[] = "Lastname is required";
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email";
        if (strlen($password) < 4) $errors[] = "Password must be at least 4 chars";

        return $errors;
    }
}
