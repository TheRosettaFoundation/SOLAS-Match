<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessagingClient
{
    public $MainExchange    = "SOLAS_MATCH"; // change to consts
    public $AlertsExchange  = "ALERTS";

    public $OrgCreatedTopic                 = "users";
    public $TaskScoreTopic                  = "tasks";
    public $TaskUploadNotificationTopic     = "tasks";
    public $CalculateProjectDeadlinesTopic  = "projects";
    public $UserTaskClaimTopic              = "email";
    public $PasswordResetTopic              = "email";
    public $OrgMembershipAcceptedTopic      = "email";
    public $OrgMembershipRefusedTopic       = "email";
    public $TaskArchivedTopic               = "email";
    public $TaskClaimedTopic                = "email";
    public $EmailVerificationTopic          = "email";
    public $BannedLoginTopic                = "email";
    public $UserFeedbackTopic               = "email";
    public $OrgFeedbackTopic                = "email";

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
