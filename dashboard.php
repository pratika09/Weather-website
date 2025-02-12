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

$sql = "SELECT * FROM weather";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <div class="card">
        <h1>Weather Data Dashboard</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Temperature</th>
                    <th>Description</th>
                    <th>City</th>
                    <th>Pressure</th>
                    <th>Wind</th>
                    <th>Humidity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['_date']; ?></td>
                    <td><?php echo $row['temperature']; ?>°C</td>
                    <td><?php echo $row['_description']; ?></td>
                    <td><?php echo $row['city']; ?></td>
                    <td><?php echo $row['pressure']; ?> hPa</td>
                    <td><?php echo $row['wind']; ?> m/s</td>
                    <td><?php echo $row['humidity']; ?>%</td>
                    <td>
                        <a href="updateRecord.php?id=<?php echo $row['id']; ?>" class="update-btn">Update</a>
                        <a href="deleteRecord.php?id=<?php echo $row['id']; ?>" class="delete-btn">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <!-- <div class="logout"> -->
            <button class="logout"><a href="logout.php">Logout</a></button>
        <!-- </div> -->
    </div>
</body>
</html>
<?php
mysqli_close($conn);
?>
