<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FragmentControllerTest extends WebTestCase
{
    public function testNavbar()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/navbar');
    }

}
