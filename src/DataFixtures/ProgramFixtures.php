<?php


namespace App\DataFixtures;


use App\Entity\Program;

use Doctrine\Bundle\FixturesBundle\Fixture;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Doctrine\Persistence\ObjectManager;


class ProgramFixtures extends Fixture implements DependentFixtureInterface

{
    public const PROGRAMS = [
        ['title' => 'Fear the walking dead', 'summary' => 'un résumé de la série', 'poster' => 'Une image ici', 'year' => '2018', 'country' => 'USA'],
        ['title' => 'Urgences', 'summary' => 'un résumé de la série', 'poster' => 'Une image ici', 'year' => '2018', 'country' => 'USA'],
        ['title' => 'Chernobyl', 'summary' => 'un résumé de la série', 'poster' => 'Une image ici', 'year' => '2018', 'country' => 'USA'],
        ['title' => 'Rick et Morty', 'summary' => 'un résumé de la série', 'poster' => 'Une image ici', 'year' => '2018', 'country' => 'USA'],
        ['title' => 'Game of Thrones', 'summary' => 'un résumé de la série', 'poster' => 'Une image ici', 'year' => '2018', 'country' => 'USA'],
    ];


    public function load(ObjectManager $manager)

    {
        foreach (self::PROGRAMS as $row => $value) {

            $program = new Program();

            $program->setTitle($value['title']);

            $program->setSummary($value['summary']);

            $program->setPoster($value['poster']);

            $program->setYear($value['year']);

            $program->setCountry($value['country']);

            $program->setCategory($this->getReference('category_0'));

            $this->addReference('program_' . $row, $program);


            //ici les acteurs sont insérés via une boucle pour être DRY mais ce n'est pas obligatoire

            for ($i = 0; $i < count(ActorFixtures::DATA); $i++) {

                $program->addActor($this->getReference('actor_' . $i));
            }

            $manager->persist($program);
        }

        $manager->flush();
    }


    public function getDependencies()

    {

        return [

            ActorFixtures::class,

            CategoryFixtures::class,

        ];
    }
}
