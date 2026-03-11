<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/auth', name: 'auth_')]
class AuthController extends AbstractController
{
    /**
     * POST /auth/register
     *
     * Body JSON : { "email": "...", "password": "...", "displayName": "..." (optionnel) }
     *
     * Cette route est hors du firewall "api" et est publique
     * (firewall "register" avec security: false dans security.yaml).
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em,
        UserRepository $userRepository,
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        // Validation basique
        if (empty($data['email']) || empty($data['password'])) {
            return $this->json(['error' => 'Les champs email et password sont obligatoires.'], 400);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json(['error' => 'Format d\'email invalide.'], 400);
        }

        if (strlen($data['password']) < 6) {
            return $this->json(['error' => 'Le mot de passe doit contenir au moins 6 caractères.'], 400);
        }

        // Unicité de l'email
        if ($userRepository->findOneBy(['email' => strtolower(trim($data['email']))]) !== null) {
            return $this->json(['error' => 'Cet email est déjà utilisé.'], 409);
        }

        $user = new User();
        $user->setEmail(strtolower(trim($data['email'])));

        if (!empty($data['displayName'])) {
            $user->setDisplayName($data['displayName']);
        }

        $user->setPassword($hasher->hashPassword($user, $data['password']));

        $em->persist($user);
        $em->flush();

        return $this->json([
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
        ], 201);
    }
}
