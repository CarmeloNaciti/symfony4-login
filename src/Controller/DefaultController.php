<?php
/**
 * Created by PhpStorm.
 * User: cn
 * Date: 2018/01/08
 * Time: 2:02 PM
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    public function user(): Response
    {
        return $this->render('user/index.html.twig');
    }
}