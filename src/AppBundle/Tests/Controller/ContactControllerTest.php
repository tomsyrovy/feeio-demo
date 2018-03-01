<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact/create/');
    }

    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contacts/');
    }

    public function testUpdate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact/update/');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact/delete/');
    }

}
