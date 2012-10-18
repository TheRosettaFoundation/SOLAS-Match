<?php

use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessagingClient
{
    public $MainExchange = "SOLAS_MATCH";
    public $AlertsExchange = "ALERTS";

    public $TaskScoreTopic = "task.score";

    private $connection;

    private $host;
    private $port;
    private $user;
    private $pass;
    private $vhost;

    public function MessagingClient()
    {
        $settings = new Settings();
        $this->host = $settings->get('messaging.host');
        $this->port = $settings->get('messaging.port');
        $this->user = $settings->get('messaging.mess_user');
        $this->pass = $settings->get('messaging.mess_pass');
        $vhost = $settings->get('messaging.virtualhost');
        if($vhost != '') {
            $this->vhost = $vhost;
        }
    }

    public function init()
    {
        $ret = $this->openConnection();
        $this->user = false;
        $this->pass = false;
        return $ret;
    }

    private function openConnection()
    {
        $conn = false;
        $ret = false;

        try {
            $conn = new AMQPConnection($this->host, $this->port, $this->user, $this->pass);
        } catch(AMQPException $e) {
            echo "ERROR: ".$e->getMessage();
        } catch(Exception $e) {
            echo "ERROR: ".$e->getMessage();
        }

        if($conn) {
            $this->connection = $conn;
            $ret = true;
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
}
