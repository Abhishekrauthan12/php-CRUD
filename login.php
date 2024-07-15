<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'facebook');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the query
    $stmt = $conn->prepare("SELECT id, password, is_verified FROM user WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $stored_password, $is_verified);
        $stmt->fetch();

        // Compare the plaintext password directly (Not secure for production)
        if ($password === $stored_password) {
            if ($is_verified) {
                echo "Login successful.";
                // Redirect to the user's dashboard or another page
                // header("Location: dashboard.php"); // Example redirection (uncomment to use)
            } else {
                // Update the is_verified field in the database
                $update_stmt = $conn->prepare("UPDATE user SET is_verified = 1 WHERE email = ?");
                $update_stmt->bind_param("s", $email);
                if ($update_stmt->execute()) {
                    echo "Email verified. Login successful.";
                } else {
                    echo "Failed to update email verification status.";
                }
                $update_stmt->close();
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email address.";
    }

    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in to Facebook</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <h2>Facebook</h2>
    <div class="container">
        <h4>Log in to Facebook</h4><br>
        <form action="login.php" method="POST">
            <input type="email" id="email" name="email" placeholder="Email address or Phone number" required><br><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br><br>
            <button type="submit" name="login">Log in</button><br><br>
            <div class="texts">
                <a href="forget.php">Forgotten account?</a>
                <a href="registration.php">Sign up for Facebook</a>
            </div>
        </form>
    </div>
</body>
</html>
