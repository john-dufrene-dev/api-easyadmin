<?php

namespace App\Entity\Security;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Security\AdminConfigRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AdminConfigRepository::class)
 */
class AdminConfig
{
    /**
     * id - The unique auto incremented primary key
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * admin - Admin related to this config
     * 
     * @ORM\OneToOne(targetEntity=Admin::class, inversedBy="admin_config", cascade={"persist", "remove"})
     * @ORM\JoinColumn(referencedColumnName="uuid", nullable=false)
     */
    private $admin;

    /**
     * dashboard_title - Dashboard title of the connected admin
     * 
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dashboard_title;

    /**
     * crud_paginator - Crud paginator for this admin
     * 
     * @var int|null
     * 
     * @ORM\Column(type="smallint", nullable=true)
     * 
     * @Assert\Type(type="integer", message="asserts.entity.int.not_valid")
     * @Assert\LessThan(
     *      value = 301,
     *      message = "asserts.entity.int.less_than"
     * )
     */
    private $crud_paginator;

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getAdmin()->getEmail();
    }

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
     * getAdmin
     *
     * @return Admin
     */
    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    /**
     * setAdmin
     *
     * @param  mixed $admin
     * @return self
     */
    public function setAdmin(Admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * getDashboardTitle
     *
     * @return string
     */
    public function getDashboardTitle(): ?string
    {
        return $this->dashboard_title;
    }

    /**
     * setDashboardTitle
     *
     * @param  mixed $dashboard_title
     * @return self
     */
    public function setDashboardTitle(?string $dashboard_title): self
    {
        $this->dashboard_title = $dashboard_title;

        return $this;
    }

    /**
     * getCrudPaginator
     *
     * @return int
     */
    public function getCrudPaginator(): ?int
    {
        return $this->crud_paginator;
    }

    /**
     * setCrudPaginator
     *
     * @param  mixed $crud_paginator
     * @return self
     */
    public function setCrudPaginator(?int $crud_paginator): self
    {
        $this->crud_paginator = $crud_paginator;

        return $this;
    }
}
