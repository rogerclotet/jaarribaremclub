<?php

namespace App\DataFixtures\ORM;

use App\Entity\Caminada;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CaminadaDataLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        foreach (range(1, 24) as $num) {
            $caminada = new Caminada();
            $caminada->setNumber($num);
            $caminada->setYear(1992 + $num);
            $caminada->setPath([
                'St. Llorenç',
                'El Monegal',
                'St. Lleí',
                'El Jou',
                'Coll Sta. Creu',
                'El Minguell',
                'La Coma',
                'La Puda',
                'St. Llorenç',
            ]);
            $caminada->setDescription('Descripció de la ' . $num . 'a Caminada');
            $caminada->setNotes('Notes de prova');

            $manager->persist($caminada);
        }

        $manager->flush();
    }
}
