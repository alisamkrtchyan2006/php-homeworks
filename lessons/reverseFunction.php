<?php
function reverseAssoc(array $arr): array {
    $result = [];

    foreach ($arr as $key => $value) {
        $result[$value] = $key;
    }

    return $result;
}

$array = ["name" => "Alisa"];
$reversed = reverseAssoc($array);

print_r($array);
echo '<br><br>';
print_r($reversed);
