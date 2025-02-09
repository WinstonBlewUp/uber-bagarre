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

        $admin = new User();
        $admin->setEmail('playlist.robin@gmail.com');
        $admin->setUsername('RobinAdmin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setLatitude(48.8566);
        $admin->setLongitude(2.3522);
        $manager->persist($admin);

        $users = [];
        foreach (array_slice($fighters, 0, 5) as $name) {
            $user = new User();
            $user->setEmail(strtolower($name) . "@uberbagarre.com");
            $user->setUsername($name);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setLatitude(45 + mt_rand(0, 10) + (mt_rand(0, 100) / 100));
            $user->setLongitude(1 + mt_rand(0, 10) + (mt_rand(0, 100) / 100));
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
            $bagarreur->setLatitude(45 + mt_rand(0, 10) + (mt_rand(0, 100) / 100));
            $bagarreur->setLongitude(1 + mt_rand(0, 10) + (mt_rand(0, 100) / 100));
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

            $latitude = 48.8566 + mt_rand(-100, 100) / 1000.0;
            $longitude = 2.3522 + mt_rand(-150, 150) / 1000.0;
        
            $annonce->setLatitude($latitude);
            $annonce->setLongitude($longitude);
        
            $manager->persist($annonce);
        }
        

        $manager->flush();
    }
}
