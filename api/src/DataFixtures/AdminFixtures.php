<?php

namespace App\DataFixtures;

use App\Entity\Security\Admin;
use App\Entity\Security\AdminConfig;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Default Admin
        $admin = new Admin();
        $admin_config = new AdminConfig();

        $admin->setEmail('admin@admin.com'); // don't forget to change address

        $password = $this->encoder->hashPassword($admin, 'admin'); // don't forget to change password
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']);
        $admin->setIsAdmin(1);
        $admin->setCreatedAt(new \DateTime());
        $admin->setUpdatedAt(new \DateTime());

        $admin_config->setAdmin($admin);
        $admin_config->setDashboardTitle('Default Admin Title');
        $admin_config->setCrudPaginator(30);
        $admin->setAdminConfig($admin_config);

        $manager->persist($admin);

        // Test Admin
        $test = new Admin();
        $test_config = new AdminConfig();
        $test->setEmail('default@default.com');

        $password = $this->encoder->hashPassword($test, 'test'); // don't forget to change password
        $test->setPassword($password);
        $test->setCreatedAt(new \DateTime());
        $test->setUpdatedAt(new \DateTime());

        $test_config->setAdmin($test);
        $test_config->setDashboardTitle('Default Test Title');
        $test_config->setCrudPaginator(50);
        $test->setAdminConfig($test_config);

        $manager->persist($test);

        $manager->flush();
    }
}
