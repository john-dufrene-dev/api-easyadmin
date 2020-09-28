<?php

namespace App\Service\Admin\Traits\Entity;

use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ShopRepository::class)
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
     * @var \Ramsey\Uuid\UuidInterface
     * Todo : Change to uuidGenerator default symfony when release symfony 5.2
     * Todo : Change to assert \Uuid when release symfony 5.2
     *
     * @ORM\Id()
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     *
     * @Assert\Type("Ramsey\Uuid\UuidInterface")
     *
     * @ApiProperty(identifier=true)
     * 
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
