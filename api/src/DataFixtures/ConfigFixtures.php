<?php

namespace App\DataFixtures;

use App\Entity\Configuration\Config;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ConfigFixtures extends Fixture
{
    public const CONF_START = 'CONF_';

    // Default title
    public const CONF_DEFAULT_DASHBOARD_NAME = 'DASHBOARD_TITLE';
    public const CONF_DEFAULT_DASHBOARD_VALUE = 'Default Admin Dashboard Title';
    public const CONF_DEFAULT_DASHBOARD_DESCRIPTION = 'Default Admin Dashboard Title';

    // Default paginator
    public const CONF_DEFAULT_PAGINATOR_NAME = 'DEFAULT_PAGINATOR';
    public const CONF_DEFAULT_PAGINATOR_VALUE = 15;
    public const CONF_DEFAULT_PAGINATOR_DESCRIPTION = 'Default Admin Pagination';

    public function load(ObjectManager $manager)
    {
        // Create configuration for default page admin title
        $config = new Config();
        $config->setName(self::CONF_START . self::CONF_DEFAULT_DASHBOARD_NAME);
        $config->setValue(self::CONF_DEFAULT_DASHBOARD_VALUE);
        $config->setTyping(0);
        $config->setDescription(self::CONF_DEFAULT_DASHBOARD_DESCRIPTION);
        $config->setCreatedAt(new \DateTime());
        $config->setUpdatedAt(new \DateTime());

        $manager->persist($config);

        // Create configuration for default paginator
        $config = new Config();
        $config->setName(self::CONF_START . self::CONF_DEFAULT_PAGINATOR_NAME);
        $config->setValue(self::CONF_DEFAULT_PAGINATOR_VALUE);
        $config->setTyping(5);
        $config->setDescription(self::CONF_DEFAULT_PAGINATOR_DESCRIPTION);
        $config->setCreatedAt(new \DateTime());
        $config->setUpdatedAt(new \DateTime());

        $manager->persist($config);

        $manager->flush();
    }
}
