<?php

namespace App\DataFixtures;

use App\Entity\Security\Admin;
use Symfony\Component\Uid\Uuid;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $uuid = Uuid::v1();

        $admin = new Admin();
        $admin->setEmail('admin@admin.com'); // don't forget to change address
        $admin->setUuid($uuid);
        
        $password = $this->encoder->encodePassword($admin, 'admin'); // don't forget to change password
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());

        $manager->persist($admin);
        $manager->flush();
    }
}
