<?php

declare(strict_types=1);

include "classes/UserSave.php";

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: register.php");
    exit;
}

$storage = new UserSave("usersData/users.csv");

$users = $storage->getAll();

echo '<h2>Registered Users</h2>';

echo    '<table border="1" cellpadding="5">';
echo    '<tr>
            <th>Firstname</th>
            <th>Lastname</th>
            <th>Email</th>
        </tr>';

foreach ($users as $u) 
{
        echo    '<tr>
                    <td>' . $u[0] . '</td>
                    <td>' . $u[1] . '</td>
                    <td>' . $u[2] . '</td>
                </tr>';
};

echo '</table>';
echo '<button onclick="location.href=\'register.php\'">Go To Register</button>';
