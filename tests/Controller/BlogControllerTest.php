<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BlogControllerTest extends WebTestCase
{
    public function testLoadBlogSection()
    {
        $this->markTestSkipped('Need to have an InMemory test environment');

        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            $client->getResponse()->getContent()
        );
        $this->assertContains('Notícies', $crawler->filter('.main-container h1')->text());
    }

    public function testCreatePost()
    {
        $this->markTestSkipped('Need to have an InMemory test environment');

        $client = static::createClient();

        $session = $client->getContainer()->get('session');

        $firewall = 'main';

        $token = new UsernamePasswordToken('admin', null, $firewall, ['ROLE_ADMIN']);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        $text = 'Test text';

        $client->request('POST', '/', [], [], [], $text);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertContains('<a href="/">', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals($text, $crawler->filter('article')->text());
    }
}
