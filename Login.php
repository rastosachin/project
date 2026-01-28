<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $servername="localhost";
    $username="root";
    $password="";
    $db="check";

    $conn= new mysqli($servername,$username,$password,$db);

    $Name=$_POST['name'];
    $class=$_POST['class'];
    $address=$_POST['address'];
    $email=$_POST['email'];
    $dob=$_POST['dob'];


    $sql="INSERT INTO sam(name, class ,address, email, dob) VALUES('$Name','$class','$address', '$email', '$dob')";

    if($conn-> query($sql)){
        echo"Registration success";
    }
    else{
        echo"connection failed";
    }
    $conn->close();
    exit();
}
?>