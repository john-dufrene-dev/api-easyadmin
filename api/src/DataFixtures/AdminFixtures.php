<?php

namespace App\DataFixtures;

use App\Entity\Security\Admin;
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
        // Default Admin
        $admin = new Admin();
        $admin->setEmail('admin@admin.com'); // don't forget to change address

        $password = $this->encoder->encodePassword($admin, 'admin'); // don't forget to change password
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $admin->setIsAdmin(1);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());

        $manager->persist($admin);

        // Test Admin
        $test = new Admin();
        $test->setEmail('default@default.com');

        $password = $this->encoder->encodePassword($test, 'test'); // don't forget to change password
        $test->setPassword($password);
        $test->setCreatedAt(new \DateTime());
        $test->setUpdatedAt(new \DateTime());

        $manager->persist($test);

        $manager->flush();
    }
}
