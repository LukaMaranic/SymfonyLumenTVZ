<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\AppUser;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppUserFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            AppGroupFixtures::class,
        ];
    }

    public function load(ObjectManager $manager)
    {
        $date=new \DateTime();
        $faker = Faker\Factory::create('hr_HR');
        for ($j=0; $j<10; $j++) {
            $user = new AppUser();
            $user->setUsername($faker->userName);
            $user->setIsEmployer($faker->boolean);
            $user->setEmail($faker->email);
            $user->setPhoneNumber($faker->phoneNumber);
            $user->setPassword(sha1(12345));
            $user->setDateModified($date);
            $user->setDateCreated($date);
            $user->setAddress($faker->address);
            $user->setProfileImage("../userImages/defaultAvatarImg.jpeg");
            //$group = $this->getReference('appGroup'.mt_rand(0, 4));
           // $user->addGroupId($group);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
