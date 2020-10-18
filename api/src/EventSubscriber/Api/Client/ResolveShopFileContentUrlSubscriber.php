<?php

namespace App\EventSubscriber\Api\Client;

use App\Entity\Client\Shop;
use App\Entity\Client\ShopFile;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Vich\UploaderBundle\Storage\StorageInterface;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ResolveShopFileContentUrlSubscriber implements EventSubscriberInterface
{
    /**
     * storage
     *
     * @var mixed
     */
    private $storage;

    /**
     * __construct
     *
     * @param  mixed $storage
     * @return void
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * onPreSerialize
     *
     * @param  mixed $event
     * @return void
     */
    public function onPreSerialize(ViewEvent $event)
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();

        if ($controllerResult instanceof Response || !$request->attributes->getBoolean('_api_respond', true)) {
            return;
        }

        $attributes = RequestAttributesExtractor::extractAttributes($request);

        if (
            !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !\is_a($attributes['resource_class'], Shop::class, true)
        ) {
            return;
        }

        $shops = $controllerResult;

        if (!is_iterable($shops)) {
            $shops = [$shops];
        }

        foreach ($shops as $shop) {

            // Instance of Shop
            if (!$shop instanceof Shop) {
                continue;
            }

            foreach ($shop->getShopFiles() as $shopfile) {

                // Instance of ShopFile
                if (!$shopfile instanceof ShopFile) {
                    continue;
                }

                // Resolve ShopFile entity URL with shop_upload storage
                $shopfile->setImageName($this->storage->resolveUri($shopfile, 'image_file'));
            }
        }
    }

    /**
     * getSubscribedEvents
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onPreSerialize', EventPriorities::PRE_SERIALIZE],
        ];
    }
}
