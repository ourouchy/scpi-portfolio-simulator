<?php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AuthController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);

        // Vérifier si l'email est valide
        $emailErrors = $validator->validateProperty($user, 'email');
        if (count($emailErrors) > 0) {
            return $this->json(['error' => (string) $emailErrors], 400);
        }

        // Vérifier si l'email existe déjà
        $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($existing) {
            return $this->json(['error' => 'Email déjà utilisé'], 409);
        }

        $hashedPassword = $hasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        return $this->json(['success' => true, 'userId' => $user->getId()]);
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, TokenStorageInterface $tokenStorage): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return $this->json(['error' => 'Email et mot de passe requis'], 400);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user) {
            return $this->json(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        if (!$hasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => 'Email ou mot de passe incorrect'], 401);
        }

        // S'assurer que la session est démarrée
        $session = $request->getSession();
        if (!$session->isStarted()) {
            $session->start();
        }

        // Authentifier l'utilisateur via le token storage
        $token = new UsernamePasswordToken($user, 'api', $user->getRoles());
        $tokenStorage->setToken($token);
        $session->set('_security_api', serialize($token));

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }

    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request, TokenStorageInterface $tokenStorage): JsonResponse
    {
        // Supprimer le token de sécurité
        $tokenStorage->setToken(null);
        
        // Invalider la session
        $request->getSession()->invalidate();
        
        return $this->json(['success' => true]);
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->json(['error' => 'Non authentifié'], 401);
        }

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }
}
