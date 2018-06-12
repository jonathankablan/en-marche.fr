<?php

namespace Tests\AppBundle\Controller\EnMarche;

use AppBundle\DataFixtures\ORM\LoadOrderSectionData;
use AppBundle\DataFixtures\ORM\LoadPageData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 * @group explainer
 */
class ExplainerControllerTest extends MysqlWebTestCase
{
    use ControllerTestTrait;

    /**
     * @dataProvider provideActions
     */
    public function testSuccessfulActions(string $path)
    {
        $crawler = $this->client->request(Request::METHOD_GET, $path);

        $this->assertResponseStatusCode(Response::HTTP_OK, $this->client->getResponse());
        $this->assertSame(4, $crawler->filter('.explainer__articles li')->count());
    }

    public function provideActions()
    {
        yield ['/transformer-la-france'];
    }

    protected function setUp()
    {
        parent::setUp();

        $this->init([
            LoadPageData::class,
            LoadOrderSectionData::class,
        ]);
    }

    protected function tearDown()
    {
        $this->kill();

        parent::tearDown();
    }
}
