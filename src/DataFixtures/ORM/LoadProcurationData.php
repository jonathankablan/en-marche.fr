<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\Election;
use AppBundle\Entity\ProcurationProxy;
use AppBundle\Entity\ProcurationRequest;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use libphonenumber\PhoneNumber;

class LoadProcurationData implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $electionsRepository = $manager->getRepository(Election::class);

        $presidentialElections = $electionsRepository->findOneBy(['name' => 'Élections Présidentielles 2017']);
        $legislativeElections = $electionsRepository->findOneBy(['name' => 'Élections Législatives 2017']);
        $partialLegislativeElections = $electionsRepository->findOneBy(['name' => 'Élection législative partielle pour la 1ère circonscription du Val-d\'Oise']);

        $manager->persist($this->createRequest(
            'male',
            'Timothé, Jean, Marcel',
            'Baumé',
            'timothe.baume@example.gb',
            '100 Roy Square, Main Street',
            'E14 8BY',
            null,
            'London',
            '44 9999888111',
            '1972-11-23',
            'GB',
            'E14 8BY',
            null,
            'London',
            'Lycée international Winston Churchill',
            $presidentialElections->getRounds()
        ));

        $manager->persist($this->createRequest(
            'female',
            'Carine, Margaux',
            'Édouard',
            'caroline.edouard@example.fr',
            '165 rue Marcadet',
            '75018',
            '75018-75118',
            null,
            '33 655443322',
            '1968-10-09',
            'FR',
            '75018',
            '75018-75118',
            null,
            'Damremont',
            array_merge($presidentialElections->getRounds()->toArray(), $legislativeElections->getRounds()->toArray()),
            ProcurationRequest::REASON_RESIDENCY
        ));

        $manager->persist($this->createRequest(
            'female',
            'Fleur',
            'Paré',
            'FleurPare@armyspy.com',
            '13, rue Reine Elisabeth',
            '77000',
            '77000-77288',
            null,
            '33 169641061',
            '1945-01-29',
            'FR',
            '75018',
            '75018-75118',
            null,
            'Aquarius',
            [$presidentialElections->getRounds()->last(), $legislativeElections->getRounds()->last()],
            ProcurationRequest::REASON_HEALTH
        ));

        $manager->persist($this->createRequest(
            'male',
            'Kevin',
            'Delcroix',
            'kevin.delcroix@example.fr',
            '165 rue Marcadet',
            '75018',
            '75018-75118',
            null,
            '33 988776655',
            '1991-01-18',
            'FR',
            '92110',
            '92110-92024',
            null,
            'Mairie',
            [$presidentialElections->getRounds()->last(), $legislativeElections->getRounds()->last()],
            ProcurationRequest::REASON_HELP
        ));

        $manager->persist($this->createRequest(
            'male',
            'Thomas, Jean',
            'René',
            'thomas.rene@example.gb',
            '95, Faubourg Saint Honoré',
            '75020',
            '75020-75120',
            null,
            '33 0099887766',
            '1962-10-11',
            'FR',
            '75020',
            '75020-75120',
            null,
            'Lycée Faubourg',
            $partialLegislativeElections->getRounds(),
            ProcurationRequest::REASON_HEALTH
        ));

        $manager->persist($this->createRequest(
            'female',
            'Belle, Carole',
            'D\'Aubigné',
            'belle.carole@example.fr',
            '77, Place de la Madeleine',
            '75010',
            '75010-75110',
            null,
            '33 655443322',
            '1978-03-09',
            'FR',
            '75010',
            '75010-75110',
            null,
            'Madeleine',
            [$partialLegislativeElections->getRounds()->last()],
            ProcurationRequest::REASON_HEALTH
        ));

        $manager->persist($request1 = $this->createRequest(
            'male',
            'William',
            'Brunelle',
            'WilliamBrunelle@dayrep.com',
            '59, Avenue De Marlioz',
            '44000',
            '44000-44109',
            null,
            '33 411809703',
            '1964-01-16',
            'FR',
            '44000',
            '44000-44109',
            null,
            'Saighterse',
            array_merge($presidentialElections->getRounds()->toArray(), $legislativeElections->getRounds()->toArray()),
            ProcurationRequest::REASON_HEALTH
        ));

        $manager->persist($request2 = $this->createRequest(
            'female',
            'Alice',
            'Delavega',
            'alice.delavega@exemple.org',
            '12, Avenue de la République',
            '75011',
            '75011-75111',
            null,
            '33 111809703',
            '1984-11-05',
            'FR',
            '75008',
            '75008-75108',
            null,
            'École de la république',
            $partialLegislativeElections->getRounds(),
            ProcurationRequest::REASON_TRAINING
        ));

        $referent = $manager->getRepository(Adherent::class)->findByUuid(LoadAdherentData::REFERENT_1_UUID);

        $manager->persist($this->createProxyProposal(
            $referent,
            'male',
            'Maxime',
            'Michaux',
            'maxime.michaux@example.fr',
            '14 rue Jules Ferry',
            '75018',
            '75020-75120',
            null,
            '33 988776655',
            '1989-02-17',
            'FR',
            '75018',
            '75018-75118',
            null,
            'Mairie',
            [$presidentialElections->getRounds()->last(), $legislativeElections->getRounds()->last()]
        ));

        $manager->persist($this->createProxyProposal(
            $referent,
            'male',
            'Jean-Michel',
            'Carbonneau',
            'jm.carbonneau@example.fr',
            '14 rue Jules Ferry',
            '75018',
            '75020-75120',
            null,
            '33 988776655',
            '1974-01-17',
            'FR',
            '75018',
            '75018-75118',
            null,
            'Lycée général Zola',
            array_merge($presidentialElections->getRounds()->toArray(), $legislativeElections->getRounds()->toArray())
        ));

        $manager->persist($proxy1 = $this->createProxyProposal(
            $referent,
            'male',
            'Benjamin',
            'Robitaille',
            'BenjaminRobitaille@teleworm.us',
            '47, place Stanislas',
            '44100',
            '44100-44109',
            null,
            '33 269692256',
            '1969-10-17',
            'FR',
            '44100',
            '44100-44109',
            null,
            'Bentapair',
            array_merge($presidentialElections->getRounds()->toArray(), $legislativeElections->getRounds()->toArray()),
            5,
            'Responsable procuration'
        ));

        $manager->persist($proxy2 = $this->createProxyProposal(
            $referent,
            'male',
            'Romain',
            'Gentil',
            'romain.gentil@exemple.org',
            '2, place Iéna',
            '75008',
            '75008-75108',
            null,
            '33 673849284',
            '1979-12-01',
            'FR',
            '75008',
            '75008-75108',
            null,
            'Gymnase de Iéna',
            array_merge(
                $presidentialElections->getRounds()->toArray(),
                $legislativeElections->getRounds()->toArray(),
                $partialLegislativeElections->getRounds()->toArray()
            ),
            5,
            'Responsable procuration'
        ));

        $manager->persist($this->createProxyProposal(
            $referent,
            'female',
            'Léa',
            'Bouquet',
            'lea.bouquet@exemple.org',
            '18, avenue de la République',
            '75010',
            '75010-75110',
            null,
            '33 673839259',
            '1982-02-21',
            'FR',
            '75010',
            '75010-75110',
            null,
            'École de la République',
            [$partialLegislativeElections->getRounds()->first()],
            5,
            'Responsable procuration'
        ));

        $manager->persist($this->createProxyProposal(
            $referent,
            'male',
            'Emmanuel',
            'Harquin',
            'emmanuel.harquin@exemple.org',
            '53, Quai des Belges',
            '91300',
            '91300-91377',
            null,
            '33 675645342',
            '1953-09-19',
            'FR',
            '91300',
            '91300-91377',
            null,
            'École 42',
            $partialLegislativeElections->getRounds(),
            5,
            'Responsable procuration'
        ));

        $manager->flush();

        $manager->refresh($request1);
        $manager->refresh($request2);
        $manager->refresh($proxy1);
        $manager->refresh($proxy2);
        $finder = $manager->getRepository(Adherent::class)->findByUuid(LoadAdherentData::ADHERENT_4_UUID);
        $request1->process($proxy1, $finder);
        $request2->process($proxy2, $finder);

        $reflectionClass = new \ReflectionClass(ProcurationRequest::class);
        $reflectionProperty = $reflectionClass->getProperty('processedAt');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($request1, new \DateTime('-48 hours'));
        $reflectionProperty->setValue($request2, new \DateTime('-72 hours'));

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            LoadAdherentData::class,
            LoadElectionData::class,
        ];
    }

    private function createRequest(
        string $gender,
        string $firstNames,
        string $lastName,
        string $email,
        string $address,
        ?string $postalCode,
        ?string $city,
        ?string $cityName,
        ?string $phone,
        string $birthDate,
        string $voteCountry,
        ?string $votePostalCode,
        ?string $voteCity,
        ?string $voteCityName,
        string $voteOffice,
        iterable $electionRounds,
        string $reason = ProcurationRequest::REASON_HOLIDAYS
    ): ProcurationRequest {
        if ($phone) {
            $phone = $this->createPhone($phone);
        }

        $request = new ProcurationRequest();
        $request->setGender($gender);
        $request->setFirstNames($firstNames);
        $request->setLastName($lastName);
        $request->setEmailAddress($email);
        $request->setAddress($address);
        $request->setPostalCode($postalCode);
        $request->setCity($city);
        $request->setCityName($cityName);
        $request->setPhone($phone);
        $request->setBirthdate(new \DateTime($birthDate));
        $request->setVoteCountry($voteCountry);
        $request->setVotePostalCode($votePostalCode);
        $request->setVoteCity($voteCity);
        $request->setVoteCityName($voteCityName);
        $request->setVoteOffice($voteOffice);
        $request->setElectionRounds($electionRounds);
        $request->setReason($reason);

        return $request;
    }

    public function createProxyProposal(
        Adherent $referent,
        string $gender,
        string $firstNames,
        string $lastName,
        string $email,
        string $address,
        ?string $postalCode,
        ?string $city,
        ?string $cityName,
        ?string $phone,
        string $birthDate,
        string $voteCountry,
        ?string $votePostalCode,
        ?string $voteCity,
        ?string $voteCityName,
        string $voteOffice,
        iterable $electionRounds,
        int $reliability = 0,
        string $reliabilityDescription = ''
    ): ProcurationProxy {
        if ($phone) {
            $phone = $this->createPhone($phone);
        }

        $proxy = new ProcurationProxy($referent);
        $proxy->setGender($gender);
        $proxy->setFirstNames($firstNames);
        $proxy->setLastName($lastName);
        $proxy->setEmailAddress($email);
        $proxy->setAddress($address);
        $proxy->setPostalCode($postalCode);
        $proxy->setCity($city);
        $proxy->setCityName($cityName);
        $proxy->setPhone($phone);
        $proxy->setBirthdate(new \DateTime($birthDate));
        $proxy->setVoteCountry($voteCountry);
        $proxy->setVotePostalCode($votePostalCode);
        $proxy->setVoteCity($voteCity);
        $proxy->setVoteCityName($voteCityName);
        $proxy->setVoteOffice($voteOffice);
        $proxy->setElectionRounds($electionRounds);
        $proxy->setReliability($reliability);
        $proxy->setReliabilityDescription($reliabilityDescription);

        return $proxy;
    }

    /**
     * Returns a PhoneNumber object.
     *
     * The format must be something like "33 0102030405"
     *
     * @param string $phoneNumber
     *
     * @return PhoneNumber
     */
    private function createPhone($phoneNumber): PhoneNumber
    {
        list($country, $number) = explode(' ', $phoneNumber);

        $phone = new PhoneNumber();
        $phone->setCountryCode($country);
        $phone->setNationalNumber($number);

        return $phone;
    }
}
