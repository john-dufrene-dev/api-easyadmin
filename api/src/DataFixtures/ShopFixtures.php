<?php

namespace App\DataFixtures;

use App\Entity\Client\Shop;
use App\Entity\Security\Admin;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ShopFixtures extends Fixture
{
    public const DEFAULT_EMAIL = 'default@default.com';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $shop = new Shop();
        $shop->setEmail('shop@shop.com');

        $shop->setName('shop test');
        $shop->setCreatedAt(new \DateTime());
        $shop->setUpdatedAt(new \DateTime());

        $admins = $this->em->getRepository(Admin::class)->findAll();

        foreach ($admins as $admin) {
            if ($admin->getEmail() === self::DEFAULT_EMAIL) {
                $shop->addAdmin($admin);
            }
        }

        $manager->persist($shop);
        $manager->flush();
    }
}