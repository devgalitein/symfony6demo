<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
class EmailService
{
    private MailerInterface $mailer;
    private $senderEmail;

    public function __construct(MailerInterface $mailer,string $senderEmail)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
    }
    
    public function sendTemplatedEmail($to_email,$subject,$template_path,$parameter_array) {
        
        $smtp_user_email = $this->senderEmail;
        if ($to_email == '') {
            $to_email = $smtp_user_email;
        }
        $email = (new TemplatedEmail())
                ->to($to_email)
                ->subject($subject)
                ->htmlTemplate($template_path)
                ->context($parameter_array);
        
        $status = $this->mailer->send($email);
        
        return $status;
    }
    
    public function sendEmail($to_email,$subject,$html_body) {
        
        $email = (new Email())
                ->to($to_email)
                ->subject($subject)
                ->html($html_body);

        $status = $this->mailer->send($email);
        return $status;
    }

}