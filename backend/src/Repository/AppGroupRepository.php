<?php

namespace App\Repository;

use App\Entity\AppGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
/**
 * @method AppGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method AppGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method AppGroup[]    findAll()
 * @method AppGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AppGroupRepository extends ServiceEntityRepository
{
    //private $appGroupRepository;
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, AppGroup::class);
        $this->manager = $manager;
    }


    public function saveGroup($groupName, $groupImage)
    {
        $date = date_create();
        $group = new AppGroup();
        $group->setGroupName($groupName)->setGroupImage($groupImage);
        $group->setDateCreated($date);
        $group->setDateModified($date);
        $this->manager->persist($group);
        $this->manager->flush();
    }

        public function updateGroup(AppGroup $group, $data)
    {
        empty($data['groupName']) ? true : $group->setGroupName($data['groupName']);

        $this->manager->flush();
    }

        public function deleteGroup(AppGroup $appGroup)
    {
        $this->manager->remove($appGroup);
        $this->manager->flush();
    }
}
