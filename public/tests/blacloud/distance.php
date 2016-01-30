<?php
require_once ('../../lib/inclusion.php');

require_once_model('Sector');

$sector = new Sector(0, 7, 1);

echo $sector->getDistanceFrom(6, 3);
?>
