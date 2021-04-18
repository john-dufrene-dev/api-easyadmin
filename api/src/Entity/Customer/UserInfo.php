<?php

namespace App\Entity\Customer;

use App\Entity\Customer\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\Customer\UserInfoRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserInfoRepository::class)
 */
class UserInfo
{
    const GENDERS = ['M', 'F', 'O', null];

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
     * user - User related of the infos
     * 
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="user_info")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $user;

    /**
     * firstname - The firstname of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     */
    private $firstname;

    /**
     * lastname - The lastname of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     * 
     * @Assert\Length(
     *      min = 3,
     *      max = 255,
     *      minMessage = "asserts.entity.min_length",
     *      maxMessage = "asserts.entity.max_length"
     * )
     */
    private $lastname;

    /**
     * birthday - Date of birthday User
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $birthday;

    /**
     * gender - The gender of the User
     *
     * @var string|null
     * 
     * @ORM\Column(type="string", length=10, nullable=true)
     * 
     * @Assert\Choice(choices=UserInfo::GENDERS, message="asserts.entity.gender")
     */
    private $gender;

    /**
     * phone - The phone of the User
     * 
     * @var string|null
     *
     * @ORM\Column(type="string", length=16, nullable=true)
     * 
     * @Assert\Length(
     *      max = 16,
     *      maxMessage = "asserts.entity.max_length"
     * )
     * @Assert\Regex(
     *     pattern="/^([0-9\(\)\/\+ \-]*)$/",
     *     message="asserts.entity.phone"
     * )
     */
    private $phone;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * __toString
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getFullname();
    }

    /**
     * getUser
     *
     * @return User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * setUser
     *
     * @param  mixed $user
     * @return self
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * getFirstname
     *
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * setFirstname
     *
     * @param  mixed $firstname
     * @return self
     */
    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * getLastname
     *
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * setLastname
     *
     * @param  mixed $lastname
     * @return self
     */
    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * getFullname
     *
     * @return string
     */
    public function getFullname(): ?string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * getBirthday
     *
     * @return \DateTimeInterface
     */
    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    /**
     * setBirthday
     *
     * @param  mixed $birthday
     * @return self
     */
    public function setBirthday(?\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * getGender
     *
     * @return string
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        if (!\in_array($gender, self::GENDERS)) {
            throw new \InvalidArgumentException('Error with gender');
        }

        $this->gender = $gender;

        return $this;
    }

    /**
     * getPhone
     *
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * setPhone
     *
     * @param  mixed $phone
     * @return self
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}
