<?php

namespace App\DataPersister\Customer;

use App\Entity\Customer\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserPasswordDataPersister implements ContextAwareDataPersisterInterface
{
    protected $request;

    protected $entityManager;

    protected $userPasswordEncoder;

    protected $validator;

    public function __construct(
        RequestStack $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordEncoder,
        ValidatorInterface $validator
    ) {
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;
    }

    public function supports($data, array $context = []): bool
    {
        if ($context['item_operation_name'] !== 'user_put_password') {
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

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $content = $serializer->deserialize($this->request->getCurrentRequest()->getContent(), User::class, 'json');

        // route /api/users/password to update User password
        $password = $content->getPassword() ? $content->getPassword() : null;
        $plainPassword = $content->getPlainPassword() ? $content->getPlainPassword() : null;

        // Check if password or plainpassword are null
        if (null === $plainPassword || null === $password || !isset($plainPassword) || !isset($password)) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request : Invalid Json parameter'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check if passwords are equal together
        if ($password !== $plainPassword) {
            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => 'Bad Request : Password and confirmation password are not equal'
            ], Response::HTTP_BAD_REQUEST);
        }

        if ($data->getPlainPassword()) {
            $data->setPassword(
                $this->userPasswordEncoder->hashPassword($data, $data->getPlainPassword())
            );
        }

        $data->setUpdatedAt(new \Datetime());

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return new JsonResponse([
            'code' => Response::HTTP_OK,
            'message' => 'Success : Password successfully updated'
        ], Response::HTTP_OK);
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}
