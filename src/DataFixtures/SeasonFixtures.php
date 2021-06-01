<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public const SEASONS = [
        ['number' => 1, 'year' => 2014, 'description' => 'Une description ici'],
        ['number' => 2, 'year' => 2015, 'description' => 'Une description ici'],
        ['number' => 3, 'year' => 2016, 'description' => 'Une description ici'],
        ['number' => 4, 'year' => 2017, 'description' => 'Une description ici'],
        ['number' => 5, 'year' => 2018, 'description' => 'Une description ici']
    ];


    public function load(ObjectManager $manager)

    {
        foreach (self::SEASONS as $row => $value) {

            $season = new Season();

            $season->setNumber($value['number']);

            $season->setYear($value['year']);

            $season->setdescription($value['description']);

            $season->setProgram($this->getReference('program_0'));

            $this->addReference('season_' . $row, $season);

            $manager->persist($season);
        }

        $manager->flush();
    }

    public function getDependencies()

    {

        return [

            ProgramFixtures::class,

        ];
    }
}
