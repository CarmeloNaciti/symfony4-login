<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/01/12
 * Time: 9:06 PM
 */

namespace App\Service;

use Carbon\Carbon;
use Symfony\Component\Routing\RouterInterface;

class EmailManager
{
    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(\Swift_Mailer $mailer, CacheInterface $cache, RouterInterface $router)
    {
        $this->cache = $cache;
        $this->mailer = $mailer;
        $this->router = $router;
    }

    public function sendPasswordReset(string $email): int
    {
        $timestamp = Carbon::now()->timestamp;
        $token = $this->cache->setToken($timestamp, $email);
        // @todo - Add base url
        $url = $this->router->generate('reset_password', ['timestamp' => $timestamp, 'token' => $token]);

        $message = (new \Swift_Message())
            ->setSubject('Password Reset Requested')
            ->setFrom('admin@example.com')
            ->setTo($email)
            ->addPart(
                'Please reset your password at the following link: ' . $url
            );

        return $this->mailer->send($message);
    }
}