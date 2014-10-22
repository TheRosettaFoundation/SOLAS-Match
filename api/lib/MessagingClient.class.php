<?php

namespace SolasMatch\API\Lib;

use \PhpAmqpLib\Connection\AMQPConnection;
use \PhpAmqpLib\Message\AMQPMessage;
use \SolasMatch\Common as Common;

class MessagingClient
{
    public $MainExchange    = "SOLAS_MATCH"; // change to consts
    public $AlertsExchange  = "ALERTS";

    public $OrgCreatedTopic                 = "users";
    public $TaskRevokedTopic                = "users";
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
    public $UserReferenceRequestTopic       = "email";
    public $UserBadgeAwardedTopic           = "email";

    private $connection;

    public function messagingClient()
    {
        // Default ctor
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
            $this->connection = new AMQPConnection(
                Common\Lib\Settings::get('messaging.host'),
                Common\Lib\Settings::get('messaging.port'),
                Common\Lib\Settings::get('messaging.username'),
                Common\Lib\Settings::get('messaging.password')
            );
            if ($this->connection) {
                $ret = true;
            }
        } catch (Exception $e) {
            error_log("ERROR: ".$e->getMessage());
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
        $apiFormat = Common\Lib\Settings::get('ui.api_format');
        $serializer = null;
        
        switch ($apiFormat) {
        	case '.json' : $serializer = new Common\Lib\JSONSerializer();
                return new AMQPMessage($serializer->serializeToString($proto));
        	   break;
        	case '.html' : $serializer = new Common\Lib\HTMLSerializer();
        	   break;
        	case '.php' : $serializer = new Common\Lib\PHPSerializer();
        	   break;
        	case '.proto' : $serializer = new Common\Lib\ProtobufSerializer();
        	   break;
        	case '.xml' : $serializer = new Common\Lib\XMLSerializer();
        	   break;
        	default : throw new Common\Exceptions\SolasMatchException(
        	   "Error: No API format specified or unsupported format."
            );
        }
        
        return new AMQPMessage($serializer->serialize($proto));
    }
}
