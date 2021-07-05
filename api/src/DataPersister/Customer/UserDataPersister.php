<?php

namespace App\DataPersister\Customer;

use App\Entity\Customer\User;
use App\Entity\Customer\UserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersister implements ContextAwareDataPersisterInterface
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
        if ($context['item_operation_name'] !== 'user_put_uuid') {
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

        // route /api/users to update User informations
        $content = $serializer->deserialize(
            $this->request->getCurrentRequest()->getContent(),
            UserInfo::class,
            'json',
            [AbstractNormalizer::IGNORED_ATTRIBUTES => ['birthday']]
        );
        $datetime = json_decode($this->request->getCurrentRequest()->getContent(), true);
        $birthday_data = isset($datetime['birthday']) ? $datetime['birthday'] : null;

        $firstname = $content->getFirstname() ? $content->getFirstname() : $context['previous_data']->getUserInfo()->getFirstname();
        $lastname = $content->getLastname() ? $content->getLastname() : $context['previous_data']->getUserInfo()->getLastname();
        $birthday = (null !== $birthday_data)
            ? new \DateTime($birthday_data)
            : $context['previous_data']->getUserInfo()->getBirthday();
        $gender = $content->getGender() ? $content->getGender() : $context['previous_data']->getUserInfo()->getGender();
        $phone = $content->getPhone() ? $content->getPhone() : $context['previous_data']->getUserInfo()->getPhone();

        $errors_user_info = $this->validator->validate($content);

        if (0 !== count($errors_user_info)) {
            $errs = [];
            foreach ($errors_user_info as $error) {
                $errs = array_merge($errs, [$error->getMessage()]);
            }

            return new JsonResponse([
                'code' => Response::HTTP_BAD_REQUEST,
                'message' => $errs,
            ], Response::HTTP_BAD_REQUEST);
        }

        $data->getUserInfo()->setFirstname($firstname);
        $data->getUserInfo()->setLastname($lastname);
        $data->getUserInfo()->setBirthday($birthday);
        $data->getUserInfo()->setGender($gender);
        $data->getUserInfo()->setPhone($phone);
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
