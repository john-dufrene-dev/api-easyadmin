<?php

namespace App\Service\Traits\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity()
 *
 * @UniqueEntity(
 *     fields={"id", "uuid"},
 *     message="asserts.entity.unique"
 * )
 */
trait UuidTrait
{
    /**
     * id - The unique auto incremented id (not the primary key)
     *
     * @var int|null
     *
     * @ORM\Column(type="integer", columnDefinition="INT AUTO_INCREMENT NOT NULL", unique=true)
     * @ORM\GeneratedValue()
     *
     * @ApiProperty(identifier=false)
     */
    private $id;

    /**
     * uuid - The unique auto incremented primary key
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     *
     * @Assert\Uuid
     * 
     * @Groups({"shop:readOne", "shop:readAll", "user:readOne", "user:update"})
     *
     * @ApiProperty(identifier=true)
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
     * @return UuidV4
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Display unmapped Uuid in form
     */
    public function DisplayUuid()
    {
        return $this->uuid;
    }
}
