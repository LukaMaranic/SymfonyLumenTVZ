<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\Reservations;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ReservationFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $date = new \DateTime();
        $faker = Faker\Factory::create('hr_HR');
        for ($j=0; $j<100; $j++) {
            $reservation = new Reservations();
            $reservation->setName("Rezervacija ".$j);
            $reservation->setDateOfReservation($faker->dateTimeBetween('now', '+1 years'));
            $reservation->setTimeOfReservation($faker->dateTimeBetween('now', '+1 years'));
            $reservation->setNumberOfGuests(mt_rand(1,12));
            $reservation->setDateCreated($date);
            $reservation->setDateModified($date);
            $manager->persist($reservation);
        }

        $manager->flush();
    }
}
