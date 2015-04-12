<?php

namespace <<
NAMESPACEPREFIX >> \Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager,
    Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoder,
    Lexik\Bundle\JWTAuthenticationBundle\Security\Authentication\Token\JWTUserToken,
    Symfony\Component\HttpFoundation\Cookie,
    Symfony\Component\EventDispatcher\EventDispatcher;

class DefaultController extends Controller
{

    public function getTokenAction()
    {
        // The security layer will intercept this request
        error_log("The security layer will intercept this request");
        return new Response('', 401);
    }
}
