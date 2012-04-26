<?php

class Email {
    public function sendEmail($recipient, $subject, $body) {
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
        }
        
        $mailParams = "-f$mailFrom";
        $mailResult = mail($mailTo, $mailSubject, $mailBody, $mailHeader, $mailParams);
    }    
}
