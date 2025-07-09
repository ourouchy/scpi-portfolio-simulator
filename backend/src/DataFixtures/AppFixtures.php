<?php
namespace App\DataFixtures;

use App\Entity\Scpi;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // SCPI de démo
        $scpis = [
            ['nom' => 'SCPI Alpha', 'taux' => 4.5],
            ['nom' => 'SCPI Beta', 'taux' => 5.1],
            ['nom' => 'SCPI Gamma', 'taux' => 4.2],
        ];
        foreach ($scpis as $data) {
            $scpi = new Scpi();
            $scpi->setNom($data['nom']);
            $scpi->setTauxRendementAnnuel($data['taux']);
            $manager->persist($scpi);
        }

        // Utilisateur de démo
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $hashed = $this->hasher->hashPassword($user, 'password');
        $user->setPassword($hashed);
        $manager->persist($user);

        $manager->flush();
    }
}
