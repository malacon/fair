<?php

namespace CB\FairBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminPanelControllerTest extends WebTestCase
{
    public function testDefault()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
    }

    public function testConfig()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/settings');
    }

}
