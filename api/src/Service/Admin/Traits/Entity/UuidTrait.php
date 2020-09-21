<?php

namespace App\Service\Admin\Traits\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use ApiPlatform\Core\Annotation\ApiProperty;

trait UuidTrait
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Column(type="integer", columnDefinition="INT AUTO_INCREMENT NOT NULL", unique=true)
     * @ORM\GeneratedValue()
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id()
     * @ApiProperty(identifier=true)
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $uuid;
    
    /**
     * getId
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
    
    /**
     * getUuid
     *
     * @return UuidInterface
     */
    public function getUuid(): ?UuidInterface
    {
        return $this->uuid;
    }

    /**
     * @var \Ramsey\Uuid\UuidInterface
     * Display unmapped Uuid in form
     */
    public function DisplayUuid(): ?UuidInterface
    {
        return $this->uuid;
    }
}
