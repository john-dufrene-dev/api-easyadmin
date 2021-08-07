<?php

namespace App\DataPersister\Customer;

use App\Entity\Client\Shop;
use App\Entity\Customer\User;
use App\Entity\Customer\UserShopHistory;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class UserShopDataPersister implements ContextAwareDataPersisterInterface
{
    protected $request;

    protected $entityManager;

    protected $security;

    public function __construct(
        RequestStack $request,
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function supports($data, array $context = []): bool
    {
        if ($context['item_operation_name'] !== 'user_put_shop') {
            return false;
        }

        return $data instanceof User;
    }

    public function persist($data, array $context = [])
    {
        if (
            !$this->request->getCurrentRequest()->attributes->getBoolean('_api_persist', true)
        ) {
            return;
        }

        // Check if content is empty
        if (empty($this->request->getCurrentRequest()->getContent())) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request : Body content is empty'
            ], Response::HTTP_BAD_REQUEST);
        }

        // @todo switch to custom Model for serializer
        $content = json_decode($this->request->getCurrentRequest()->getContent(), true);
        $shop_content = isset($content['shop']) ? $content['shop'] : null;
        $shop_uuid = u($shop_content)->after('/api/shops/')->toString();
        $shop = $this->entityManager->getRepository(Shop::class)->findOneBy(['uuid' => $shop_uuid]);
        $user = $this->security->getUser();

        // Insert history User Shop
        if (null !== $user->getShop()) {
            $user_history = $this->entityManager->getRepository(UserShopHistory::class);
            $check = $user_history->findOneBy([
                'shop_reference' => $shop->getReference(),
                'user_reference' => $user->getReference()
            ]);

            if (!$check) {
                $user_history = new UserShopHistory();
                $user_history->setUserReference($user->getReference());
                $user_history->setShopReference($shop->getReference());
                $this->entityManager->persist($user_history);
            }
        }

        $data->setUpdatedAt(new \Datetime());

        // block update with previous data
        $data->setPassword($context['previous_data']->getPassword());

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}
