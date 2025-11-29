<?php

$fruits = [
    'pomegranat' => 300,
    'apple' => 100,
    'banana' => 250,
    'pineapple' => 700,
    'orange' => 300,
    'strawberry' => 500,
];



//Most expensive version 1

$maxPrice = 0;
$mostExpensive = '';

foreach ($fruits as $key => $value) {
    if ($value > $maxPrice) {
        $maxPrice = $value;
        $mostExpensive = $key;
    }
}




echo "$mostExpensive - $maxPrice$";

echo "<br><br>";


//Most expensive version 2

$items = [];
foreach ($fruits as $k => $v) {
    $items[] = [$k, $v];
}

$maxPrice = 0;
$mostExpensive = '';

for ($i = 0; $i < count($items); $i++) {
    $fruit = $items[$i][0];
    $price = $items[$i][1];

    if ($price > $maxPrice) {
        $maxPrice = $price;
        $mostExpensive = $fruit;
    }
}


echo "$mostExpensive - $maxPrice$";



echo "<br><br>";



// Sorted array

$sortedFruits = [];

while (count($fruits) > 0) {
    $minKey = 0;
    $minValue = 0;
    foreach ($fruits as $key => $value) {
        if ($minValue === 0 || $value < $minValue) {
            $minValue = $value;
            $minKey = $key;
        }
    }
    $sortedFruits[$minKey] = $minValue;
    unset($fruits[$minKey]);
}

echo '<pre>';
print_r($sortedFruits);
echo '</pre>';





// Sum price
$fruits = [
    'pomegranat' => 300,
    'apple' => 100,
    'banana' => 250,
    'pineapple' => 700,
    'orange' => 300,
    'strawberry' => 500,
];

$sum = 0;
foreach ($fruits as $key => $value) {
    $sum += $value;
}
echo "Total price - " . $sum;




echo "<br><br>";

// Average price

$fruits = [
    'pomegranat' => 300,
    'apple' => 100,
    'banana' => 250,
    'pineapple' => 700,
    'orange' => 300,
    'strawberry' => 500,
];

$sum = 0;
foreach ($fruits as $key => $value) {
    $sumPrice = $sum += $value;
    $average = $sumPrice/2;
}
echo "Average price - " . $average;