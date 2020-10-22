<?php

namespace App\DataFixtures;

use App\Entity\Client\Shop;
use App\Entity\Security\Admin;
use App\Entity\Client\ShopInfo;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Service\Traits\Entity\ShopHourTrait;

class ShopFixtures extends Fixture
{
    use ShopHourTrait;

    public const DEFAULT_EMAIL = 'default@default.com';
    public const DEFAULT_SHOP_EMAIL = 'shop@shop.com';
    public const DEFAULT_SHOP = 'shop test';
    public const DEFAULT_COUNTRY = 'US';

    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function load(ObjectManager $manager)
    {
        $shop = new Shop();
        $shop_info = new ShopInfo();

        $shop->setEmail(self::DEFAULT_SHOP_EMAIL);

        $shop->setName(self::DEFAULT_SHOP);
        $shop->setCreatedAt(new \DateTime());
        $shop->setUpdatedAt(new \DateTime());

        $shop_info->setShop($shop);
        $shop_info->setCountry(self::DEFAULT_COUNTRY);
        $shop_info->setShopHour($this->getShopHourFormattedValues());
        $shop->setShopInfo($shop_info);

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
