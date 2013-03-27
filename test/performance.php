<?php

require_once 'PHPUnit/Autoload.php';
require_once __DIR__.'/../api/vendor/autoload.php';
\DrSlump\Protobuf::autoload();
require_once __DIR__.'/../api/DataAccessObjects/BadgeDao.class.php';
require_once __DIR__.'/../Common/lib/ModelFactory.class.php';
require_once __DIR__.'/UnitTestHelper.php';

$startTime = system('date +%s%N') / 1000000000;

for($i=0; $i < 50000; $i++) {
    $badgeDao = new BadgeDao();
    $result = $badgeDao->getBadge();
}

$endTime = system('date +%s%N')  / 1000000000;

$timeTaken = $endTime - $startTime;
echo "Newing up each time: $timeTaken seconds \n";





$startTime = system('date +%s%N') / 1000000000;

$badgeDao = new BadgeDao();
for($i=0; $i < 50000; $i++) {
    $result = $badgeDao->getBadge();
}

$endTime = system('date +%s%N')  / 1000000000;

$timeTaken = $endTime - $startTime;
echo "Newing up once: $timeTaken seconds \n";





$startTime = system('date +%s%N') / 1000000000;

for($i=0; $i < 50000; $i++) {
    $result = BadgeDao::getBadge();
}

$endTime = system('date +%s%N')  / 1000000000;

$timeTaken = $endTime - $startTime;
echo "Static Call: $timeTaken seconds \n";

?>
