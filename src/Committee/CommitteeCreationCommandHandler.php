<?php

namespace AppBundle\Committee;

use AppBundle\Events;
use AppBundle\Mailer\MailerService;
use AppBundle\Mailer\Message\CommitteeCreationConfirmationMessage;
use AppBundle\Referent\ReferentTagManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CommitteeCreationCommandHandler
{
    private $dispatcher;
    private $factory;
    private $manager;
    private $mailer;
    private $photoManager;
    private $referentTagManager;

    public function __construct(
        EventDispatcherInterface $dispatcher,
        CommitteeFactory $factory,
        CommitteeManager $manager,
        MailerService $mailer,
        PhotoManager $photoManager,
        ReferentTagManager $referentTagManager
    ) {
        $this->dispatcher = $dispatcher;
        $this->factory = $factory;
        $this->manager = $manager;
        $this->mailer = $mailer;
        $this->photoManager = $photoManager;
        $this->referentTagManager = $referentTagManager;
    }

    public function handle(CommitteeCreationCommand $command): void
    {
        $adherent = $command->getAdherent();
        $committee = $this->factory->createFromCommitteeCreationCommand($command);
        // Uploads an ID photo
        $this->photoManager->addPhotoFromCommand($command, $committee);

        $this->referentTagManager->assignReferentLocalTags($committee);

        $command->setCommittee($committee);

        $this->manager->followCommittee($adherent, $committee);

        $this->dispatcher->dispatch(Events::COMMITTEE_CREATED, new CommitteeEvent($committee));

        $message = CommitteeCreationConfirmationMessage::create($adherent, $committee);
        $this->mailer->sendMessage($message);
    }
}
