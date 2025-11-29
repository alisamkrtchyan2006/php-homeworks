<?php
declare(strict_types=1);


function separateFilesByType(string $folder): array
{
    $images = [];
    $nonImages = [];

    $files = scandir($folder); 

    foreach ($files as $file) {

        if ($file === '.' || $file === '..') {
            continue;
        }

        $fullPath = $folder . '/' . $file;

            $parts = explode('.', $file);
            $ext = strtolower(end($parts));

            if ($ext === 'png' || $ext === 'jpg' || $ext === 'jpeg') {
                $images[] = $file;
            } else {
                $nonImages[] = $file;
            }
    }

    return ['images' => $images, 'nonImages' => $nonImages];
}


$folderPath = '..'; 

$result = separateFilesByType($folderPath);

echo "<h2>Image files</h2>";
if (!empty($result['images'])) {
    echo "<ul>";
    foreach ($result['images'] as $img) {
        echo "<li>" . htmlspecialchars($img) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No image files</p>";
}

echo "<h2>Non-image files</h2>";
if (!empty($result['nonImages'])) {
    echo "<ul>";
    foreach ($result['nonImages'] as $file) {
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>No non-image files</p>";
}
