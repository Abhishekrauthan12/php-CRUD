<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "facebook";

$conn = new mysqli($servername,$username,$password,$dbname);

if($conn->connect_error){
    die("Connection failed:" . $conn->connect_error);
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
   $name    =$_POST['name'];
   $surname =$_POST['surname'];
   $date    =$_POST['date'];    
   $gender  =$_POST['gender'];    
   $email   =$_POST['email'];    
   $password=($_POST['password']);    

   $sql = "INSERT INTO user (name,surname,date,gender,email,password) VALUES(?,?,?,?,?,?)";
   $stmt=$conn->prepare($sql);

   if($stmt){
   $stmt->bind_param("ssssss",$name,$surname,$date,$gender,$email,$password);
   $stmt->execute();
   
   if ($stmt->affected_rows > 0) {
    header("Location: registration.php");
    echo "record created successfully";
    exit();
} else {
    echo "Error: " . $stmt->error;
}
$stmt->close();
} else {
    echo "Error preparing statement: " . $conn->error;
}
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up For facebook</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <h2>facebook</h2>
    <div class="container">
        <h3>Create New Account</h3>
        <p class="text">It's quick & easy</p><hr><br>
    <form action="registration.php" method="POST">
       <input type="text" id="name" name="name" placeholder="First name" required>
       <input type="text" id="name" name="surname" placeholder="Surname" required><br>

       <p>Date of birth:</p>
       <input type="date" id="date" name="date" required><br><br>

       Gender:
       <input type="radio" name="gender" value="male" required>Male:
       <input type="radio" name="gender" value="female" required>Female:<br><br>

       <input type="email" id="email" name="email" placeholder="Enter Email id" required><br><br>
       <input type="password" name="password" placeholder="Set Password" required><br> <br>

       <input type="submit" name="submit" value="Sign up"><br><br>
       <a class="texts" href="login.php">Already have an account?</a>
    </form>
    </div>
</body>
</html>
