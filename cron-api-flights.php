<?php
//how to run:
//step 1: crontab -e
//step 2: 0 0 * * * /usr/bin/php /var/www/sample/rabbitmqphp_example/cron-api-flights.php
//btw, syntax ^: 0 min 0 hour every day any month and weekday

include('amadeusData.php');

requestApi(array('type' => 'flight', 'location' => 'NYC'));
// just nyc-origin flights for now

/* TODO see if more locations needed
$locationCodes = ['NYC', 'PAR', 'LON']; //todo add more large cities

foreach ($locationCodes as $location) {
    requestApi(array('type' => 'flight', 'location' => $location));
}
*/
?>
