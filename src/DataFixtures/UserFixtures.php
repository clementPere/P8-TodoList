<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

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
        $faker = Faker\Factory::create('fr_FR');
        for ($i = 0; $i < 10; $i++) {
            $manager->persist($this->setUser($faker->firstName, ["ROLE_USER"]));
        };
        $manager->persist($this->setUser("user", ["ROLE_USER"]));
        $manager->persist($this->setUser("anonyme", ["ROLE_USER"]));
        $manager->persist($this->setUser("admin", ["ROLE_ADMIN"]));
        $manager->flush();
    }

    private function setUser(string $username, array $role): User
    {

        $user = new User();
        $password = $this->hasher->hashPassword($user, 'test');
        return $user->setPassword($password)
            ->setUsername($username)
            ->setEmail($username . '@gmail.com')
            ->setRoles($role);
    }
}
