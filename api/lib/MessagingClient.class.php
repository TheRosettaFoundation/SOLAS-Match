<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessagingClient
{
    public $MainExchange    = "SOLAS_MATCH"; // change to consts
    public $AlertsExchange  = "ALERTS";

    public $TaskScoreTopic                  = "tasks.score";
    public $UserTaskClaimTopic              = "email.user.task.claim";
    public $PasswordResetTopic              = "email.user.password-reset";
    public $OrgMembershipAcceptedTopic      = "email.org.membership.accepted";
    public $OrgMembershipRefusedTopic       = "email.org.membership.rejected";
    public $TaskArchivedTopic               = "email.user.task.archived";
    public $TaskClaimedTopic                = "email.user.task.claimed";
    public $FeedbackEmailTopic              = "email.user.feedback";
    public $EmailVerificationTopic          = "email";
    public $BannedLoginTopic                = "email";
    public $TaskUploadNotificationTopic     = "tasks";
    public $CalculateProjectDeadlinesTopic  = "projects";

    private $connection;

    public function messagingClient()
    {

    }

    public function init()
    {
        $ret=null;
       
        $ret = $this->openConnection();
       
        return $ret;
    }

    private function openConnection()
    {
        $ret = false;

        try {
            if ($this->connection = new AMQPConnection(Settings::get('messaging.host'), Settings::get('messaging.port'), settings::get('messaging.username'), Settings::get('messaging.password'))) {
                $ret = true;
            }
        } catch (Exception $e) {
            //echo "ERROR: ".$e->getMessage(); //do not echo any exception ever, it will break the api clients. 
            //log it to a file if needed.
        }

       

        return $ret;
    }

    public function sendTopicMessage($msg, $exchange, $topic)
    {
        $channel = $this->connection->channel();

        $channel->exchange_declare($exchange, 'topic', false, true, false);
        $channel->basic_publish($msg, $exchange, $topic);
        
        $channel->close();
    }

    public function sendMessage($msg, $exchange)
    {
        $channel = $this->connection->channel();

        $channel->exchange_declare($exchange, 'fanout', false, true, false);
        $channel->basic_publish($msg, $exchange);

        $channel->close();
    }

    public function createMessageFromString($message)
    {
        return new AMQPMessage($message, array('content_type' => 'text/plain'));
    }

    public function createMessageFromProto($proto)
    {
        return new AMQPMessage($proto->serialize());
    }
}
