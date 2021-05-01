<?php

namespace App\Controller\Api\Auth\ResetPassword;

use App\Entity\Customer\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Api\Builder\ApiResponseBuilder;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * @Route("/api")
 */
class ResetPasswordController extends AbstractController
{
    /**
     * em
     *
     * @var mixed
     */
    private $em;

    /**
     * params
     *
     * @var mixed
     */
    private $params;

    /**
     * __construct
     *
     * @return void
     */
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params)
    {
        $this->em = $em;
        $this->params = $params;
    }

    /**
     * @Route("/auth/reset/password", name="api_reset_password")
     * 
     * @IsGranted("ROLE__USER")
     * 
     * @param  mixed $request
     * @return JsonResponse
     */
    public function ResetPasswordSecretApi(
        Request $request,
        ApiResponseBuilder $apiResponseBuilder,
        ValidatorInterface $validator,
        UserPasswordEncoderInterface $passwordEncoder,
        JWTTokenManagerInterface $JWTManager,
        RefreshTokenManagerInterface $refreshTokenManager
    ): JsonResponse {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        // Check if POST Method
        if (!$request->isMethod('POST')) {
            return $apiResponseBuilder->CheckIfMethodPost();
        }

        // Check if it's json contentType
        if (!\in_array($request->headers->get('content_type'), ApiResponseBuilder::CONTENT_TYPE, true)) {
            return $apiResponseBuilder->checkIfAcceptContentType();
        }

        // Check if content is empty
        if (empty($request->getContent())) {
            return $apiResponseBuilder->checkIfBodyIsEmpty();
        }

        $content = $serializer->deserialize($request->getContent(), User::class, 'json');
        $password = $content->getPassword() ? $content->getPassword() : null;
        $confirm_password = $content->getPlainPassword() ? $content->getPlainPassword() : null;

        // Check if valid parameters
        if (null === $password || !isset($password) || null === $confirm_password || !isset($confirm_password)) {
            return $apiResponseBuilder->checkIfInvalidQueryParameters();
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getEmail()]);

        // Check if User exist
        if (!$user) {
            return $apiResponseBuilder->checkIfInvalidUser();
        }

        $user->setPassword($password);
        $user->setPlainPassword($confirm_password);

        $errs = [];
        $errors_user = $validator->validate($user);

        // Check validations constraints
        if (0 !== count($errors_user)) {
            foreach ($errors_user as $error) {
                $errs = array_merge($errs, [$error->getMessage()]);
            }

            return $apiResponseBuilder->checkIfErrorsBadRequest($errs);
        }

        // Check if passwords are equal together
        if ($password !== $confirm_password) {
            return $apiResponseBuilder->checkIfValueAreEqualTo();
        }

        // Encode the plain password, and set it.
        $encodedPassword = $passwordEncoder->encodePassword($user, $confirm_password);
        $user->setPassword($encodedPassword);

        $token = $JWTManager->create($user);

        // Check if token is valid
        if (!$token) {
            return $apiResponseBuilder->checkIfInvalidToken();
        }

        $datetime = new \DateTime();
        $datetime->modify('+' . $this->params->get('gesdinet_jwt_refresh_token.ttl') . ' seconds');

        // Create the refresh token User
        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getUsername());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid($datetime);

        $refreshTokenManager->save($refreshToken);

        $this->em->persist($user);
        $this->em->flush();

        // @todo : Send confirmation reset password to the User

        return $this->json([
            'token' => $JWTManager->create($user),
            'refresh_token' => $refreshToken->getRefreshToken()
        ], Response::HTTP_OK);
    }
}
