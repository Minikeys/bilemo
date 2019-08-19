<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\Annotations as Rest;

class AuthController extends AbstractController
{
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();

        $username = $request->request->get('username');
        $password = $request->request->get('password');

        $user = new User();
        $user->setUsername($username);
        $user->setPassword($passwordEncoder->encodePassword(
            $user,
            $password
        ));
        $em->persist($user);
        $em->flush();
        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }
    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }

    /**
     * @Route(name="login", path="/login_check")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function login()
    {
        $user = $this->getUser();

        return $this->json(array(
            'username' => $user->getUsername(),
            'role' => $user->getRoles(),
        ));
    }
}
