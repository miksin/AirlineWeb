<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");
    
    if($_POST['add_airport'] === 'add_airport'){
        check_admin();
    }
    else {
        header('location:logout.php');
        exit;
    }

    $input_name = $_POST['airport_name'];
    $input_longitude = $_POST['airport_longitude'];
    $input_latitude = $_POST['airport_latitude'];
    $input_belong = $_POST['airport_belonging'];
    $input_fullname = $_POST['airport_fullname'];
    $input_timezone = $_POST['airport_timezone'];
    $match_pattern = '/^([\-0-9a-zA-Z\ ]+)$/';
    $match_pattern2 = '/^[\+-][0-9][0-9]:[0-9][0-9]$/';

    if(str_replace(" ","",$input_name)==="" || str_replace(" ","",$input_longitude)==="" || str_replace(" ","",$input_latitude)==="" || str_replace(" ","",$input_fullname)==="" || str_replace(" ","",$input_timezone)===""){
        $_SESSION['message'] = 'Each field should not be empty.';
        $_SESSION['input_airport_name'] = $input_name;
        $_SESSION['input_longitude'] = $input_longitude;
        $_SESSION['input_latitude'] = $input_latitude;
        $_SESSION['input_belonging'] = $input_belong;
        $_SESSION['input_airport_fullname'] = $input_fullname;
        $_SESSION['input_timezone'] = $input_timezone;
        header('location:view_airport.php');
        exit;
    }
    else if((!is_double($input_longitude) && !is_numeric($input_longitude))||(!is_double($input_latitude) && !is_numeric($input_latitude))){
        $_SESSION['message'] = 'Longitude and latitude should be numeric.';
        $_SESSION['input_airport_name'] = $input_name;
        $_SESSION['input_longitude'] = $input_longitude;
        $_SESSION['input_latitude'] = $input_latitude;
        $_SESSION['input_belonging'] = $input_belong;
        $_SESSION['input_airport_fullname'] = $input_fullname;
        $_SESSION['input_timezone'] = $input_timezone;
        header('location:view_airport.php');
        exit;
    }
    else if($input_longitude>180 || $input_longitude<-180 || $input_latitude >90 || $input_latitude<-90){
        $_SESSION['message'] = 'Longitude (-180~180) or latitude (-90~90) is not correct.';
        $_SESSION['input_airport_name'] = $input_name;
        $_SESSION['input_longitude'] = $input_longitude;
        $_SESSION['input_latitude'] = $input_latitude;
        $_SESSION['input_belonging'] = $input_belong;
        $_SESSION['input_airport_fullname'] = $input_fullname;
        $_SESSION['input_timezone'] = $input_timezone;
        header('location:view_airport.php');
        exit;
    }
    else if(!preg_match($match_pattern,$input_name) || !preg_match($match_pattern,$input_fullname)){
        $_SESSION['message'] = 'Invalid charactors';
        $_SESSION['input_airport_name'] = $input_name;
        $_SESSION['input_longitude'] = $input_longitude;
        $_SESSION['input_latitude'] = $input_latitude;
        $_SESSION['input_belonging'] = $input_belong;
        $_SESSION['input_airport_fullname'] = $input_fullname;
        $_SESSION['input_timezone'] = $input_timezone;
        header('location:view_airport.php');
        exit;
    }
    else if(!preg_match($match_pattern2,$input_timezone)){
        $_SESSION['message'] = 'Wrong timezone format!';
        $_SESSION['input_airport_name'] = $input_name;
        $_SESSION['input_longitude'] = $input_longitude;
        $_SESSION['input_latitude'] = $input_latitude;
        $_SESSION['input_belonging'] = $input_belong;
        $_SESSION['input_airport_fullname'] = $input_fullname;
        $_SESSION['input_timezone'] = $input_timezone;
        header('location:view_airport.php');
        exit;
    }
    else {
        $sql = "SELECT * FROM `airport`"
              ."WHERE `abbreviation` = :an";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':an' => $input_name));

        if($check = $sth->fetchObject()){
            $_SESSION['message'] = 'Airport name has been used.';
            $_SESSION['input_airport_name'] = $input_name;
            $_SESSION['input_longitude'] = $input_longitude;
            $_SESSION['input_latitude'] = $input_latitude;
            $_SESSION['input_belonging'] = $input_belong;
            $_SESSION['input_airport_fullname'] = $input_fullname;
            $_SESSION['input_timezone'] = $input_timezone;
            header('location:view_airport.php');
            exit;
        }
        else {
            $_SESSION['message'] = 'Add new airport successfully';
            $sql = "INSERT INTO `airport` (abbreviation,longitude,latitude,belonging_country_id,fullname,timezone)"
                  ." VALUES(:an,:lg,:lt,:bc,:fn,:tz)";
            $sth1 = $db->prepare($sql);
            $sth1->execute(array(
                ':an' => $input_name, 
                ':lg' => (double)$input_longitude,
                ':lt' => (double)$input_latitude,
                ':bc' => $input_belong,
                ':fn' => $input_fullname,
                ':tz' => reverse_timezone_transform($input_timezone)));
            header('location:view_airport.php');
            exit;
        }
    }

?>

