<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;

class ActorFixtures extends Fixture
{
    public const DATA = [['firstname'=> 'Norman','lastname'=>'Reedus','birth_date'=>'1969-01-01'],
    ['firstname'=> 'Andrew','lastname'=>'Lincoln','birth_date'=>'1973-01-01'],
    ['firstname'=> 'Lauren','lastname'=>'Cohan','birth_date'=>'1983-01-01'],
    ['firstname'=> 'Jeffrey Dean','lastname'=>'Morgan','birth_date'=>'1966-01-01'],
    ['firstname'=> 'Chandler','lastname'=>'Riggs','birth_date'=>'1999-01-01']];

      
    public function load(ObjectManager $manager)

    {

        foreach (self::DATA as $row=> $value) {

            $actor = new Actor();

            $actor->setFirstName($value['firstname']);
            $actor->setLastName($value['lastname']);
            $actor->setBirthDate(new \DateTime($value['birth_date']));

            $manager->persist($actor);

            $this->addReference('actor_' . $row, $actor);

        }

        
        $manager->flush();

    }
}
