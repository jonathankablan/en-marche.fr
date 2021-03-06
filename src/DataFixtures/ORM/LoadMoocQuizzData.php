<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Mooc\Quizz;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMoocQuizzData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $quizz = new Quizz(
            'Le test de votre vie',
            '<p>une description</p>',
            '<p>le quizz</p>'
        );

        $manager->persist($quizz);
        $manager->flush();

        $this->setReference('mooc-quizz-1', $quizz);
    }
}
