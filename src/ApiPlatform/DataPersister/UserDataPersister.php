<?php


namespace App\ApiPlatform\DataPersister;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use App\Service\UserManager;


class UserDataPersister implements DataPersisterInterface
{
    public function __construct(private UserManager $userManager) {
    }

    /**
     * @param $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @param User $user
     * @return User
     */
    public function persist($user): User
    {
        $isNew = $user->getId() === null;
        $this->setPassword($user);

        if ($isNew) {
            $this->userManager->registerUser($user);
        } else {
            $this->userManager->updateUser($user);
        }

        return $user;
    }

    /**
     * @param User $user
     */
    private function setPassword(User $user): void
    {
        if (null !== $user->getPlainPassword()) {
            $this->userManager->setEncodedPassword($user, $user->getPlainPassword());
        }
    }

    public function remove($data)
    {
        $this->userManager->remove($data);
    }
}