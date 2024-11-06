<?php
//how to run:
//step 1: crontab -e
//step 2: 0 0 */3 * * /usr/bin/php /var/www/sample/rabbitmqphp_example/cron-api-hotels.php
//btw, syntax ^: 0 min 0 hour every 3 days any month and weekday

include('amadeusData.php');

$locationCodes = ['NYC', 'PAR', 'LON']; //todo add more large cities

foreach ($locationCodes as $location) {
    requestApi(array('type' => 'hotel', 'location' => $location));
}
?>
