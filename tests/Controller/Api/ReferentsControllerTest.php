<?php

namespace Tests\AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ApiControllerTestTrait;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 */
class ReferentsControllerTest extends MysqlWebTestCase
{
    use ControllerTestTrait;
    use ApiControllerTestTrait;

    public function testApiApprovedCommittees()
    {
        $this->client->request(Request::METHOD_GET, '/api/referents');

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());

        $content = $this->client->getResponse()->getContent();
        $this->assertJson($content);

        // Check the payload
        $this->assertNotEmpty(\GuzzleHttp\json_decode($content, true));
        $this->assertEachJsonItemContainsKey('postalCode', $content);
        $this->assertEachJsonItemContainsKey('name', $content);
        $this->assertEachJsonItemContainsKey('coordinates', $content);
    }

    public function setUp()
    {
        parent::setUp();

        $this->init([]);
    }

    public function tearDown()
    {
        $this->kill();

        parent::tearDown();
    }
}
