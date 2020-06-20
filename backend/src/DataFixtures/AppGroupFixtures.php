<?php

namespace App\DataFixtures;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Repository\AppGroupRepository;
use App\Entity\AppGroup;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppGroupFixtures extends Fixture
{
    private $appGroupRepository;

    public function __construct(AppGroupRepository $appGroupRepository){

        $this->appGroupRepository = $appGroupRepository;
    }

    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        for($i=0; $i<5; $i++){
            $group = new AppGroup();
            $group->setGroupName('Group '.($i+1));
            $group->setDateCreated($date);
            $group->setDateModified($date);
            $manager->persist($group);
            $this->addReference('appGroup'.$i, $group);
        }
        $manager->flush();
    }
}
