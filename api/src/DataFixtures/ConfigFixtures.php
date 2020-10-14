<?php

namespace App\DataFixtures;

use App\Entity\Configuration\Config;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ConfigFixtures extends Fixture
{
    public const CONF_START = 'CONF_';
    public const CONF_DEFAULT_DASHBOARD_TITLE = 'DASHBOARD_TITLE';
    public const CONF_DEFAULT_DASHBOARD_VALUE = 'Default Admin Dashboard Title';

    public function load(ObjectManager $manager)
    {
        // Create configuration for default page admin title
        $config = new Config();
        $config->setName(self::CONF_START . self::CONF_DEFAULT_DASHBOARD_TITLE);
        $config->setValue(self::CONF_DEFAULT_DASHBOARD_VALUE);
        $config->setCreatedAt(new \DateTime());
        $config->setUpdatedAt(new \DateTime());

        $manager->persist($config);
        $manager->flush();
    }
}
