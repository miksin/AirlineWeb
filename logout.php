<?php
    session_start();
    session_save_path("./session");
    $_SESSION = array();
    session_destroy();
    session_unset();
    unset($_SESSION);

    header('location:index.php');
    exit;
?>
