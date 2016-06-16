<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    check_admin();

    if($id = $_POST['change']){
        $sql = "SELECT * FROM `user`"
              ."WHERE `id` = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $id));
        $edit_data = $sth->fetchObject();

        $sql = "UPDATE `user`"
              ." SET is_admin = :ad"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':ad' => !($edit_data->is_admin),
            ':id' => $id ));
        header('location:view_user.php');
        exit;
    }
    else if($id = $_POST['delete']){
        $sql = "DELETE FROM `user`"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $id));
        header('location:view_user.php');
        exit;
    }
    else{
        header('location:index.php');
        exit;
    }
?>
