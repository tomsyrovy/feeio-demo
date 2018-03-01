<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SubcommissionControllerTest extends WebTestCase
{
    public function testList()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/commission/commission_id/subcommissions/');
    }

    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/commission/commission_id/subcommission/create/');
    }

}
