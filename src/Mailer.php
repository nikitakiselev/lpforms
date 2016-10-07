<?php

namespace DigitalHammer\LpForms;

use PHPMailer as PHPMailer;

class Mailer
{
    private $subject;
    private $message;

    private $mailFrom;
    private $nameFrom = '';
    private $mailTo = [];
    private $mail;
    private $isSmtp = false;
    private $config;

    public function __construct($from, $mailTo, $config = [])
    {
        if (is_array($from))
        {
            $this->mailFrom = $from[0];
            $this->nameFrom = $from[1];
        } else {
            $this->mailFrom = $from;
        }

        $this->mailTo[] = $mailTo;
        $this->mail = new PHPMailer();
        $this->config = $config;
    }

    public function useSmtp($value = true)
    {
        $this->isSmtp = $value;
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setMessageBody($message)
    {
        $this->message = $message;
    }

    public function addAddress($emailTo)
    {
        return $this->mailTo[] = $emailTo;
    }

    public function send()
    {
        if ($this->isSmtp)
        {
            $this->mail->isSMTP();                                      // Set mailer to use SMTP
            $this->mail->Host = $this->config('smtp_host');             // Specify main and backup SMTP servers
            $this->mail->SMTPAuth = $this->config('smtp_auth', true);                               // Enable SMTP authentication
            $this->mail->Username = $this->config('smtp_username');     // SMTP username
            $this->mail->Password = $this->config('smtp_password');     // SMTP password
            $this->mail->SMTPSecure = $this->config('smtp_secure', 'tls');                            // Enable TLS encryption, `ssl` also accepted
            $this->mail->Port = $this->config('smtp_port', 587);        // TCP port to connect to
        }

        $this->mail->setFrom($this->mailFrom, $this->nameFrom);

        foreach ($this->mailTo as $address) {
            $this->mail->addAddress($address);
        }
        
        $this->mail->isHTML($this->config('html', true));
        $this->mail->Subject = $this->subject;
        $this->mail->Body = $this->message;
        $this->mail->CharSet = 'UTF-8';

        return $this->mail->send();
    }

    protected function config($key, $default = null)
    {
        return isset($this->config[$key])
            ? $this->config[$key]
            : $default;
    }

    public function getErrors()
    {
        return $this->mail->ErrorInfo;
    }

    public function getErrorsInline()
    {
        return $this->getErrors();
    }
}
