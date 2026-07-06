<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function create(User $user, ?string $plainPassword = null): void
    {
        if ($plainPassword) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $plainPassword)
            );
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function update(User $user, ?string $plainPassword = null): void
    {
        if ($plainPassword) {
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $plainPassword)
            );
        }

        $this->entityManager->flush();
    }

    public function delete(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    public function resetPassword(User $user, string $plainPassword): void
    {
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, $plainPassword)
        );
        $this->entityManager->flush();
    }
}
