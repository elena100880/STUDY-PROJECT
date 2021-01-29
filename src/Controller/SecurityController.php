<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
    {
        //$request= Request::createFromGlobals();
        //$path=$request->query->get('_target_path'); 

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $contents = $this->renderView('security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
                //'path' => $path,
                
            ],
        );
        return new Response($contents);
    }
}