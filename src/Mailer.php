<?php

namespace DigitalHammer\LpForms;

use \PHPMailer as PHPMailer;

class Mailer
{
    private $subject;
    private $message;

    private $mailFrom;
    private $nameFrom = '';
    private $mailTo;
    private $mail;

    public function __construct($from, $mailTo)
    {
        if (is_array($from))
        {
            $this->mailFrom = $from[0];
            $this->nameFrom = $from[1];
        } else {
            $this->mailFrom = $from;
        }

        $this->mailTo = $mailTo;
        $this->mail = new PHPMailer();
    }

    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function setMessageBody($message)
    {
        $this->message = $message;
    }

    public function send()
    {
        $this->mail->setFrom($this->mailFrom, $this->nameFrom);
        $this->mail->addAddress($this->mailTo);
        $this->mail->Subject = $this->subject;
        $this->mail->Body = $this->message;
        $this->mail->CharSet = 'UTF-8';

        return $this->mail->send();
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