<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/03/18
 * Time: 1:08 PM
 */

namespace App\Service;

interface CacheInterface
{
    public function __construct();

    public function setToken(string $timestamp, string $email): string;

    public function getEmailByToken(string $id, string $token): string;
}