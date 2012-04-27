<?php

class Notify {
	public static function notifyUserClaimedTask($user, $task) {
		$app 		= Slim::getInstance();
        $settings   = new Settings();
        $task_url 	= $app->urlFor('task', array('task_id' => $task->getTaskId()));

		$app->view()->appendData(array(
			'site_name' => $settings->get('site.name'),
			'task_url' => $task_url
		));
		$email_subject = "You have claimed a volunteer translation task, here's how to upload your translated file";
		$email_body = $app->view()->fetch('email.claimed-task.tpl');
		$user_email = $user->getEmail();

		Email::sendEmail($user_email, $email_subject, $email_body);
	}
}