<?php

$videoLinks = [
    'https://www.youtube.com/watch?v=NxLjOCuKHFM',
    'https://www.youtube.com/watch?v=rG1ZpmE-H5o',
    'https://www.youtube.com/watch?v=ZG8BrakwuW8',
    'https://www.youtube.com/watch?v=7tJNtteH6KM',
    'https://www.youtube.com/watch?v=ThZ6dB42ls0',
    'https://www.youtube.com/watch?v=8dQ9eIhT_rs',
    'https://www.youtube.com/watch?v=soJt7G81UG4',
    'https://www.youtube.com/watch?v=Y1hxPlwNl_o',
    'https://www.youtube.com/watch?v=tWwrCJpOfIs',
    'https://www.youtube.com/watch?v=_rKpipQY0Oc'
];

$apiKey = 'AIzaSyCVMJei_bNg7xw9pyGV8TvsaqpZZAhgi_o';

$requestCounter = 0;
$totalCalls = 5000;

// Determine the number of iterations required
$iterations = ceil($totalCalls / count($videoLinks));

for ($i = 0; $i < $iterations; $i++) {
    foreach ($videoLinks as $link) {
        if ($requestCounter >= $totalCalls) {
            echo "Reached the maximum number of API requests.";
            break 2; // Break both the inner and outer loops
        }

        $videoId = getVideoIdFromLink($link);
        $videoStatus = getVideoStatus($videoId, $apiKey);

        echo "Video: $link\n";
        echo "Status: $videoStatus\n\n";

        $requestCounter++;
        usleep(100000); // Sleep for 100 milliseconds (adjust as needed to comply with API rate limits)
    }
}

function getVideoIdFromLink($link) {
    $query = parse_url($link, PHP_URL_QUERY);
    parse_str($query, $params);
    return $params['v'];
}

function getVideoStatus($videoId, $apiKey) {
    $url = "https://www.googleapis.com/youtube/v3/videos?part=status,contentDetails&id=$videoId&key=$apiKey";
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (isset($data['items'][0]['status']['uploadStatus'])) {
        $uploadStatus = $data['items'][0]['status']['uploadStatus'];
        $privacyStatus = $data['items'][0]['status']['privacyStatus'];
        $license = $data['items'][0]['status']['license'];
        $embeddable = $data['items'][0]['status']['embeddable'];
        $publicStatsViewable = $data['items'][0]['status']['publicStatsViewable'];

        $contentDetails = $data['items'][0]['contentDetails'];
        $madeForKids = isset($contentDetails['contentRating']['youthSafety']) ? $contentDetails['contentRating']['youthSafety'] : '';
        $selfDeclaredMadeForKids = $data['items'][0]['status']['selfDeclaredMadeForKids'] ?? '';

        return "Upload Status: $uploadStatus, Privacy Status: $privacyStatus, License: $license, Embeddable: $embeddable, Public Stats Viewable: $publicStatsViewable, Made For Kids: $madeForKids, Self-Declared Made For Kids: $selfDeclaredMadeForKids";
    }

    return 'Unknown';
}
