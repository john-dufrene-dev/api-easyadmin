<?php

namespace App\DataFixtures;

use App\Entity\Customer\User;
use App\Entity\Customer\UserInfo;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const DEFAULT_FIRSTNAME = 'Firstname';
    public const DEFAULT_LASTNAME = 'Lastname';
    public const DEFAULT_GENDER = 'M';
    public const DEFAULT_PHONE = '+33601020102';

    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Default User
        $user = new User();
        $user_info = new UserInfo();

        $user->setEmail('user@user.com'); // don't forget to change address

        $user_info->setUser($user);
        $user_info->setFirstname(self::DEFAULT_FIRSTNAME);
        $user_info->setLastname(self::DEFAULT_LASTNAME);
        $user_info->setBirthday(new \DateTime());
        $user_info->setGender(self::DEFAULT_GENDER);
        $user_info->setPhone(self::DEFAULT_PHONE);
        $user->setUserInfo($user_info);

        $password = $this->encoder->encodePassword($user, 'user'); // don't forget to change password
        $user->setPassword($password);
        $user->setRoles(['ROLE__USER']);
        $user->setIsActive(1);
        $user->setIsVerified(1);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $manager->persist($user);

        $manager->flush();
    }
}
