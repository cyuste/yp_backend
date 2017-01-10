<?php
namespace Tests\Yust\PlayItBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    public function testGetUsers()
    {
        $client =  static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin'
        ));

        $crawler = $client->request('GET', '/admin/users');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Usuarios")')->count()
        );
    }

    public function testNewDevice()
    {
        $client =  static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin'
        ));

        $crawler = $client->request('GET', '/admin/newDevice');

        $this->assertGreaterThan(
            0,
            $crawler->filter('html:contains("Alias")')->count()
        );
    } 
}
