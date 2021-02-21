<?php

namespace App\Service\Admin\Upload;

use App\Entity\Client\ShopFile;
use Vich\UploaderBundle\Naming\NamerInterface;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\Polyfill\FileExtensionTrait;

class UniqueShopNamer implements NamerInterface
{
    use FileExtensionTrait;

    /**
     * name - Gets the configured  namer
     *
     * @param  mixed $object
     * @param  mixed $mapping
     * @return string
     */
    public function name($object, PropertyMapping $mapping): string
    {
        // Create name for Shop uploads
        if ($object instanceof ShopFile) {

            // @todo : Replace with u() string function
            $file = $mapping->getFile($object);
            $name = \str_replace('.', '', \uniqid('', true));
            $extension = $this->getExtension($file);

            if (\is_string($extension) && '' !== $extension) {
                $name = \sprintf('%s.%s', $name, $extension);
            }

            return $object->getShop()->getUuid()->toRfc4122() . '__' . $name;
        }

        // Add here your other custom file namer for others Entities
    }
}
