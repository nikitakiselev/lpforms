<?php

namespace Nikitakiselev\LpForms;

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

    /**
     * @param string $from
     * @param string $mailTo
     * @param array $config
     */
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

    /**
     * @param bool $value
     */
    public function useSmtp($value = true)
    {
        $this->isSmtp = $value;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $message
     */
    public function setMessageBody($message)
    {
        $this->message = $message;
    }

    /**
     * @param string $emailTo
     */
    public function addAddress($emailTo)
    {
        $this->mailTo[] = $emailTo;
    }

    /**
     * @return mixed
     */
    public function send()
    {
        if ($this->isSmtp)
        {
            $this->mail->isSMTP();
            $this->mail->Host = $this->config('smtp_host');
            $this->mail->SMTPAuth = $this->config('smtp_auth', true);
            $this->mail->Username = $this->config('smtp_username');
            $this->mail->Password = $this->config('smtp_password');
            $this->mail->SMTPSecure = $this->config('smtp_secure', 'tls');
            $this->mail->Port = $this->config('smtp_port', 587);
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

    /**
     * @param string $key
     * @param string|null $default
     * @return string|null
     */
    protected function config($key, $default = null)
    {
        return isset($this->config[$key])
            ? $this->config[$key]
            : $default;
    }

    /**
     * Get mail sending errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->mail->ErrorInfo;
    }

    /**
     * Get mail errors inlined
     *
     * @return string
     */
    public function getErrorsInline()
    {
        return $this->getErrors();
    }
}
