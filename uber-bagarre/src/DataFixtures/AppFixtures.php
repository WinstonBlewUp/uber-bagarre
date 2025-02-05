<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Annonce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $fighters = [
            'Ali', 'Tyson', 'McGregor', 'Khabib', 'Mayweather',
            'Jones', 'Adesanya', 'Ngannou', 'Canelo', 'Fury',
            'Hagler', 'Pacquiao', 'Holm', 'Rousey', 'GSP',
        ];

        $users = [];
        foreach (array_slice($fighters, 0, 5) as $name) {
            $user = new User();
            $user->setEmail(strtolower($name) . "@uberbagarre.com");
            $user->setUsername($name);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);
            $users[] = $user;
        }

        $bagarreurs = [];
        foreach (array_slice($fighters, 5) as $name) {
            $bagarreur = new User();
            $bagarreur->setEmail(strtolower($name) . "@uberbagarre.com");
            $bagarreur->setUsername($name);
            $bagarreur->setRoles(['ROLE_BAGARREUR']);
            $bagarreur->setPassword($this->passwordHasher->hashPassword($bagarreur, 'fight'));
            $bagarreur->setDescription("Expert en combat de rue, redouté dans l'octogone.");
            $bagarreur->setWeight(mt_rand(70, 120));
            $bagarreur->setHeight(mt_rand(160, 210));
            $bagarreur->setVictories(mt_rand(0, 50));
            $bagarreur->setScore($bagarreur->getVictories() * 10);
            $manager->persist($bagarreur);

            $bagarreurs[] = $bagarreur;
        }

        for ($i = 1; $i <= 10; $i++) {
            $annonce = new Annonce();
            $annonce->setTitle("Bagarre N°$i");
            $annonce->setDescription("Une grosse bagarre à venir !");
            $annonce->setReward(mt_rand(10, 100));
            $annonce->setDate(new \DateTimeImmutable());
            $annonce->setCreatedBy($users[array_rand($users)]);
            $manager->persist($annonce);
        }

        $manager->flush();
    }
}
