<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    $user_authority = $_SESSION['identity'];
    
    if($_POST['prefer_flight'] || $_POST['no_prefer_flight'] || $_POST['no_follow']|| $_POST['add_follow']){
        $user_information = get_user();

        if($flight_id = $_POST['prefer_flight']){
            $sql = "INSERT INTO `cmpsheet` (flight_id,user_id)"
                  ." VALUES(:fd,:ud)";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':fd' => $flight_id,
                ':ud' => $user_information->id));
//            header('location:view_flight_'.$user_authority.'.php');
//            exit;
        }
        else if($flight_id = $_POST['no_prefer_flight']){
            $sql = "SELECT id FROM cmpsheet ".
                   "WHERE flight_id = :fid ".
                   "AND user_id = :uid ";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':fid' => $flight_id,
                ':uid' => $user_information->id
            ));
            $cmpObject = $sth->fetchObject();
            $cmp_id = $cmpObject->id;

            $sql = "DELETE FROM `cmpsheet`"
                  ." WHERE `id` = :id";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':id' => $cmp_id));
//            header('location:view_flight_'.$user_authority.'.php');
//            exit;
        } 
        else if($sh_id = $_POST['no_follow']){
            $sql = "DELETE FROM `cmpsheet`"
                  ." WHERE `id` = :id";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':id' => $sh_id));
            header('location:cmpsheet.php');
            exit;
        }
        else if($flight_id = $_POST['add_follow']){
            $sql = "INSERT INTO `cmpsheet` (flight_id,user_id)"
                  ." VALUES(:fd,:ud)";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':fd' => $flight_id,
                ':ud' => $user_information->id));
            header('location:cmpsheet.php');
            exit;
        } 
    }

?>
