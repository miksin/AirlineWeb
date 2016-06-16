<?php
    require_once 'config.php';
    require_once 'crypt.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");

    if($_POST['sign_up']){
        check_nologin();
    }
    else {
        header('location:index.php');
        exit;
    }

    $input_email = $_POST['email'];
    $input_pw = $_POST['pw'];
    $input_pw_ = $_POST['pw_'];
    $match_pattern = "/^([0-9a-zA-Z]+)$/";


    if($input_email==="" || $input_pw===""){
        $_SESSION['reg_message'] = 'Username and password should not be empty.';
        $_SESSION['input_email'] = $input_email;
        header('location:index.php');
        exit;
    }
    else if(strstr($input_email,' ') || strstr($input_pw,' ')){
        $_SESSION['reg_message'] = 'Username and password should not contain empty.';
        $_SESSION['input_email'] = $input_email;
        header('location:index.php');
        exit;
    }
    else if($input_pw != $input_pw_){
        $_SESSION['reg_message'] = 'Password confirmaion not match.';
        $_SESSION['input_email'] = $input_email;
        header('location:index.php');
        exit;
    }
    else if(!preg_match($match_pattern,$input_email) || !preg_match($match_pattern,$input_pw)){
        $_SESSION['reg_message'] = 'Include invalid charactors';
        header('location:index.php');
        exit;
    }
    else {
        $sql = "SELECT account FROM `user`"
              ."WHERE `account` = :un";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':un' => $input_email));

        if($check = $sth->fetchObject()){
            $_SESSION['reg_message'] = 'Username has been used.';
            $_SESSION['input_email'] = $input_email;
            header('location:index.php');
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
                ':ad' => 0));

            $_SESSION['reg_message'] = 'Create account Successfully.';
            header('location:index.php');
            exit;
        }
    }
?>
