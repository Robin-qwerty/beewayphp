<?php
include'../private/dbconnect.php';
session_start();


$sql = "UPDATE disciplines SET archive = 0 WHERE disciplineid=:disciplineid ";
$sth = $conn->prepare($sql);
$sth->bindParam(':disciplineid',$_GET['disciplineid']);
$sth->execute();
$_SESSION['success'] = "archive successful";
header("location: ../index.php?page=vakkenlijst");
?>
