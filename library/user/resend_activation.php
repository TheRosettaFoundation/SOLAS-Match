<?php
include($_SERVER['DOCUMENT_ROOT'].'/scripts/smarty.php');

$resend_activation = new Access_user($smarty);

if (isset($_POST['Submit'])) {
	$resend_activation->resend_activation($_POST['email']);
}
$error = $resend_activation->the_msg;
if (isset($error))
{
	$smarty->interface->assignVar('error', $error);
}

$smarty->interface->assignVar('page_title', $smarty->interface->getText('user_resend_activation'));
$smarty->interface->assignText('user_resend_activation');
$smarty->interface->assignText('user_resend_desc', $smarty->interface->getText('url_email_admin'));
$smarty->interface->assignText('email');
$smarty->interface->assignText('log_in');

$smarty->assign('value_email', (isset($_POST['email'])) ? $_POST['email'] : "");

$smarty->display('user_resend_activation.tpl');

?>
