<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $manager->persist($this->setUser("user" . $i, ["ROLE_USER"]));
        };
        $manager->persist($this->setUser("admin", ["ROLE_ADMIN"]));
        $manager->flush();
    }

    private function setUser(string $username, array $role): User
    {
        $user = new User();
        $password = $this->hasher->hashPassword($user, 'test');
        return $user->setPassword($password)
            ->setUsername($username)
            ->setEmail($username . '@test.com')
            ->setRoles($role);
    }
}
