<?php
function password_hash ($password) {
    return crypt($password, '$5$rounds=5000$'. md5(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)) .'$');
}
     
function password_verify ($password, $crypted) {
    return crypt($password, $crypted) == $crypted;
}

?>
