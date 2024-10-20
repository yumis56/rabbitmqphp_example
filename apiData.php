<?php
function fetchMinion($id) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://ffxivcollect.com/api/minions/' . $id,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);
    
    curl_close($curl);
    echo $response;
    
    if ($response === false) {
        throw new Exception('Curl error: ' . curl_error($curl));
    }

    $data = json_decode($response, true);
    if ($data === null) {
        throw new Exception('JSON decode error: ' . json_last_error_msg());
    }

    return $data;
}
?>

