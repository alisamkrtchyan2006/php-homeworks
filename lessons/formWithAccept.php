<?php
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $age = isset($_POST['age']) ? trim($_POST['age']) : '';
    $accept = isset($_POST['accept']) ? $_POST['accept'] : '';

    if ($accept !== 'on') {
        $message = "Error: You must accept the terms";
    } else {
        if ($name === '' && $age === '') {
            $message = "Please fill in name and age fields";
        } elseif ($name === '') {
            $message = "Please fill in the name field";
        } elseif ($age === '') {
            $message = "Please fill in the age field";
        } else {
            $message = "Ok";
        }
    }
}


echo '<form method="post">';
echo '<label for="age">Enter your age</label> ';
echo '<input type="number" name="age" id="age"> ';
echo '<br><br>';
echo '<label for="name">Enter your name</label> ';
echo '<input type="text" name="name" id="name"> ';
echo '<br><br>';
echo '<label for="accept">Accept</label> ';
echo '<input type="checkbox" name="accept" id="accept"> ';
echo '<br><br>';
echo '<button type="submit">Submit</button>';
echo '</form>';

echo $message;
