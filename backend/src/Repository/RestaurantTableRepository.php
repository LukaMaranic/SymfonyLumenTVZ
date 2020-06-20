<?php

namespace App\Repository;

use App\Entity\Restaurant;
use App\Entity\RestaurantTable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method RestaurantTable|null find($id, $lockMode = null, $lockVersion = null)
 * @method RestaurantTable|null findOneBy(array $criteria, array $orderBy = null)
 * @method RestaurantTable[]    findAll()
 * @method RestaurantTable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantTableRepository extends ServiceEntityRepository
{
    private $manager;
    public function __construct(ManagerRegistry $registry,EntityManagerInterface $manager)
    {
        parent::__construct($registry, RestaurantTable::class);
        $this->manager = $manager;
    }

    public function saveTable($tableName, $numberOfSeats, $tableType, Restaurant $restaurantId)
    {
        $date = date_create();
        $table = new RestaurantTable();
        $table->setTableName($tableName);
        $table->setNumberOfSeats($numberOfSeats);
        $table->setTableType($tableType);
        $table->setDateCreated($date);
        $table->setDateModified($date);
        $table->setRestaurant( $restaurantId);
        $this->manager->persist($table);
        $this->manager->flush();
    }
    public function updateTable(RestaurantTable $table, $data)
    {
        $isUpdated = false;
        if (array_key_exists("tableName", $data)){
            $table->setTableName($data['tableName']);
            $date = date_create();
            $table->setDateModified($date);
            $this->manager->flush();
            $isUpdated = true;
            return $isUpdated;
        }
        return $isUpdated;

    }
    public function deleteTable(RestaurantTable $table)
    {
        $this->manager->remove($table);
        $this->manager->flush();
    }
}
