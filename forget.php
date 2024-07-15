<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'facebook');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id FROM user WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind the email parameter
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if any rows were returned
    if ($stmt->num_rows > 0) {
        $_SESSION['email'] = $email; // Store email in session for further use
        header("Location: password.php"); // Redirect to new_page.php
        exit();
    } else {
        echo "Email does not exist in the database.";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgotten Password|Can't Log in</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
   <h2>facebook</h2>
   <div class="container">
    <h3>Find your account</h3><hr>
    <p>Please enter your email address or mobile number to search for your account.</p>
    <form action="forget.php" method="POST">
    <input type="email" id="email" name="email" placeholder="Email address or phone number"><br><br><hr>
    <div class="searchbox">
  <a href="login.php"><input type="button" value="cancel"> </a>
   <input type="submit" name="submit" value="search">
   </div>
   </form>
</div>
</body>
</html>