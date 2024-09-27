<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    public function Login(AuthenticationUtils $AuthenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin_articles');
        }

        // get the login error if there is one
        $Error = $AuthenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $LastUsername = $AuthenticationUtils->getLastUsername();

        return $this->render('main/login.html.twig', ['last_username' => $LastUsername, 'error' => $Error]);
    }

    public function LoginToken(Request $Request, JWTTokenManagerInterface $JWTManager, UserProviderInterface $UserProvider, UserPasswordHasherInterface $Encoder): JsonResponse
    {
        $Content = json_decode($Request->getContent(), true);

        if (empty($Content['username']) || empty($Content['password']))
            return new JsonResponse(['message' => 'Email and password should not be empty', 'status' => '401'], JsonResponse::HTTP_BAD_REQUEST);

        try {
            $User = $UserProvider->loadUserByIdentifier($Content['username']);
        } catch (AuthenticationException $e) {
            return new JsonResponse(['message' => 'Invalid credentials.', 'status' => '401'], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$User instanceof UserInterface || !$Encoder->isPasswordValid($User, $Content['password']))
            return new JsonResponse(['message' => 'Invalid credentials.', 'status' => '401'], JsonResponse::HTTP_BAD_REQUEST);

        $Token = $JWTManager->create($User);

        return new JsonResponse(['token' => $Token, 'roles' => $User->getRoles(), 'status' => '200']);
    }
}
