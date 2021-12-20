<?php


namespace App\Service;


use App\Entity\User;
use App\Event\User\RegisterEvent;
use App\Event\User\UserRemovedEvent;
use App\Event\User\UserUpdatedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Redis;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class UserManager
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function setEncodedPassword(User $user, string $password): void
    {
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));
        $user->eraseCredentials();
    }

    /**
     * @param User $user
     */
    public function registerUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new RegisterEvent($user));
    }

    /**
     * @param User $user
     */
    public function updateUser(User $user): void
    {
        $this->entityManager->flush();
        $this->dispatcher->dispatch(new UserUpdatedEvent($user));
    }

    /**
     * @param User $user
     */
    public function remove(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(new UserRemovedEvent($user));
    }
}