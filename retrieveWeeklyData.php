<?php
header('Content-Type: application/json');
$apikey = 'b1b15e88fa797225412429c1c50c122a1'; // Replace with your OpenWeatherMap API key

$city = isset($_GET['city']) ? $_GET['city'] : 'Kathmandu';

// Fetch latitude and longitude for the city
$weatherUrl = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apikey;
$weatherResponse = @file_get_contents($weatherUrl);

if ($weatherResponse === FALSE) {
    echo json_encode(['error' => 'Unable to fetch weather data from: ' . $weatherUrl]);
    exit();
}

$weatherData = json_decode($weatherResponse, true);

if (isset($weatherData['message'])) {
    echo json_encode(['error' => $weatherData['message']]);
    exit();
}

$lat = $weatherData['coord']['lat'];
$lon = $weatherData['coord']['lon'];

// Fetch 7-day forecast data
$forecastUrl = "https://api.openweathermap.org/data/2.5/forecast/daily?lat=$lat&lon=$lon&cnt=7&appid=" . $apikey;
$forecastResponse = @file_get_contents($forecastUrl);

if ($forecastResponse === FALSE) {
    echo json_encode(['error' => 'Unable to fetch forecast data from: ' . $forecastUrl]);
    exit();
}

$forecastData = json_decode($forecastResponse, true);
$dailyData = array_slice($forecastData['list'], 1, 6); // Exclude today and get the next 6 days

echo json_encode(['list' => $dailyData]);
?>
