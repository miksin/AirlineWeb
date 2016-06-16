<?php
    require_once 'config.php';
    require_once 'crypt.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");

    if($_POST['sign_in'] == 'Sign in'){
    }
    else {
        header('location:index.php');
        exit;
    }

    $input_email = $_POST['email'];
    $input_pw = $_POST['pw'];
    $match_pattern = "/^([0-9a-zA-Z]+)$/";

    if($input_email === "" || $input_pw === ""){
        $_SESSION['login_message'] = 'username and password should not be empty';
        $_SESSION['input_email'] = $input_email;
        header('location:index.php');
        exit;
    }
    else if(!preg_match($match_pattern,$input_email) || !preg_match($match_pattern,$input_pw)){
        $_SESSION['login_message'] = 'Include invalid charactors';
        header('location:index.php');
        exit;
    }
    else {
        $sql = "SELECT * From `user`"
             ." WHERE `account` = :ac";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':ac' => $input_email));

        if ($result = $sth->fetchObject()){
            if(password_verify($input_pw,$result->password)){
                $_SESSION['id'] = $result->id;
                if($result->is_admin){
                    $_SESSION['identity'] = 'admin';
//                    header('location:view_flight_admin.php');
                    header('location:index.php');
                    exit;
                }
                else {
                    $_SESSION['identity'] = 'user';
//                    header('location:view_flight_user.php');
                    header('location:index.php');
                    exit;
                }
            }
            else { 
                $_SESSION['login_message'] = 'The password is not correct.';
                $_SESSION['input_email'] = $input_email;
                header('location:index.php');
                exit;
            }
        }
        else {
            $_SESSION['login_message'] = 'Invalid username';
            header('location:index.php');
            exit;
        }
    }

?>
