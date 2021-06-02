<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Service\Slugify;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    private $input;

    public function __construct(Slugify $input)
    {
        $this->input = $input;
    }
    public const EPISODES = [
        ['title' => 'Episode 1', 'number' => 1, 'synopsis' => 'Une synopsis ici'],
        ['title' => 'Episode 2', 'number' => 2, 'synopsis' => 'Une synopsis ici'],
        ['title' => 'Episode 3', 'number' => 3, 'synopsis' => 'Une synopsis ici'],
        ['title' => 'Episode 4', 'number' => 4, 'synopsis' => 'Une synopsis ici'],
        ['title' => 'Episode 5', 'number' => 5, 'synopsis' => 'Une synopsis ici']
    ];

    public function load(ObjectManager $manager)

    {
        foreach (self::EPISODES as $row => $value) {

            $episode = new Episode();

            $episode->setTitle($value['title']);

            $episode->setSlug($this->input->generate($value['title']));

            $episode->setNumber($value['number']);

            $episode->setSynopsis($value['synopsis']);

            $episode->setSeason($this->getReference('season_0'));

            $manager->persist($episode);
        }

        $manager->flush();
    }

    public function getDependencies()

    {

        return [

            SeasonFixtures::class,

        ];
    }
}
