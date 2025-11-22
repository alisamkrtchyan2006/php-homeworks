<?php
declare(strict_types=1);

$file = 'students.txt';


function addStudent(string $file, string $name, string $surname): void
{
    $name = trim($name);
    $surname = trim($surname);

    if ($name !== '' && $surname !== '') {
        $fp = fopen($file, 'a') or die("Could not open file.");
        fwrite($fp, $name . ' ' . $surname . "\n"); 
        fclose($fp);
    }
}


function getStudents(string $file): array
{
    if (file_exists($file)) {
        return file($file);
    }
    return [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    addStudent($file, $_POST['name'] ?? '', $_POST['surname'] ?? '');
}


echo '<div>
    <h1>Add name and surname</h1>

    <form action="" method="post">
        <label for="name">Name</label>
        <input type="text" name="name" id="name" required>

        <label for="surname">Surname</label>
        <input type="text" name="surname" id="surname" required>

        <button type="submit">Add</button>
    </form>

</div>';


echo '<h2>Student list</h2>';
echo '<ul>';
$students = getStudents($file);
if (!empty($students)) {
    foreach ($students as $student) {
        echo '<li>' . htmlspecialchars($student) . '</li>';
    }
} else {
    echo '<p>Students have not been added yet</p>';
}
echo '</ul>';


