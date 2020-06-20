<?php

namespace App\Repository;

use App\Entity\WaitingList;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;


/**
 * @method WaitingList|null find($id, $lockMode = null, $lockVersion = null)
 * @method WaitingList|null findOneBy(array $criteria, array $orderBy = null) $offset = null)
 */
class WaitingListRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, WaitingList::class);
        $this->manager = $manager;
    }

 function saveWaiting($waitingName, $dateOfWaiting,$timeOfWaiting,$numberOfGuests, $latitude, $longitude)
    {
        $waitingList = new WaitingList();
        $dateCreated = new DateTime();
        try {
            $date = new DateTime();
            $year = substr($dateOfWaiting,0,4);
            $month = substr($dateOfWaiting, 5,2);
            $day = substr($dateOfWaiting,8,2);
            $date->setDate($year, $month, $day);

            $time = new DateTime();
            $hours = substr($timeOfWaiting,0,2);
            $minutes = substr($timeOfWaiting,3,2);
            $time->setTime($hours, $minutes);

            $waitingList->setName($waitingName)
                ->setDate($date)
                ->setTime($time)
                ->setNumberOfGuests($numberOfGuests)
                ->setDateCreated($dateCreated)
                ->setDateModified($dateCreated)
                ->setLatitude($latitude)
                ->setLongitude($longitude);
            $this->manager->persist($waitingList);
            $this->manager->flush();
        }catch(Exception $exceptione) {
            return false;
        }
        return true;
    }
    public function updateWaiting(WaitingList $waitingList, $data)
    {
        $isUpdated = false;
        if (array_key_exists("nameOfWaiting", $data)){
            $waitingList->setName($data['nameOfWaiting']);
            $isUpdated = true;
        }
        if (array_key_exists("dateOfWaiting", $data)){
            $waitingList->setDate($data['dateOfWaiting']);
            $isUpdated = true;
        }
        if (array_key_exists("timeOfWaiting", $data)){
            $waitingList->setTime($data['timeOfWaiting']);
            $isUpdated = true;
        }
        if (array_key_exists("numberOfGuests", $data)){
            $waitingList->setTime($data['numberOfGuests']);
            $isUpdated = true;
        }
        if (array_key_exists("latitude", $data)){
            $waitingList->setLatitude($data['latitude']);
            $isUpdated = true;
        }
        if (array_key_exists("longitude", $data)){
            $waitingList->setLongitude($data['longitude']);
            $isUpdated = true;
        }
        if ($isUpdated){
            $date = date_create();
            $waitingList->setDateModified($date);
            $this->manager->flush();
        }
        return $isUpdated;
    }

    public function deleteWaiting(WaitingList $waitingList)
    {
        $this->manager->remove($waitingList);
        $this->manager->flush();
    }
}
