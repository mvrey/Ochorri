<?php
$manteinanceCost=2;
$incrementCost=1.5;
$level = 3;

$actualCost = $manteinanceCost*(pow(1+$incrementCost,$level-1));
$previousCost = $manteinanceCost*(pow(1+$incrementCost,$level-2));
$value=$actualCost-$previousCost;
echo $value;

?>
