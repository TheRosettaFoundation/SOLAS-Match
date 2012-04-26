<?php

class Notify {
	public function notifyUserClaimedTask($user, $task) {
		// Get claim email contents
		$app 		= Slim::getInstance();
        $settings   = new Settings();
        $task_url 	= $app->urlFor('task', array('task_id' => $task->getTaskId()));

		$app->view()->appendData(array(
			'site_name' => $settings->get('site.name'),
			'task_url' => $task_url
		));
		$templating_engine = $app->view()->getInstance();
		$email_body = $templating_engine->render('email.claimed-task.tpl');

		// Send the email
		Email::
	}
}