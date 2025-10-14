<?php
session_start();

/* connect to database check user*/
$con = mysqli_connect('localhost', 'root', '', 'login_1');
if (!$con) {
    die('Connection failed: ' . mysqli_connect_error());
}

/* create variables to store data */
$name =$_POST['user'];
$pass =$_POST['password'];

/* select data from DB */
$s="select * from table3 where username='$name'&& password='$pass'";

/* result variable to store data */
$result = mysqli_query($con,$s);

/* check for duplicate names and count records */
$num =mysqli_num_rows($result);
if($num==1){
  /* Storing the username and session */
    $_SESSION['username'] =$name;
    header('location:home.php');
}else{
    header('location:login.php');
}
