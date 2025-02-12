<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: adminLogin.html");
    exit;
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weatherdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch weather data from API
function fetchWeatherFromAPI($city) {
    $apikey = '9633306c8594e498ff6e92d5186586fa';
    $url = "https://api.openweathermap.org/data/2.5/weather?q=" . urlencode($city) . "&appid=" . $apikey . "&units=metric";
    $data = file_get_contents($url);
    return json_decode($data, true);
}

// Function to insert weather data into the database
function insertWeatherData($data, $conn) {
    $sql = "INSERT INTO weather (id, _date, temperature, _description, city, pressure, wind, humidity) 
            VALUES('" . $data['id'] . "', '" . $data['_date'] . "', '" . $data['temperature'] . "', '" . $data['_description'] . "', '" . $data['city'] . "', '" . $data['pressure'] . "', '" . $data['wind'] . "', '" . $data['humidity'] . "')";
    mysqli_query($conn, $sql);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Retrieve the city of the record to be updated
    $sql = "SELECT city FROM weather WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    $record = mysqli_fetch_assoc($result);

    if ($record) {
        $city = $record['city'];

        // Delete the old record
        $sql = "DELETE FROM weather WHERE id = $id";
        mysqli_query($conn, $sql);

        // Fetch new weather data from API
        $weather = fetchWeatherFromAPI($city);

        if (isset($weather['id'])) {
            $newData = [
                'id' => $weather['id'],
                '_date' => date('Y-m-d'),
                'temperature' => $weather['main']['temp'],
                '_description' => $weather['weather'][0]['description'],
                'city' => $weather['name'],
                'pressure' => $weather['main']['pressure'],
                'wind' => $weather['wind']['speed'],
                'humidity' => $weather['main']['humidity']
            ];

            // Insert the new record
            insertWeatherData($newData, $conn);
        }

        header("Location: dashboard.php");
    } else {
        echo "Invalid ID.";
    }
} else {
    echo "ID is required.";
}

mysqli_close($conn);
?>
