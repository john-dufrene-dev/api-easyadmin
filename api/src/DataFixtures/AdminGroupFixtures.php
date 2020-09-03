<?php

namespace App\DataFixtures;

use App\Entity\Security\AdminGroup;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AdminGroupFixtures extends Fixture
{
    public const CLIENT_ROLES = [
        'ROLE_ADMIN_ACTION_INDEX',
        'ROLE_ADMIN_ACTION_EDIT',
        'ROLE_ADMIN_ACTION_DETAIL',
    ];

    public function load(ObjectManager $manager)
    {
        $group = new AdminGroup();
        $group->setName('clients');

        $group->setRoles(self::CLIENT_ROLES);

        $group->setCreatedAt(new \DateTime());
        $group->setUpdatedAt(new \DateTime());

        $manager->persist($group);
        $manager->flush();
    }
}
