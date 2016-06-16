<?php
    $id = $_POST['id'];
    $value = $_POST['value'];
    $match_pattern = "/^([0-9a-zA-Z]+)$/";

    if(!preg_match($match_pattern,$value)){
        $_SESSION['message'] = 'Invalid charactors';
    }
    else {
        echo $value;
    }

    list($field, $id) = explode('_', $id);
?>
