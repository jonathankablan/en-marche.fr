<?php

namespace Tests\AppBundle\Repository;

use AppBundle\DataFixtures\ORM\LoadAdherentData;
use AppBundle\Repository\CommitteeRepository;
use Tests\AppBundle\Controller\ControllerTestTrait;
use Tests\AppBundle\MysqlWebTestCase;

/**
 * @group functional
 */
class CommitteeRepositoryTest extends MysqlWebTestCase
{
    /**
     * @var CommitteeRepository
     */
    private $repository;

    use ControllerTestTrait;

    public function testCountApprovedCommittees()
    {
        $this->assertSame(9, $this->repository->countApprovedCommittees());
    }

    public function testFindApprovedCommittees()
    {
        $this->assertCount(9, $this->repository->findApprovedCommittees());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->loadFixtures([
            LoadAdherentData::class,
        ]);

        $this->container = $this->getContainer();
        $this->repository = $this->getCommitteeRepository();
    }

    protected function tearDown()
    {
        $this->loadFixtures([]);

        $this->repository = null;
        $this->container = null;

        parent::tearDown();
    }
}
