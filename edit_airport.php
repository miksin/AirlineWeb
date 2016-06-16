<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");
    check_admin();

    if(is_numeric($_POST['change']) && !$SESSION['airport_id']){
        $_SESSION['airport_id'] = $_POST['change'];
        header('location:view_airport.php');
        exit;
    }
    else if(is_numeric($_POST['delete'])){
        $id = $_POST['delete'];
        $sql = "DELETE FROM `airport`"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $id));
        header('location:view_airport.php');
        exit;
    }
    else if(is_numeric($_POST['edit_ok']) && $_SESSION['airport_id']){
        $input_name = $_POST['name'];
        $input_longitude = $_POST['longitude'];
        $input_latitude = $_POST['latitude'];
        $input_belong = $_POST['airport_belonging'];
        $input_fullname = $_POST['fullname'];
        $input_timezone = $_POST['timezone'];
        $match_pattern = '/^([0-9a-zA-Z\ -]+)$/';
        $match_pattern2 = '/^[\+-][0-9][0-9]:[0-9][0-9]$/';
        
        if(str_replace(" ","",$input_name)==="" || str_replace(" ","",$input_longitude)==="" || str_replace(" ","",$input_latitude)==="" || str_replace(" ","",$input_fullname)==="" || str_replace(" ","",$input_timezone)===""){
            $_SESSION['airport_message'] = 'Each field should not be empty.';
            header('location:view_airport.php');
            exit;
        }
        else if((!is_double($input_longitude) && !is_numeric($input_longitude))||(!is_double($input_latitude) && !is_numeric($input_latitude))){
            $_SESSION['airport_message'] = 'Longitude and latitude should be numeric.';
            header('location:view_airport.php');
            exit;
        }
        else if($input_longitude>180 || $input_longitude<-180 || $input_latitude >90 || $input_latitude<-90){
            $_SESSION['airport_message'] = 'Longitude (-180~180) or latitude (-90~90) is not correct.';
            header('location:view_airport.php');
            exit;
        }
        else if(!preg_match($match_pattern,$input_name) || !preg_match($match_pattern,$input_fullname)){
            $_SESSION['airport_message'] = 'Invalid charactors';
            header('location:view_airport.php');
            exit;
        }
        else if(!preg_match($match_pattern2,$input_timezone)){
            $_SESSION['airport_message'] = 'Wrong timezone format!';
            header('location:view_airport.php');
            exit;
        }
        else if(!is_numeric($input_belong)){
            $_SESSION['airport_message'] = 'Edit fail!';
            header('location:view_airport.php');
            exit;
        }
        else {
            $sql = "SELECT * FROM `airport`"
                  ." WHERE `abbreviation` = :an and `id` != :id";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':an' => $input_name,
                ':id' => $_POST['edit_ok']));

            if($check = $sth->fetchObject()){
                $_SESSION['airport_message'] = 'Airport name has been used.';
                header('location:view_airport.php');
                exit;
            }
            else {
                $sql = "UPDATE `airport`"
                      ." SET abbreviation = :an,longitude = :lg,latitude = :lt,fullname = :fn,belonging_country_id = :bc,timezone = :tz"
                      ." WHERE id = :id";
                $sth = $db->prepare($sql);
                $sth->execute(array(
                    ':an' => $input_name,
                    ':lg' => $input_longitude,
                    ':lt' => $input_latitude,
                    ':fn' => $input_fullname,
                    ':bc' => $input_belong,
                    ':tz' => reverse_timezone_transform($input_timezone),
                    ':id' => $_POST['edit_ok']));
                    
                unset($_SESSION['airport_id']);
                header('location:view_airport.php');
                exit;
            }
        }
    }
    else{
        header('location:index.php');
        exit;
    }
?>
