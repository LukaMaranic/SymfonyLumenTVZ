<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Restaurant::class);
        $this->manager = $manager;
    }

    public function saveRestaurant($restaurantName, $restaurantImage)
    {
        $date = date_create();
        $restaurant = new Restaurant();
        $restaurant->setRestaurantName($restaurantName);
        $restaurant->setRestaurantImage($restaurantImage);
        $restaurant->setDateCreated($date);
        $restaurant->setDateModified($date);
        $this->manager->persist($restaurant);
        $this->manager->flush();
    }
    public function updateRestaurant(Restaurant $restaurant, $data)
    {
        empty($data['restaurantName']) ? true : $restaurant->setRestaurantName($data['restaurantName']);
        //TODO update slike
        $date = date_create();
        $restaurant->setDateModified($date);
        $this->manager->flush();
    }

    public function deleteRestaurant(Restaurant $restaurant)
    {
        $this->manager->remove($restaurant);
        $this->manager->flush();
    }
}
