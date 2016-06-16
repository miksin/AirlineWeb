<?php
    require_once 'config.php';
    require_once 'check_page.php';
    require_once 'functions.php';
    session_start();
    session_save_path("./session");

    $username = if_login();
    $user_authority = $_SESSION['identity'];
    $user_information = get_user();

    $check = $_POST['shopping'];
    if($check == 'buy'){
        if($_POST['flight3']){
            $sql = "INSERT INTO shoppinglist (user_id,flight1_id,flight2_id,flight3_id)"
                  ." VALUES(:uid,:fid1,:fid2,:fid3)";
            $sth = $db->prepare($sql);
            $sth->execute(array(
               ':uid' => $user_information->id ,
                ':fid1' => $_POST['flight1'],
                ':fid2' => $_POST['flight2'],
                ':fid3' => $_POST['flight3']
                ));

            $sql = <<<__SQL__
            SELECT * FROM shoppinglist
            WHERE user_id = :ud
            AND flight1_id = :f1id
            AND flight2_id = :f2id
            AND flight3_id = :f3id
__SQL__;
            $find_ticket = $db->prepare($sql);
            $find_ticket->execute(array(
                ':ud' => $user_information->id,
                ':f1id' => $_POST['flight1'],
                ':f2id' => $_POST['flight2'],
                ':f3id' => $_POST['flight3']));
        }
        else if($_POST['flight2']){
            $sql = "INSERT INTO shoppinglist (user_id,flight1_id,flight2_id)"
                  ." VALUES(:uid,:fid1,:fid2)";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':uid' => $user_information->id ,
                ':fid1' => $_POST['flight1'],
                ':fid2' => $_POST['flight2']
                ));

            $sql = <<<__SQL__
            SELECT * FROM shoppinglist
            WHERE user_id = :ud
            AND flight1_id = :f1id
            AND flight2_id = :f2id
            AND flight3_id is NULL
__SQL__;
            $find_ticket = $db->prepare($sql);
            $find_ticket->execute(array(
                ':ud' => $user_information->id,
                ':f1id' => $_POST['flight1'],
                ':f2id' => $_POST['flight2']));
        }
        else {
            $sql = "INSERT INTO shoppinglist (user_id,flight1_id)"
                  ." VALUES(:uid,:fid1)";
            $sth = $db->prepare($sql);
            $sth->execute(array(
                ':uid' => $user_information->id ,
                ':fid1' => $_POST['flight1']
                ));

            $sql = <<<__SQL__
            SELECT * FROM shoppinglist
            WHERE user_id = :ud
            AND flight1_id = :f1id
            AND flight2_id is NULL
            AND flight3_id is NULL
__SQL__;
            $find_ticket = $db->prepare($sql);
            $find_ticket->execute(array(
                ':ud' => $user_information->id,
                ':f1id' => $_POST['flight1']));
        }

        $ticket = $find_ticket->fetchObject();
        echo "<div stype='buyed' sid='".$ticket->id."'>
              <a href='#' class='btn btn-sm btn-primary'>
              <span class='glyphicon glyphicon-shopping-cart'></span> Delete from shopping list
              </a>
              </div>";
    }
    else if($check === 'unbuy' && is_numeric($_POST['target_id'])){
        $sql = "DELETE FROM shoppinglist"
              ." WHERE id = :id";
        $sth = $db->prepare($sql);
        $sth->execute(array(
            ':id' => $_POST['target_id']));

        echo "<div stype='unbuy'>
              <a href='#' class='btn btn-sm btn-pink'>
              <span class='glyphicon glyphicon-shopping-cart'></span> Add to shopping list
              </a>
              </div>";
    }

?>
