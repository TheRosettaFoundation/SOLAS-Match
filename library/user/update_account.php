<?php
include($_SERVER['DOCUMENT_ROOT']."/scripts/smarty.php");

//$g_user = new Access_user;
// $new_member->language = "de"; // use this selector to get messages in other languages
$smarty->user->access_page(); // protect this page from users who are not logged in.
$smarty->user->get_user_info(); // call this method to get all other information

if (isset($_POST['Submit'])) {
	$smarty->user->update_user($_POST['login'], $_POST['password'], $_POST['confirm'], $_POST['email']); // the update method
}
$error = $smarty->user->the_msg; // error message
if (isset($error))
{
	$smarty->interface->assignVar('error', $error);
}
if (isset($_POST['Submit']) && empty($error)) {
	// Output a confirmation message that account has been updated.
	$smarty->interface->assignVar('confirmation', $smarty->interface->getText('user_mail_acct_mod'));
	;
}

$smarty->interface->assignText("user_update_account");
$smarty->assign("php_self", $_SERVER['PHP_SELF']);
$smarty->interface->assignText('display_name');
$smarty->interface->assignText('display_name_help');
$smarty->interface->assignTextVar('user_reg_login_min_length', LOGIN_LENGTH);
$smarty->assign("user_username", $smarty->interface->formatForDisplay($smarty->user->user));
$smarty->interface->assignText('password');
$smarty->interface->assignTextVar('user_reg_pass_min_length', PW_LENGTH);
$smarty->interface->assignText((isset($_POST['password'])) ? $_POST['password'] : '');
$smarty->interface->assignText("user_reg_confirm");
$smarty->assign("value_confirm", (isset($_POST['confirm'])) ? $_POST['confirm'] : '');
$smarty->assign('value_login', (isset($_POST['login'])) ? $_POST['login'] : $smarty->user->user);
$smarty->interface->assignText("email");
$smarty->assign("value_email", (isset($_POST['email'])) ? $_POST['email'] : $smarty->user->user_email);
$smarty->interface->assignText("back_home");
$smarty->interface->assignText("user_req_updating");

$smarty->display('user_update_account.tpl');
