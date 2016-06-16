<?php
    require_once 'config.php';
    session_start();
    session_save_path("./session");
function check_admin(){
    if($log_id = $_SESSION['id']){
        $sql = "SELECT * FROM `user`"
              ."WHERE `id` = :id";
        global $db;
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $log_id
            ));
        if($user_information = $sth->fetchObject()){
            if($_SESSION['identity'] === 'admin'){
            }
            else if($_SESSION['identity'] === 'user'){
                header('location:logout.php');
                exit;
            }
            else {
                header('location:logout.php');
                exit;
            }
        }
        else {
            unset($_SESSION['id']);
            header('location:logout.php');
            exit;
        }
    }
    else {
        header('location:logout.php');
        exit;
    }
    return $user_information;
}

function check_user(){
    if($log_id = $_SESSION['id']){
        $sql = "SELECT * FROM `user`"
              ."WHERE `id` = :id";
        global $db;
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $log_id
            ));
        if($user_information = $sth->fetchObject()){
            if($_SESSION['identity'] === 'admin'){
                header('location:logout.php');
                exit;
            }
            else if($_SESSION['identity'] === 'user'){
            }
            else {
                header('location:logout.php');
                exit;
            }
        }
        else {
            unset($_SESSION['id']);
            header('location:logout.php');
            exit;
        }
    }
    else {
        header('location:logout.php');
        exit;
    } 
    return $user_information;
}

function check_login(){
    if($log_id = $_SESSION['id']){
        $sql = "SELECT * FROM `user`"
              ."WHERE `id` = :id";
        global $db;
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $log_id
            ));
        if($user_information = $sth->fetchObject()){
            if($_SESSION['identity'] === 'admin'){
            }
            else if($_SESSION['identity'] === 'user'){
            }
            else {
                header('location:logout.php');
                exit;
            }
        }
        else {
            unset($_SESSION['id']);
            header('location:logout.php');
            exit;
        }
    }
    else {
        header('location:logout.php');
        exit;
    }
    return $user_information;
}

function check_nologin(){
    unset($_SESSION['id']);
    unset($_SESSION['identity']);
}

function if_login(){
    if($log_id = $_SESSION['id']){
        $sql = "SELECT * FROM `user`"
              ."WHERE `id` = :id";
        global $db;
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $log_id
            ));
        if($user_information = $sth->fetchObject()){
            if($_SESSION['identity'] === 'admin'){
                return $user_information->account;
            }
            else if($_SESSION['identity'] === 'user'){
                return $user_information->account;
            }
            else {
                unset($_SESSION);
                session_destroy();
                return '_nologin_';
            }
        }
        else {
            unset($_SESSION);
            session_destroy();
            return '_nologin_';
        }
    }
    else {
        return '_nologin_';
    }
}

function get_user(){
    $log_id = $_SESSION['id'];
    $sql = "SELECT * FROM `user`"
          ."WHERE `id` = :id";
    global $db;
    $sth = $db->prepare($sql);
    $sth->execute(array(
        ':id' => $log_id
        ));
    $user = $sth->fetchObject();

    return $user;
}
?>
