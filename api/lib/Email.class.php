<?php

class Email {
    public static function sendEmail($recipient, $subject, $body) {
        $mailTo         = $recipient;
        $mailSubject    = $subject;
        $mailBody       = $body;

        $settings       = new Settings();
        $mailFrom       = $settings->get('site.system_email_address');
        $site_name      = $settings->get('site.name');
        $site_url       = $settings->get('site.url');
        $monitor_email_address = $settings->get('site.notifications_monitor_email_address');

        $mailSignature = "\n\n-- \n";
        $mailSignature .= $site_name . "\n";
        $mailSignature .= '<' . $site_url . '>' . "\n";

        $mailBody .= $mailSignature;

        $mailHeader  = "From: $mailFrom\r\n";
        $mailHeader .= "Reply-To: $mailFrom\r\n";
        $mailHeader .= "X-Mailer: PHP " . phpversion() . "\r\n";    
        $mailHeader .= "X-Sender-IP: {$_SERVER['REMOTE_ADDR']}\r\n";

        if (!empty($monitor_email_address)) {
            $mailHeader .= "Bcc: " . $monitor_email_address . "\r\n";  
            $mailHeader .= "Content-type: text/html\r\n";
        }
        
        $mailParams = "$mailFrom";
 
        try {
           $mailResult = Email::SendMail($mailTo, $mailSubject, $mailBody, $mailHeader, $mailParams);
        }
        catch (Exception $e) {
            trigger_error("Error sending email: " . $e->getMessage(), E_USER_WARNING);
        }
    }
    
    static function SendMail ($mailTo, $mailSubject, $mailBody, $mailHeader, $mailParams)
    {
        $settings = new Settings();
        if ($SMTPIN = fsockopen ($settings->get('mail.smtp_server'), $settings->get('messaging.smtp_port')))
        {
            fputs ($SMTPIN, "EHLO ".$settings->get('mail.smtp_server')."\r\n");
            $talk["hello"] = fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            $talk["hello"] .= fgets ( $SMTPIN,2048);
            if(strpos($talk["hello"],"AUTH" )){
                 $talk["hello"] .= fgets ( $SMTPIN,2048);
                 $talk["hello"] .= fgets ( $SMTPIN,2048);
                 fputs ($SMTPIN, "auth login\r\n");
                 $talk["auth"] = fgets ( $SMTPIN, 2048);
                 fputs ($SMTPIN, base64_encode($settings->get('mail.smtp_user'))."\r\n");
                 $talk["username"] = fgets ( $SMTPIN, 2048 );
                 fputs ($SMTPIN, base64_encode($settings->get('mail.smtp_pass'))."\r\n");
                 $talk["pass"] = fgets ( $SMTPIN, 2048 );
            }
            
            fputs ($SMTPIN, "MAIL FROM: <".$mailParams.">\r\n");
            $talk["From"] = fgets ( $SMTPIN, 1024 );
            fputs ($SMTPIN, "RCPT TO: <".$mailTo.">\r\n");
            $talk["To"] = fgets ($SMTPIN, 1024);
            fputs($SMTPIN, "DATA\r\n");
            $talk["data"]=fgets( $SMTPIN,1024 );
            fputs($SMTPIN, "To: <".$mailTo.">\r\nFrom: <".$mailParams.">\r\nSubject:".$mailSubject."\r\n\r\n\r\n".$mailBody."\r\n.\r\n");
            $talk["send"]=fgets($SMTPIN,256);
            fputs ($SMTPIN, "QUIT\r\n");
            fclose($SMTPIN);
        }
        return $talk;
    } 
}
