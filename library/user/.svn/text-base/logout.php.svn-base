<?php

require($_SERVER['DOCUMENT_ROOT'].'/includes/smarty.php');

$my_access = new User($s, false);

// If we didn't do the next two lines, a cookie would continue to store the
// username, and would not ask them to reenter their password.
$my_access->save_login = "no";
$my_access->login_saver();

$my_access->logoutRedirect();

