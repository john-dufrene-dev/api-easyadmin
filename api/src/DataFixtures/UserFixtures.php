<?php

namespace App\DataFixtures;

use App\Entity\Customer\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager)
    {
        // Default User
        $user = new User();
        $user->setEmail('user@user.com'); // don't forget to change address
        
        $password = $this->encoder->encodePassword($user, 'user'); // don't forget to change password
        $user->setPassword($password);
        $user->setRoles(['ROLE__USER']);
        $user->setIsActive(1);
        $user->setCreatedAt(new \DateTime());
        $user->setUpdatedAt(new \DateTime());

        $manager->persist($user);

        $manager->flush();
    }
}
