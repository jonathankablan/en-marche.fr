<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 */
class IntlControllerTest extends MysqlWebTestCase
{
    use ControllerTestTrait;

    public function testGetPostalCode()
    {
        $this->client->request(Request::METHOD_GET, '/api/postal-code/35420');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals([
            35018 => 'La Bazouge-du-Désert',
            35111 => 'Le Ferré',
            35162 => 'Louvigné-du-Désert',
            35174 => 'Mellé',
            35190 => 'Monthault',
            35230 => 'Poilley',
            35271 => 'Saint-Georges-de-Reintembault',
            35357 => 'Villamée',
        ], \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true));
    }

    public function testGetVoteOffices()
    {
        $this->client->request(Request::METHOD_GET, '/api/vote-offices/DE');

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertEquals([
            'Berlin',
            'Dusseldorf',
            'Hambourg',
            'Francfort',
            'Munich',
            'Nuremberg',
            'Sarrebruck',
            'Stuttgart',
        ], \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true));
    }

    protected function setUp()
    {
        parent::setUp();

        $this->init();
    }

    protected function tearDown()
    {
        $this->kill();

        parent::tearDown();
    }
}
