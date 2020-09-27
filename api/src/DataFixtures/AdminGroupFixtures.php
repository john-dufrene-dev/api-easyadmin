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
        //Add Admin Role
        'ROLE_ADMIN_ACTION_EDIT',
        'ROLE_ADMIN_ACTION_DETAIL',
        //Add Shop Role
        'ROLE_SHOP_ACTION_INDEX',
        'ROLE_SHOP_ACTION_EDIT',
        'ROLE_SHOP_ACTION_DETAIL',
    ];

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $group = new AdminGroup();
        $group->setName('clients');

        $group->setRoles(self::CLIENT_ROLES);

        $group->setCreatedAt(new \DateTime());
        $group->setUpdatedAt(new \DateTime());

        $admins = $this->em->getRepository(Admin::class)->findAll();

        foreach ($admins as $admin) {
            if ($admin->getEmail() === self::DEFAULT_EMAIL) {
                $group->addAdmin($admin);
            }
        }

        $manager->persist($group);
        $manager->flush();
    }
}
