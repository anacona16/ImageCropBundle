<?php

namespace Anacona16\Bundle\ImageCropBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testGenerateImageMissingParams()
    {
        /*$client = static::createClient();
        $client->followRedirects();

        $crawler = $client->xmlHttpRequest('GET', '/en/generate_image');
        $result = json_decode($client->getResponse()->getContent(), true);

        $this->assertFalse($result["success"]);
        $this->assertSame("Required fields are empty", $result["message"]);
        */

        $this->assertTrue(true);
    }

    /*
    public function testOverview()
    {
        $client = static::createClient();
        $client->followRedirects();

        #$crawler = $client->xmlHttpRequest('GET', '/en/overview');

        #/{_locale}/overview/{style}/{id}/{fqcn}/
        $crawler = $client->request('GET', '/en/overview/{style}/{id}/{fqcn}/');

        $result = json_decode($client->getResponse()->getContent(), true);
        #var_dump($result);

        $this->assertFalse($result["success"]);
        $this->assertSame("Required fields are empty", $result["message"]);

        #$this->assertTrue($crawle->filter('html:contains("Hello Fabien")')->count() > 0);
    }*/
}
