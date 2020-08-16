<?php

//App/Controller/SecurityController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SecurityController extends AbstractController
{
    
    public function logout():void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }
}