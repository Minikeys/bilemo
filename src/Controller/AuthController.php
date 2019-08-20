<?php

namespace App\Controller;

use App\Form\UserType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;

class AuthController extends AbstractFOSRestController
{
    /**
     * @Route(name="register", path="/api/register")
     * @Rest\Post(
     *     "/api/register",
     *     name = "register"
     * )
     * @SWG\Tag(name="users")
     *
     * @SWG\Response(
     *     response=201,
     *     description="Returns success.",
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns errors.",
     * )
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {

        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($passwordEncoder->encodePassword(
                $user,
                $data['password']
            ));
            $em->persist($user);
            $em->flush();
            $username = $user->getUsername();
            return $this->handleView($this->view(['status' => 'Account '. $username .' create success.'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors(), Response::HTTP_BAD_REQUEST));


    }


    /**
    /**
     * @Route(name="api", path="/api")
     * @Rest\Get(
     *     "/api",
     *     name = "api"
     * )
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns logged as.",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="JWT Token error.",
     * )
     *
     * @return Response
     */
    public function api()
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }

    /**
     * @Route(name="login", path="/api/login_check")
     * @Rest\Post(
     *     "/api/login_check",
     *     name = "login"
     * )
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @SWG\Tag(name="users")
     * @SWG\Response(
     *     response=200,
     *     description="Returns token.",
     * )
     * @SWG\Response(
     *     response=401,
     *     description="Bad credentials.",
     * )
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
