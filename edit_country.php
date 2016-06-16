<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    check_admin();

    if(is_numeric($_POST['change']) && !$SESSION['country_id']){
        $_SESSION['country_id'] = $_POST['change'];
        header('location:view_country.php');
        exit;
    }
    else if(is_numeric($_POST['delete'])){
        $id = $_POST['delete'];
        $sql = "DELETE FROM `country`"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $id));
        header('location:view_country.php');
        exit;
    }
    else if(is_numeric($_POST['edit_ok']) && $_SESSION['country_id']){
        $input_name = $_POST['name'];
        $input_fullname = $_POST['fullname'];
        $input_timezone = $_POST['timezone'];
        $match_pattern = '/^([\.\(\)\ 0-9a-zA-Z]+)$/';
        $match_pattern2 = '/^[A-Z][A-Z][A-Z]$/';
        
        if(str_replace(" ","",$input_name)==="" || str_replace(" ","",$input_fullname)===""){
            $_SESSION['country_message'] = 'Each field should not be empty.';
            header('location:view_country.php');
            exit;
        }
        else if(!preg_match($match_pattern2,$input_name) || !preg_match($match_pattern,$input_fullname)){
            $_SESSION['country_message'] = 'Invalid charactors';
            header('location:view_country.php');
            exit;
        }
        else {
            $sql = "SELECT * FROM `country`"
                  ." WHERE `abbreviation` = :an and `id` != :id";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':an' => $input_name,
                ':id' => $_POST['edit_ok']));

            if($check = $sth->fetchObject()){
                $_SESSION['country_message'] = 'Country name has been used.';
                header('location:view_country.php');
                exit;
            }
            else {
                $sql = "UPDATE `country`"
                      ." SET abbreviation = :an,fullname = :fn"
                      ." WHERE id = :id";
                $sth = $db->prepare($sql);
                $sth->execute(array(
                    ':an' => $input_name,
                    ':fn' => $input_fullname,
                    ':id' => $_POST['edit_ok']));
                    
                unset($_SESSION['country_id']);
                header('location:view_country.php');
                exit;
            }
        }
    }
    else{
        header('location:index.php');
        exit;
    }
?>
