<?php

namespace AjaxBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VatControllerTest extends WebTestCase
{
    public function testLoad()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/vat/ico/load/');
    }

}
