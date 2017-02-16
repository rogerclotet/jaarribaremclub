<?php

namespace App\DataFixtures\ORM;

use App\Entity\Post;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PostDataLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $post1 = new Post();
        $post1->setTitle('First post');
        $post1->setText("Text without anything special.\nMaybe some line break.");

        $post2 = new Post();
        $post2->setTitle('Second post');
        $post2->setText('Let\'s see if this thing escapes <i>HTML</i>');

        $manager->persist($post1);
        $manager->persist($post2);

        foreach (range(1, 20) as $i) {
            $post = new Post();
            $post->setTitle('Post ' . $i);
            $post->setText('blergh');

            $manager->persist($post);
        }

        $manager->flush();
    }
}
