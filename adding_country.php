<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    
    if($_POST['add_country'] === 'add_country'){
        check_admin();
    }
    else {
        header('location:logout.php');
        exit;
    }

    $input_name = $_POST['country_name'];
    $input_fullname = $_POST['country_fullname'];
    $match_pattern = '/^([\.\(\)\ 0-9a-zA-Z]+)$/';
    $match_pattern2 = '/^[A-Z][A-Z][A-Z]$/';

    if(str_replace(" ","",$input_name)==="" || str_replace(" ","",$input_fullname)===""){
        $_SESSION['message'] = 'Each field should not be empty.';
        $_SESSION['input_country_name'] = $input_name;
        $_SESSION['input_fullname'] = $input_fullname;
        header('location:view_country.php');
        exit;
    }
    else if(!preg_match($match_pattern,$input_name) || !preg_match($match_pattern,$input_fullname)){
        $_SESSION['message'] = 'Invalid charactors';
        $_SESSION['input_country_name'] = $input_name;
        $_SESSION['input_fullname'] = $input_fullname;
        header('location:view_country.php');
        exit;
    }
    else if(!preg_match($match_pattern2,$input_name)){
        $_SESSION['message'] = 'Country name should be consist of exactly three upper case alphabet.';
        $_SESSION['input_country_name'] = $input_name;
        $_SESSION['input_fullname'] = $input_fullname;
        header('location:view_country.php');
        exit;
    }
    else {
        $sql = "SELECT * FROM `country`"
              ."WHERE `abbreviation` = :an";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':an' => $input_name));

        if($check = $sth->fetchObject()){
            $_SESSION['message'] = 'Country name has been used.';
            $_SESSION['input_country_name'] = $input_name;
            $_SESSION['input_fullname'] = $input_fullname;
            header('location:view_country.php');
            exit;
        }
        else {
            unset($_SESSION['input_country_name']);
            $sql = "INSERT INTO `country` (abbreviation,fullname)"
                  ." VALUES(:an,:fn)";
            $sth1 = $db->prepare($sql);
            $sth1->execute(array(
                ':an' => $input_name, 
                ':fn' => $input_fullname));
            header('location:view_country.php');
            exit;
        }
    }

?>

