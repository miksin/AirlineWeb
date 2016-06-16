<?php
    require_once 'config.php';
    require_once 'crypt.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    
    if($_POST['add_user']){
        check_admin();
    }
    else {
        header('location:index.php');
        exit;
    }

    $input_email = $_POST['email'];
    $input_pw = $_POST['pw'];
    $input_pw_ = $_POST['pw_'];
    $input_admin = $_POST['is_admin'];

    if($input_email==="" || $input_pw===""){
        $_SESSION['message'] = 'Email and password should not be empty.';
        $_SESSION['input_email'] = $input_email;
        if($input_admin == 1){
            $_SESSION['choose_admin'] = $input_admin;
        }
        header('location:view_user.php');
        exit;
    }
    else if(strstr($input_email,' ') || strstr($input_pw,' ')){
        $_SESSION['message'] = 'Email and password should not contain empty.';
        $_SESSION['input_email'] = $input_email;
        if($input_admin == 1){
            $_SESSION['choose_admin'] = $input_admin;
        }
        header('location:view_user.php');
        exit;
    }
    else if($input_pw != $input_pw_){
        $_SESSION['message'] = 'Password confirmaion not match.';
        $_SESSION['input_email'] = $input_email;
        if($input_admin == 1){
            $_SESSION['choose_admin'] = $input_admin;
        }
        header('location:view_user.php');
        exit;
    }
    else {
        $sql = "SELECT account FROM `user`"
              ."WHERE `account` = :un";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':un' => $input_email));

        if($check = $sth->fetchObject()){
            $_SESSION['message'] = 'Username has been used.';
            $_SESSION['input_email'] = $input_email;
            if($input_admin == 1){
                $_SESSION['choose_admin'] = $input_admin;
            }
            header('location:view_user.php');
            exit;
        }
        else {
            $hash_pw = password_hash($input_pw);
            $sql = "INSERT INTO `user` (account,password,is_admin)"
                  ." VALUES(:ac,:pw,:ad)";
            $sth1 = $db->prepare($sql);
            $sth1->execute(array(
                ':ac' => $input_email, 
                ':pw' => $hash_pw,
                ':ad' => $input_admin));
            $_SESSION['message'] = 'Create account Successfully.';
            header('location:view_user.php');
            exit;
        }
    }

?>


