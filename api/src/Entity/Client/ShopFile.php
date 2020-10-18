<?php

namespace App\Entity\Client;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\Client\ShopFileRepository;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass=ShopFileRepository::class)
 * 
 * @Vich\Uploadable
 */
class ShopFile
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
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     * image_file - The VichUpload Adapter for image
     * 
     * @Vich\UploadableField(mapping="shop_upload", fileNameProperty="image_name", originalName="original_name", size="image_size", mimeType="mime_type", dimensions="dimensions")
     * 
     * @var File|null
     */
    private $image_file;

    /**
     * image_name - The name of the image
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     * 
     * @Groups({"shop:readOne"})
     */
    private $image_name;

    /**
     * original_name - Original name of the image
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $original_name;

    /**
     * image_size - The size of the image
     * 
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $image_size;

    /**
     * mime_type - The mime type of the image
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $mime_type;

    /**
     * dimensions - Dimensions of the image
     * 
     * @ORM\Column(type="simple_array", nullable=true)
     *
     * @var array|null
     */
    private $dimensions;

    /**
     * shop - Shop related of the images
     * 
     * @ORM\ManyToOne(targetEntity=Shop::class, inversedBy="shop_files")
     * @ORM\JoinColumn(referencedColumnName="uuid")
     */
    private $shop;

    /**
     * created_at - Date of created Shop File
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.created_at.not_null")
     */
    private $created_at;

    /**
     * updated_at - Date of updated Shop File
     *
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime")
     *
     * @Assert\NotNull(message="asserts.entity.updated_at.not_null")
     */
    private $updated_at;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
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
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $image_file
     */
    public function setImageFile(?File $image_file = null): void
    {
        $this->image_file = $image_file;

        if (null !== $image_file) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTime();
        }
    }

    /**
     * getImageFile
     *
     * @return File
     */
    public function getImageFile(): ?File
    {
        return $this->image_file;
    }

    /**
     * getImageName
     *
     * @return string
     */
    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    /**
     * setImageName
     *
     * @param  mixed $image_name
     * @return self
     */
    public function setImageName(?string $image_name): self
    {
        $this->image_name = $image_name;

        return $this;
    }

    /**
     * getOriginalName
     *
     * @return string
     */
    public function getOriginalName(): ?string
    {
        return $this->original_name;
    }

    /**
     * setOriginalName
     *
     * @param  mixed $original_name
     * @return void
     */
    public function setOriginalName(?string $original_name): void
    {
        $this->original_name = $original_name;
    }

    /**
     * getImageSize
     *
     * @return int
     */
    public function getImageSize(): ?int
    {
        return $this->image_size;
    }

    /**
     * setImageSize
     *
     * @param  mixed $image_size
     * @return self
     */
    public function setImageSize(?int $image_size): self
    {
        $this->image_size = $image_size;

        return $this;
    }

    /**
     * getMimeType
     *
     * @return string
     */
    public function getMimeType(): ?string
    {
        return $this->mime_type;
    }

    /**
     * setMimeType
     *
     * @param  mixed $mime_type
     * @return void
     */
    public function setMimeType(?string $mime_type): void
    {
        $this->mime_type = $mime_type;
    }

    /**
     * getDimensions
     *
     * @return array
     */
    public function getDimensions(): ?array
    {
        return $this->dimensions;
    }

    /**
     * setDimensions
     *
     * @param  mixed $dimensions
     * @return void
     */
    public function setDimensions(?array $dimensions): void
    {
        $this->dimensions = $dimensions;
    }

    /**
     * getShop
     *
     * @return Shop
     */
    public function getShop(): ?Shop
    {
        return $this->shop;
    }

    /**
     * setShop
     *
     * @param  mixed $shop
     * @return self
     */
    public function setShop(?Shop $shop): self
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * getCreatedAt
     *
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * setCreatedAt
     *
     * @param  mixed $created_at
     * @return self
     */
    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * getUpdatedAt
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * setUpdatedAt
     *
     * @param  mixed $updated_at
     * @return self
     */
    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
