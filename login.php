<?php
session_start();

require_once "auth.php";

$auth = new Auth();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($auth->attempt($username, $password)) {

        header("Location: payroll.php");
        exit;
    } else {

        $_SESSION['login_error'] = "Invalid username or password";
        header("Location: index.php");
        exit;
    }
}
