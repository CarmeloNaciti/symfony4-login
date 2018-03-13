<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/01/12
 * Time: 9:06 PM
 */

namespace App\Service;

class EmailManager
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendPasswordReset(string $email): int
    {
        $message = (new \Swift_Message())
            ->setSubject('Password Reset Requested')
            ->setFrom('admin@example.com')
            ->setTo($email)
            ->addPart(
                'Please reset your password at the following link:'
            );

        return $this->mailer->send($message);
    }
}