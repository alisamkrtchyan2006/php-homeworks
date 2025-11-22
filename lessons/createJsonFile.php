<?php
declare(strict_types=1);

$jsonFile = 'students.json';

$students = [
    [
        'id' => 1,
        'name' => 'Arman',
        'surname' => 'Hakobyan',
        'age' => 21
    ],
    [
        'id' => 2,
        'name' => 'Ani',
        'surname' => 'Sargsyan',
        'age' => 20
    ]
];

file_put_contents($jsonFile, json_encode($students));

$jsonContent = file_get_contents($jsonFile);
$data = json_decode($jsonContent, true);

$newStudent = [
    'id' => 3,
    'name' => 'Mariam',
    'surname' => 'Stepanyan',
    'age' => 19
];

$data[] = $newStudent;

foreach ($data as $key => $student) {
    if ($student['id'] === 2) {
        $data[$key]['age'] = 22;
        break;
    }
}

file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT));