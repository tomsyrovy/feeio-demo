<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyGroupControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/company/id/group/create/');
    }

    public function testDetail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/company/id/group/id/detail/');
    }

}
