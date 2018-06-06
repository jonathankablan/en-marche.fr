<?php

namespace AppBundle\Deputy;

use AppBundle\Entity\District;
use AppBundle\Geo\GeometryFactory;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\DecoderInterface;

class DistrictCsvLoader
{
    private $decoder;
    private $doctrine;
    private $geometryFactory;
    private $logger;

    public function __construct(DecoderInterface $decoder, Registry $doctrine, GeometryFactory $geometryFactory, LoggerInterface $logger)
    {
        $this->decoder = $decoder;
        $this->geometryFactory = $geometryFactory;
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    public function load(string $file): void
    {
        $this->batchInsertFromGeoflaCsvFormat(
            $this->decoder->decode(file_get_contents($file), 'csv', [CsvEncoder::DELIMITER_KEY => ';'])
        );
    }

    public function batchInsertFromGeoflaCsvFormat(array $districts): void
    {
        $this->logger->notice(sprintf('%s districts are about to be loaded', count($districts)));

        $em = $this->doctrine->getManager();
        $duplicatedDistrictCount = 0;
        $i = 0;

        foreach ($districts as $district) {
            try {
                $em->persist($this->createDistrict($district));
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                // Unique constraint exception must be ignored as GeoFla data are like that
                ++$duplicatedDistrictCount;
                $em->clear();
                $em = $this->doctrine->resetManager();
            }

            if (0 === ++$i % 1000 || $i === count($districts)) {
                $em->clear();
                $this->logger->notice("$i districts processed");
            }
        }

        if ($duplicatedDistrictCount) {
            $this->logger->warning("$duplicatedDistrictCount districts are duplicated");
        }
    }

    private function createDistrict(array $district): District
    {
        return new District(
            $this->geometryFactory->createPointFromGeoPoint($district['Geo Point']),
            $this->geometryFactory->createGeometryFromGeojson($district['Geo Shape']),
            $district['CODE'],
            $district['NAME'],
            $district['DEPARTMENT_CODE']
        );
    }
}
