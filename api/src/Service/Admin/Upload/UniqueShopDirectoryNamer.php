<?php

namespace App\Service\Admin\Upload;

use App\Entity\Client\ShopFile;
use function symfony\component\string\u;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class UniqueShopDirectoryNamer implements DirectoryNamerInterface
{
    
    /**
     * directoryName - Gets the configured directory namer
     * 
     * @param  mixed $object
     * @param  mixed $mapping
     * @return string
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        // Create directory for Shop uploads
        if ($object instanceof ShopFile) {
            if (null !== u($mapping->getFileName($object))->before('__')) {
                return u($mapping->getFileName($object))->before('__');
            }
        }

        // Add here your other custom directory namer for others Entities
    }
}
