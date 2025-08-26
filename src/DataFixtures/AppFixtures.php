<?php
namespace App\DataFixtures;
use App\Entity\ServiceRecord;
use App\Entity\Vehicle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
class AppFixtures extends Fixture
{
    public function load(ObjectManager $em): void
    {
        $golf = (new Vehicle())
            ->setName('Golf 7 GTD')
            ->setMake('Volkswagen')
            ->setModel('GTD')
            ->setYear(2013)
            ->setPlate('AB-123-CD')
            ->setVin('WVWZZZAUZEW035614')
            ->setOdometer(220000);
        $vidange = (new ServiceRecord())
            ->setVehicle($golf)
            ->setType('Vidange')
            ->setDate(new \DateTime('2025-06-01'))
            ->setMileage(218000)
            ->setCost(120)
            ->setNotes('Huile 5W30 + filtre')
            ->setNextDueMileage(233000)
            ->setNextDueDate((new \DateTime('2026-06-01')));
        $em->persist($golf);
        $em->persist($vidange);
        $em->flush();
    }
}
