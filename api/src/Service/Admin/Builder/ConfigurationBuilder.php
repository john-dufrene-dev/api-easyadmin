<?php

namespace App\Service\Admin\Builder;

use App\Entity\Configuration\Config;
use Doctrine\ORM\EntityManagerInterface;

/**
 * ConfigurationBuilder
 */
class ConfigurationBuilder
{
    /**
     * em
     *
     * @var mixed
     */
    protected $em;

    /**
     * __construct
     *
     * @param  mixed $config
     * @return void
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * get
     *
     * @param  mixed $name
     * @return string
     */
    public function get($name): ?string
    {
        if (!$config = $this->em->getRepository(Config::class)->findOneBy(['name' => $name, 'is_active' => 1])) {
            return null;
        }

        return $config->getValue();
    }

    /**
     * update
     *
     * @param  mixed $name
     * @param  mixed $value
     * @return bool
     */
    public function update($name, $value = null): ?bool
    {
        if (!$config = $this->em->getRepository(Config::class)->findOneBy(['name' => $name])) {
            return false;
        }

        $config->setValue($value);
        $config->setUpdatedAt(new \DateTime());

        $this->em->persist($config);
        $this->em->flush();

        return true;
    }
}
