<?php
function celsiusToFahrenheit(float $celsius): float {
    $f = $celsius * 9/5 + 32;
    return $f;
}

function fahrenheitToCelsius(float $fahrenheit): float {
    $c = ($fahrenheit - 32) * 5/9;
    return $c;
}

echo celsiusToFahrenheit(20.3) . ' F';
echo '<br></br>';
echo fahrenheitToCelsius(32) . ' C';

