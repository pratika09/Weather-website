<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "weatherdb";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // CREDENTIALSSSSSSSS
    if ($username == "admin" && $password == "password") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: dashboard.php");
    } else {
        echo '<script>
        alert("Invalid Credentials");
        window.location.href = "adminLogin.html";
      </script>';
    }
}

mysqli_close($conn);
?>
