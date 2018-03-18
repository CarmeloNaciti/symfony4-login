<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/03/18
 * Time: 12:22 PM
 */

namespace App\Service;

use Predis\Client as PredisClient;

class CacheManager implements CacheInterface
{
    private const TOKEN_LENGTH = 78;
    private const TOKEN_EXPIRY = 1800;

    /**
     * @var PredisClient
     */
    private $client;

    public function __construct()
    {
        $this->client = new PredisClient();
    }

    public function setToken(string $id, string $email): string
    {
        $key = sprintf('token:%s', $id);
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));

        $this->client->set($key, serialize(['email' => $email, 'token' => $token]));
        $this->client->expire($key, self::TOKEN_EXPIRY);

        return $token;
    }

    public function getEmailByToken(string $timestamp, string $token): string
    {
        $data = $this->client->get(sprintf('token:%s', $timestamp));

        if (empty($data)) {
            throw new \Exception();
        }

        $data = unserialize($data);

        if ($token === $data['token']) {
            return $data['email'];
        }

        throw new \Exception();
    }
}