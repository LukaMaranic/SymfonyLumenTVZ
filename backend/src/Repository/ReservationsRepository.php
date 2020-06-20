<?php

namespace App\Repository;

use App\Entity\Reservations;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;

/**
 * @method Reservations|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservations|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservations[]    findAll()
 * @method Reservations[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationsRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, Reservations::class);
        $this->manager = $manager;
    }

    public function saveReservation($reservationName, $dateOfReservation,$timeOfReservation,$numberOfGuests, $restaurantId, $restaurantTableId)
    {
        $reservation = new Reservations();
        $dateCreated = new DateTime();
        try {
            $date = new DateTime();
            $year = substr($dateOfReservation,0,4);
            $month = substr($dateOfReservation, 5,2);
            $day = substr($dateOfReservation,8,2);
            $date->setDate($year, $month, $day);

            $time = new DateTime();
            $hours = substr($timeOfReservation,0,2);
            $minutes = substr($timeOfReservation,3,2);
            $time->setTime($hours, $minutes);
        }catch(Exception $exceptione) {
            return false;
        }

        $reservation->setName($reservationName)
            ->setDateOfReservation($date)
            ->setTimeOfReservation($time)
            ->setNumberOfGuests($numberOfGuests)
            ->setDateCreated($dateCreated)
            ->setDateModified($dateCreated)
            ->setRestaurant($restaurantId)
            ->setRestaurantTable($restaurantTableId);
        $this->manager->persist($reservation);
        $this->manager->flush();
    }

    public function updateReservation(Reservations $reservation, $data)
    {
        try {
            $dateOfReservation = new DateTime($data['dateOfReservation']);
            $timeOfReservation = new DateTime($data['timeOfReservation']);
            empty($data['dateOfReservation']) ? true : $reservation->setDateOfReservation($dateOfReservation);
            empty($data['timeOfReservation']) ? true : $reservation->setTimeOfReservation($timeOfReservation);
        }catch (\Exception $exception){}
        finally {
            empty($data['reservationName']) ? true : $reservation->setName($data['reservationName']);
            empty($data['numberOfGuests']) ? true : $reservation->setNumberOfGuests($data['numberOfGuests']);
            $updated = new DateTime();
            $reservation->setDateModified($updated);
            $this->manager->flush();
        }
    }
    public function removeReservation(Reservations $reservation)
    {
        $this->manager->remove($reservation);
        $this->manager->flush();
    }

    /**
     * @param $date
     * @return Reservations[]
     */
    public function findByDate($date): ?array
    {
        try {
            return $this->createQueryBuilder('a')
                ->where('a.dateOfReservation = :date')
                ->setParameter(':date', $date)
                ->getQuery()
                ->getResult();
        } catch (\Exception $e) {
        }
    }

}
