<?php

$firstHour = new DateTime('2013-01-01 1:0:0');
$firstHour->setTime(1, 0);

$nextHour = new \DateTime($firstHour->format('Y-m-d H:i:s'));
$nextHour->add(new DateInterval('PT1H'));
echo $firstHour->format('g A') . ' - ' .  $nextHour->format('g A');
