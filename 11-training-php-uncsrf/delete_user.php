<?php

session_start(); 

require_once 'models/UserModel.php';
$userModel = new UserModel();

$user = NULL; //Add new user
$id = NULL;

if (
    !empty($_POST['id']) &&
    !empty($_POST['csrf']) &&
    isset($_SESSION['csrf']) &&        
    hash_equals($_SESSION['csrf'], $_POST['csrf']) 
) {
    $id = $_POST['id'];
    $userModel->deleteUserById($id);
}

header('Location: list_users.php');
exit;


?>