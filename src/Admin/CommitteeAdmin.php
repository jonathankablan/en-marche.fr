<?php

namespace AppBundle\Admin;

use AppBundle\Committee\CommitteeManager;
use AppBundle\Entity\Adherent;
use AppBundle\Entity\Committee;
use AppBundle\Entity\CommitteeMembership;
use AppBundle\Form\UnitedNationsCountryType;
use AppBundle\Intl\UnitedNationsBundle;
use Doctrine\Common\Persistence\ObjectManager;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class CommitteeAdmin extends AbstractAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_per_page' => 32,
        '_sort_order' => 'DESC',
        '_sort_by' => 'createdAt',
    ];

    private $manager;
    private $committeeMembershipRepository;
    private $cachedDatagrid;
    private $committeeRepository;
    private $adherentRepository;

    public function __construct($code, $class, $baseControllerName, CommitteeManager $manager, ObjectManager $om)
    {
        parent::__construct($code, $class, $baseControllerName);

        $this->manager = $manager;
        $this->committeeMembershipRepository = $om->getRepository(CommitteeMembership::class);
        $this->committeeRepository = $om->getRepository(Committee::class);
        $this->adherentRepository = $om->getRepository(Adherent::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid()
    {
        if (!$this->cachedDatagrid) {
            $this->cachedDatagrid = new CommitteeDatagrid(parent::getDatagrid(), $this->manager);
        }

        return $this->cachedDatagrid;
    }

    public function getTemplate($name)
    {
        if ('show' === $name) {
            return 'admin/committee/show.html.twig';
        }

        if ('edit' === $name) {
            return 'admin/committee/edit.html.twig';
        }

        return parent::getTemplate($name);
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->with('Comité', array('class' => 'col-md-7'))
                ->add('name', null, [
                    'label' => 'Nom',
                    'format_title_case' => true,
                ])
                ->add('description', TextareaType::class, [
                    'filter_emojis' => true,
                    'label' => 'Description',
                    'attr' => [
                        'rows' => '3',
                    ],
                ])
                ->add('slug', null, [
                    'label' => 'Slug',
                ])
                ->add('phone', null, [
                    'label' => 'Téléphone',
                    'template' => 'admin/adherent/show_phone.html.twig',
                ])
                ->add('facebookPageUrl', UrlType::class, [
                    'label' => 'Facebook',
                ])
                ->add('twitterNickname', null, [
                    'label' => 'Twitter',
                ])
                ->add('googlePlusPageUrl', UrlType::class, [
                    'label' => 'Google+',
                ])
                ->add('status', null, [
                    'label' => 'Statut',
                ])
                ->add('createdAt', null, [
                    'label' => 'Date de création',
                ])
                ->add('approvedAt', null, [
                    'label' => 'Date d\'approbation',
                ])
                ->add('refusedAt', null, [
                    'label' => 'Date de refus',
                ])
            ->end()
            ->with('Adresse', array('class' => 'col-md-5'))
                ->add('postAddress.address', TextType::class, [
                    'label' => 'Rue',
                ])
                ->add('postAddress.postalCode', TextType::class, [
                    'label' => 'Code postal',
                ])
                ->add('postAddress.cityName', TextType::class, [
                    'label' => 'Ville',
                ])
                ->add('postAddress.country', UnitedNationsCountryType::class, [
                    'label' => 'Pays',
                ])
                ->add('postAddress.latitude', TextType::class, [
                    'label' => 'Latitude',
                ])
                ->add('postAddress.longitude', TextType::class, [
                    'label' => 'Longitude',
                ])
            ->end()
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Comité', array('class' => 'col-md-7'))
                ->add('name', null, [
                    'label' => 'Nom',
                ])
                ->add('nameLocked', null, [
                    'label' => 'Bloquer la modification du nom de comité',
                ])
                ->add('description', null, [
                    'label' => 'Description',
                    'attr' => [
                        'rows' => '3',
                    ],
                ])
                ->add('slug', null, [
                    'label' => 'Slug',
                ])
                ->add('facebookPageUrl', UrlType::class, [
                    'label' => 'Facebook',
                    'required' => false,
                ])
                ->add('twitterNickname', null, [
                    'label' => 'Twitter',
                    'required' => false,
                ])
                ->add('googlePlusPageUrl', UrlType::class, [
                    'label' => 'Google+',
                    'required' => false,
                ])
            ->end()
            ->with('Localisation', array('class' => 'col-md-5'))
                ->add('postAddress.latitude', TextType::class, [
                    'label' => 'Latitude',
                ])
                ->add('postAddress.longitude', TextType::class, [
                    'label' => 'Longitude',
                    'help' => 'Pour modifier l\'adresse, impersonnifiez un animateur de ce comité.',
                ])
            ->end()
            ->with('Commentaire', array('class' => 'col-md-5'))
                ->add('adminComment', TextareaType::class, [
                    'label' => 'Commentaire sur l\'AL',
                    'attr' => ['rows' => 5],
                    'required' => false,
                ])
                ->add('coordinatorComment', TextareaType::class, [
                    'label' => 'Commentaire sur l\'AL du coordinateur',
                    'attr' => ['rows' => 5],
                    'disabled' => true,
                    'required' => false,
                ])
            ->end()
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $committeeMembershipRepository = $this->committeeMembershipRepository;
        $committeeRepository = $this->committeeRepository;
        $adherentRepository = $this->adherentRepository;

        $datagridMapper
            ->add('id', null, [
                'label' => 'ID',
                'show_filter' => true,
            ])
            ->add('name', null, [
                'label' => 'Nom',
                'show_filter' => true,
            ])
            ->add('createdAt', DateRangeFilter::class, [
                'label' => 'Date de création',
                'field_type' => 'sonata_type_date_range_picker',
            ])
            ->add('hostOrCreatorFirstName', CallbackFilter::class, [
                'label' => 'Prénom de l\'animateur/créateur',
                'show_filter' => true,
                'field_type' => TextType::class,
                'callback' => function (ProxyQuery $qb, string $alias, string $field, array $value) use ($committeeMembershipRepository, $adherentRepository, $committeeRepository) {
                    if (!$value['value']) {
                        return;
                    }

                    $creatorCommitteeIds = $committeeRepository->findCommitteesUuidByCreatorUuids($adherentRepository->findAdherentsUuidByFirstName($value['value']));
                    $hostCommitteeIds = $committeeMembershipRepository->findCommitteesUuidByHostFirstName($value['value']);
                    if (!$creatorCommitteeIds && !$hostCommitteeIds) {
                        // Force no results when no user is found
                        $qb->andWhere($qb->expr()->in(sprintf('%s.id', $alias), [0]));

                        return true;
                    }

                    $committeeIds = array_unique(array_merge($hostCommitteeIds, $creatorCommitteeIds));
                    $qb->andWhere($qb->expr()->in(sprintf('%s.uuid', $alias), $committeeIds));

                    return true;
                },
            ])
            ->add('hostOrCreatorLastName', CallbackFilter::class, [
                'label' => 'Nom de l\'animateur/créateur',
                'show_filter' => true,
                'field_type' => TextType::class,
                'callback' => function (ProxyQuery $qb, string $alias, string $field, array $value) use ($committeeMembershipRepository, $committeeRepository, $adherentRepository) {
                    if (!$value['value']) {
                        return;
                    }

                    $creatorCommitteeIds = $committeeRepository->findCommitteesUuidByCreatorUuids($adherentRepository->findAdherentsUuidByLastName($value['value']));
                    $hostCommitteeIds = $committeeMembershipRepository->findCommitteesUuidByHostLastName($value['value']);
                    if (!$creatorCommitteeIds && !$hostCommitteeIds) {
                        // Force no results when no user is found
                        $qb->andWhere($qb->expr()->in(sprintf('%s.id', $alias), [0]));

                        return true;
                    }

                    $committeeIds = array_unique(array_merge($hostCommitteeIds, $creatorCommitteeIds));
                    $qb->andWhere($qb->expr()->in(sprintf('%s.uuid', $alias), $committeeIds));

                    return true;
                },
            ])
            ->add('hostOrCreatorEmailAddress', CallbackFilter::class, [
                'label' => 'Email de l\'animateur/créateur',
                'show_filter' => true,
                'field_type' => EmailType::class,
                'callback' => function (ProxyQuery $qb, string $alias, string $field, array $value) use ($committeeMembershipRepository, $adherentRepository, $committeeRepository) {
                    if (!$value['value']) {
                        return;
                    }

                    $creatorCommitteeIds = $committeeRepository->findCommitteesUuidByCreatorUuids($adherentRepository->findAdherentsUuidByEmailAddress($value['value']));
                    $hostCommitteeIds = $committeeMembershipRepository->findCommitteesUuidByHostEmailAddress($value['value']);
                    if (!$creatorCommitteeIds && !$hostCommitteeIds) {
                        // Force no results when no user is found
                        $qb->andWhere($qb->expr()->in(sprintf('%s.id', $alias), [0]));

                        return true;
                    }

                    $committeeIds = array_unique(array_merge($hostCommitteeIds, $creatorCommitteeIds));
                    $qb->andWhere($qb->expr()->in(sprintf('%s.uuid', $alias), $committeeIds));

                    return true;
                },
            ])
            ->add('city', CallbackFilter::class, [
                'label' => 'Ville',
                'field_type' => TextType::class,
                'callback' => function (ProxyQuery $qb, string $alias, string $field, array $value) {
                    if (!$value['value']) {
                        return;
                    }

                    $qb->andWhere(sprintf('LOWER(%s.postAddress.cityName)', $alias).' LIKE :cityName');
                    $qb->setParameter('cityName', '%'.strtolower($value['value']).'%');

                    return true;
                },
            ])
            ->add('country', CallbackFilter::class, [
                'label' => 'Pays',
                'show_filter' => true,
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => array_flip(UnitedNationsBundle::getCountries()),
                ],
                'callback' => function (ProxyQuery $qb, string $alias, string $field, array $value) {
                    if (!$value['value']) {
                        return;
                    }

                    $qb->andWhere(sprintf('LOWER(%s.postAddress.country)', $alias).' = :country');
                    $qb->setParameter('country', strtolower($value['value']));

                    return true;
                },
            ])
            ->add('status', null, [
                'label' => 'Statut',
                'show_filter' => true,
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => [
                        'En attente' => Committee::PENDING,
                        'Accepté' => Committee::APPROVED,
                        'Refusé' => Committee::REFUSED,
                        'Pré-approuvé' => Committee::PRE_APPROVED,
                        'Pré-refusé' => Committee::PRE_REFUSED,
                    ],
                ],
            ])
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id', null, [
                'label' => 'ID',
            ])
            ->addIdentifier('name', null, [
                'label' => 'Nom',
            ])
            ->add('postAddress.postalCode', null, [
                'label' => 'Code postal',
            ])
            ->add('postAddress.cityName', null, [
                'label' => 'Ville',
            ])
            ->add('postAddress.country', null, [
                'label' => 'Pays',
            ])
            ->add('phone', null, [
                'label' => 'Téléphone',
                'template' => 'admin/adherent/list_phone.html.twig',
            ])
            ->add('membersCounts', null, [
                'label' => 'Membres',
            ])
            ->add('createdAt', null, [
                'label' => 'Date de création',
            ])
            ->add('hosts', TextType::class, [
                'label' => 'Animateur(s)',
                'template' => 'admin/committee/list_hosts.html.twig',
            ])
            ->add('creator', TextType::class, [
                'label' => 'Créateur',
                'template' => 'admin/committee/list_creator.html.twig',
            ])
            ->add('status', TextType::class, [
                'label' => 'Statut',
                'template' => 'admin/committee/list_status.html.twig',
            ])
            ->add('photo', TextType::class, [
                'virtual_field' => true,
                'label' => "Photo d'identité",
                'template' => 'admin/committee/photo.html.twig',
            ])
            ->add('_action', null, [
                'virtual_field' => true,
                'template' => 'admin/committee/list_actions.html.twig',
            ])
        ;
    }
}
