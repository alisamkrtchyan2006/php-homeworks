<?php

function countValues(array $arr): array {
    $result = [];

    foreach ($arr as $item) {
        if (isset($result[$item])) {
            $result[$item]++;
        } else {
            $result[$item] = 1;
        }
    }

    return $result;
}

echo '<pre>';
print_r (countValues(["a", 1, "a", "c", 13, "c"]));
echo '</pre>';
