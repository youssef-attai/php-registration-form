<?php

// Steps to get the API key:
// 1. Go to https://rapidapi.com/apidojo/api/online-movie-database/
// 2. Click on "Subscribe to test" button
// 3. Select the plan and click on "Subscribe" button
// 4. Copy your API key from the code snippet on the right side of the page, and paste it below
$API_KEY = "b56036c2a9msh8e3d79214b6ac0fp1f0aa1jsn81d4de64253b";

// Get the list of actors born on a specific day and month
function get_actors($Month, $Day, $API_KEY)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://online-movie-database.p.rapidapi.com/actors/list-born-today?month=$Month&day=$Day",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: online-movie-database.p.rapidapi.com",
            "X-RapidAPI-Key: $API_KEY"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else { // returns a list of actors born on the specified day and month (in HTML format)
        return $response;
    }
}

// Get the actor's bio
function get_actor_bio($nconst, $API_KEY)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://online-movie-database.p.rapidapi.com/actors/get-bio?nconst=$nconst",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => [
            "X-RapidAPI-Host: online-movie-database.p.rapidapi.com",
            "X-RapidAPI-Key: $API_KEY"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        echo "cURL Error #:" . $err;
    } else {
        // returns an array of the actor's bio (name, image, etc.)
        return json_decode($response, true);
    }
}


// Get the list of actors born on a specific day and month
$actors_list = get_actors($_GET['month'], $_GET['day'], $API_KEY);

// Extract the IDs using regular expression
preg_match_all('/\/name\/([a-z0-9]+)/i', $actors_list, $matches);
// Get all matching IDs
$response_ids = $matches[1];
// Counter to limit the number of actors to 3
$counter = 0;
// Get the actor's names and images for the first 3 actors
$actors_data = array();
foreach ($response_ids as $id) {
    $counter++;
    $actors_data[] = get_actor_bio($id, $API_KEY);
    if ($counter == 3) break;
}

$res = array("actors" => array());

// Print all the actors names and their images
foreach ($actors_data as $actor) {
    $img_result = $actor['image']['url'];
    // echo $actor['name'] . "<br>";
    // echo "<img src='$img_result' alt='actor image' width='200' height='300'> <br>";
    $res['actors'][] = array('name' => $actor['name'], 'image' => $img_result);
}

echo json_encode($res);
