<?php

declare(strict_types=1);

// function isValidEmail(string $email): bool {
//     return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
// }


// $email1 = "test@example.com";
// $email2 = "invalid-email";

// var_dump (isValidEmail($email1)); 
// echo '<br><br>';
// var_dump (isValidEmail($email2));



function isValidEmail(string $email): bool {

    if (strpos($email, '@') === false) return false;

    [$local, $domain] = explode('@', $email, 2);

    if (strlen($local) === 0) return false;

    if (strpos($domain, '.') === false) return false;

    [$host, $topLavelDomain] = explode('.', $domain, 2);

    if (strlen($host) === 0 || strlen($topLavelDomain) < 2) return false;

    return true;
}

$email1 = "test@example.com";
$email2 = "invalid-email";

var_dump(isValidEmail($email1));
echo '<br><br>';
var_dump(isValidEmail($email2));
