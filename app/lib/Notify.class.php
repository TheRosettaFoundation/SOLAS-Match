<?php

class Notify {
	public static function notifyUserClaimedTask($user, $task) {
		$app 		= Slim::getInstance();
        $settings   = new Settings();
        $task_url 	= $settings->get('site.url') . $app->urlFor('task', array('task_id' => $task->getTaskId()));

		$app->view()->appendData(array(
			'site_name' => $settings->get('site.name'),
			'task_url' => $task_url
		));
		$email_subject = "You have claimed a volunteer translation task, here's how to upload your translated file";
		$email_body = $app->view()->fetch('email.claimed-task.tpl');
		$user_email = $user->getEmail();

		Email::sendEmail($user_email, $email_subject, $email_body);
	}

    public static function sendEmailNotifications($task, $notificationType)
    {
        $app = Slim::getInstance();

        $task_dao = new TaskDao();
        $subscribed_users = $task_dao->getSubscribedUsers($task->getTaskId());

        $org_dao = new OrganisationDao();
        $org = $org_dao->find(array('id' => $task->getOrganisationId()));

        foreach($subscribed_users as $user) {
            $app->view()->setData('user', $user);
            $app->view()->appendData(array(
                        'task' => $task,
                        'org' => $org
            ));
            $email_subject = "A task's status has changed on SOLAS Match";
            $email_body = $app->view()->fetch($notificationType);
            $user_email = $user->getEmail();

            Email::sendEmail($user_email, $email_subject, $email_body);
        }
    }
}
