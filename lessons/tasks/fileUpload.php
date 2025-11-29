<?php
declare(strict_types=1);

$uploadDir = 'uploads';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    $file = $_FILES['image'];

    if ($file['error'] === 0) {

        $originalName = $file['name'];
        $parts = explode('.', $originalName);
        $ext = strtolower(end($parts));

        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed)) {
            echo "Sorry, you can only upload image files (.jpg, .jpeg, .png, .gif).";
            exit;
        }

        $newName = time() . '.' . $ext;

        $destination = $uploadDir . '/' . $newName;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            echo "The file was successfully uploaded. <br>";
            echo "New name. " . htmlspecialchars($newName);
        } else {
            echo "Sorry, the file could not be saved.";
        }

    } else {
        echo "An error occurred while uploading the file.";
    }
}


echo '<div>
    <h1>Upload image</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Upload</button>
    </form>
</div>';
