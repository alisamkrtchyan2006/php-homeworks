<?php

function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


$email1 = "test@example.com";
$email2 = "invalid-email";

var_dump (isValidEmail($email1)); 
echo '<br><br>';
var_dump (isValidEmail($email2));
