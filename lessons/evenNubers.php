<?php


// version 1

function firstFilterArray(array $arr, callable $callback): array {
    $result = [];

    foreach ($arr as $item) {
        if ($callback($item)) {
            $result[] = $item;
        }
    }

    return $result;
}

$numbers = [1, 2, 3, 4, 5, 6];

$even = firstFilterArray($numbers, function($n) {
    return $n % 2 === 0;
});

echo '<pre>';
print_r($even);
echo '</pre>';





// version 2

function secondFilterArray(callable $callback, int ...$numbers): void {
    foreach ($numbers as $n) {
        if ($callback($n)) {
            echo $n . ' ';
        }
    }
}

secondFilterArray(function($n) {
    return $n % 2 === 0;
}, 1, 2, 3, 4, 5, 6);
