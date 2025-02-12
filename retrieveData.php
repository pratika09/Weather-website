<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weatherdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function getWeatherData($city, $date, $conn) {
    $sql = "SELECT * FROM weather WHERE city = '$city' AND _date = '$date'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    } else {
        return null;
    }
}

function fetchWeatherFromAPI($city) {
    $apikey = '9633306c8594e498ff6e92d5186586fa';
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apikey . "&units=metric";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

function insertWeatherData($data, $conn) {
    $sql = "INSERT INTO weather (id, _date, temperature, _description, city, pressure, wind, humidity) 
            VALUES('" . $data['id'] . "', '" . $data['_date'] . "', '" . $data['temperature'] . "', '" . $data['_description'] . "', '" . $data['city'] . "', '" . $data['pressure'] . "', '" . $data['wind'] . "', '" . $data['humidity'] . "')";
    mysqli_query($conn, $sql);
}

$city = $_GET['city'] ?? '';
$date = date('Y-m-d');

if (empty($city)) {
    echo json_encode(['error' => 'City name is required']);
    mysqli_close($conn);
    exit();
}

$data = getWeatherData($city, $date, $conn);

if ($data === null) {
    $weather = fetchWeatherFromAPI($city);

    if (isset($weather['id'])) {
        $data = [
            'id' => $weather['id'],
            '_date' => $date,
            'temperature' => $weather['main']['temp'],
            '_description' => $weather['weather'][0]['description'],
            'city' => $weather['name'],
            'pressure' => $weather['main']['pressure'],
            'wind' => $weather['wind']['speed'],
            'humidity' => $weather['main']['humidity']
        ];
        insertWeatherData($data, $conn);
    } else {
        echo json_encode(['error' => 'Weather data not found']);
        mysqli_close($conn);
        exit();
    }
}

echo json_encode($data);
mysqli_close($conn);
?>
