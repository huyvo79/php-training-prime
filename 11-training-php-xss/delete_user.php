<?php

session_start(); 

require_once 'models/UserModel.php';

$user = NULL;
$id = NULL;
if( empty($_POST['id'])){
   echo "ID is empty";
    exit;
}
if(empty($_POST['csrf'])){
    echo "CSRF is empty";
    exit;
}

$userModel = new UserModel();

if (
    isset($_SESSION['csrf']) &&        
    hash_equals($_SESSION['csrf'], $_POST['csrf']) 
) {
    $id = $_POST['id'];
    $userModel->deleteUserById($id);
}


header('Location: list_users.php');

?>