<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $age = isset($_POST['age']) ? (int)$_POST['age'] : 0;

    if ($age < 18) {
        $message = "You are underage";
    } elseif ($age > 60) {
        $message = "You are old";
    } else {
        $message = "Access allowed";
    }
}

echo '<form method="post">';
echo '<label for="age">Enter your age</label> ';
echo '<input type="number" name="age" id="age" required> ';
echo '<button type="submit">Submit</button>';
echo '</form>';

if ($message) {
    echo '<p>' . $message . '</p>';
}
