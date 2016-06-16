<?php
    require_once 'config.php';
    require_once 'check_page.php';
    session_start();
    session_save_path("./session");
    if($id = $_POST['delete_flight']){
        $sql = "DELETE FROM `flight`"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $id));
    }
?>
