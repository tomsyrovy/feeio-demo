<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InvitationControllerTest extends WebTestCase
{
    public function testCreate()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'company/id/invitation/');
    }

    public function testSubmitted()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', 'company/id/invitation/submitted/');
    }

}
