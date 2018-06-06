<?php

namespace Tests\AppBundle\Controller\EnMarche;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 */
class ObsoleteControllerTest extends MysqlWebTestCase
{
    use ControllerTestTrait;

    /**
     * @dataProvider provideActions
     */
    public function testActions(string $path, bool $permanent = false)
    {
        $this->client->request(Request::METHOD_GET, $path);

        $this->assertStatusCode($permanent ? Response::HTTP_GONE : Response::HTTP_NOT_FOUND, $this->client);
    }

    public function provideActions()
    {
        yield ['/emmanuel-macron/desintox'];
        yield ['/emmanuel-macron/desintox/heritier-hollande-traite-quiquennat'];
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
