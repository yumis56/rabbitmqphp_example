<?php

// this snippet is from soccerData.php, keeping here for visual reference
#$results = shell_exec('GET http://api.football-data.org/alpha/soccerseasons/354');
#$arrayCode = json_decode($results);
#var_dump($arrayCode);
// snippet end

//The code below is based on template provided by https://documenter.getpostman.com/view/1779678/TzXzDHM1#b5bb8051-951c-4d10-ace8-6d5a679ffb33
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://ffxivcollect.com/api/minions/1', // the '/1' is for id#1, we can specify num to get a specific result of that id
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

if ($response === false) {
    echo 'Curl error: ' . curl_error($curl); // this handles errors
} else {
    echo $response; // outputs response
}

curl_close($curl);

?>

