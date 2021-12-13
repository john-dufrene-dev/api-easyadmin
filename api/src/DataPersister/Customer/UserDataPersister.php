<?php

namespace App\DataPersister\Customer;

use App\Entity\Customer\User;
use App\Model\Customer\UserModel;
use App\Entity\Customer\UserToken;
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
    
    /**
     * supports
     *
     * @param  mixed $data
     * @param  mixed $context
     * @return bool
     */
    public function supports($data, array $context = []): bool
    {
        if ($context['item_operation_name'] !== 'user_put_uuid') {
            return false;
        }

        return $data instanceof User;
    }
    
    /**
     * persist
     *
     * @param  mixed $data
     * @param  mixed $context
     * @return Response
     */
    public function persist($data, array $context = []): ?Response
    {
        if (
            !$this->request->getCurrentRequest()->attributes->getBoolean('_api_persist', true)
        ) {
            return null;
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

        $content = $serializer->deserialize($this->request->getCurrentRequest()->getContent(), UserModel::class, 'json');

        $email = $content->getEmail() ? $content->getEmail() : null;
        $firstname = $content->getFirstname() ? $content->getFirstname() : $context['previous_data']->getUserInfo()->getFirstname();
        $lastname = $content->getLastname() ? $content->getLastname() : $context['previous_data']->getUserInfo()->getLastname();
        // You can use this format : DD/MM/YYYY or YYYY-MM-DDT00:00:00+00:00
        $birthday = (null !== $content->getBirthday())
            ? new \DateTime($content->getBirthday())
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
        if ($data->getPassword()) {
            $data->setPassword($context['previous_data']->getPassword());
        }

        // Update email, must create new jwt login check
        if ($data->getEmail() && null !== $email) {
            $data->setEmail($email);

            $userToken = $this->entityManager->getRepository(UserToken::class);

            // Remove all refresh token of the User when logout
            if ($refreshs = count($userToken->getAllByUser($data)) !== 0) {
                foreach ($userToken->getAllByUser($data) as $token) {
                    $this->entityManager->remove($token);
                }
            }
        }

        $message = ($data->getEmail() && $email)
            ? 'Success : User informations successfully updated, email is updated too, you must login with new email'
            : 'Success : User informations successfully updated';

        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return new JsonResponse([
            'code' => Response::HTTP_OK,
            'message' => $message
        ], Response::HTTP_OK);
    }

    public function remove($data, array $context = [])
    {
        // call your persistence layer to delete $data
    }
}
