<?php

namespace App\DataFixtures;

use App\Entity\Security\Admin;
use App\Entity\Security\AdminGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AdminGroupFixtures extends Fixture
{
    public const DEFAULT_EMAIL = 'default@default.com';

    public const CLIENT_ROLES = [
        //Add Admin Roles
        'ROLE_ADMIN_ACTION_EDIT',
        'ROLE_ADMIN_ACTION_DETAIL',
        'ROLE_ADMIN_ACTION_EXPORT',
        //Add Shop Roles
        'ROLE_SHOP_ACTION_INDEX',
        'ROLE_SHOP_ACTION_EDIT',
        'ROLE_SHOP_ACTION_DETAIL',
        'ROLE_SHOP_ACTION_EXPORT',
        //Add User Roles
        'ROLE_USER_ACTION_INDEX',
        'ROLE_USER_ACTION_EDIT',
        'ROLE_USER_ACTION_DETAIL',
        'ROLE_USER_ACTION_EXPORT',
    ];

    public const API_ROLES = [
        //Add Api documentation Roles
        'ROLE_API_DOCUMENTATION',
    ];

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        // Add clients group
        $client = new AdminGroup();
        $client->setName('clients');

        $client->setRoles(self::CLIENT_ROLES);

        $client->setCreatedAt(new \DateTime());
        $client->setUpdatedAt(new \DateTime());

        // Add api group
        $api = new AdminGroup();
        $api->setName('api');

        $api->setRoles(self::API_ROLES);

        $api->setCreatedAt(new \DateTime());
        $api->setUpdatedAt(new \DateTime());

        $manager->persist($client);
        $manager->persist($api);

        //Association default Admin to clients group
        $admins = $this->em->getRepository(Admin::class)->findAll();

        foreach ($admins as $admin) {
            if ($admin->getEmail() === self::DEFAULT_EMAIL) {
                $client->addAdmin($admin);
            }
        }

        $manager->flush();
    }
}
