<?php
// version 1

echo "<table>";
for ($i = 1; $i <= 10; $i++) {
    echo "<tr>";
    echo "<td>$i</td>";
    for ($j = 1; $j <= 10; $j++) {
        echo "<td>" . ($i * $j) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";


echo "<br>";

// version 2

echo "<table>";
echo "<tr><th> </th>";
    for ($a = 1; $a <= 10; $a++) {
        echo "<th>$a</th>";
    }
    echo "</tr>";
for ($i = 1; $i <= 10; $i++) {
    echo "<tr>";
    echo "<th>$i</th>";
    for ($j = 1; $j <= 10; $j++) {
        echo "<td>" . ($i * $j) . "</td>";
    }
    echo "</tr>";
}
echo "</table>";