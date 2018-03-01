<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Campaign;
use AppBundle\Entity\Company;
use AppBundle\Entity\CompanyGroup;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CommissionFormType extends AbstractType
{

    /**
     * @var Campaign
     */
    private $campaign;

    /**
     * CommissionFormType constructor.
     *
     * @param $campaign
     */
    public function __construct( $campaign ){
        $this->campaign = $campaign;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('startDate', 'entity', array(
            'class' => 'AppBundle\Entity\YearMonth',
            'label' => 'Zahájení',
            'property' => 'code',
            'query_builder' => function (EntityRepository $er) {
                return
                    $er->createQueryBuilder('ym')->where('ym.year = :year')->setParameters(array(
                        'year' => $this->campaign->getYear()->getYear(),
                    ));
            },
        ));
        $builder->add('endDate', 'entity', array(
            'class' => 'AppBundle\Entity\YearMonth',
            'label' => 'Ukončení',
            'property' => 'code',
            'query_builder' => function (EntityRepository $er) {
                return
                    $er->createQueryBuilder('ym')->where('ym.year = :year')->setParameters(array(
                        'year' => $this->campaign->getYear()->getYear(),
                    ));
            },
        ));
        $builder->add('nameOwn', 'text', [
            'label' => 'Název',
            'required' => false,
        ]);
        $builder->add('description', 'textarea', [
            'label' => 'Popis',
            'required' => false,
        ]);
        $builder->add('status', 'choice', [
            'label' => 'Stav',
            'choices' => [
                'Prezentováno' => 'Prezentováno',
                'Odsouhlaseno' => 'Odsouhlaseno',
                'Objednáno' => 'Objednáno',
                'Fakturováno' => 'Fakturováno',
            ],
            'required' => true,
        ]);
        $builder->add('clientApproved', 'checkbox', [
            'label' => 'Schválený klientem',
            'required' => false,
        ]);
        $builder->add('billable', 'checkbox', [
            'label' => 'Zúčtovatelný',
            'required' => false,
        ]);
        $builder->add('repeatable', 'choice', [
            'label' => 'Opakovatelný job v rámci kampaně',
            'choices' => [
                true => 'ano (job se opakuje v rámci kampaně a trvá jeden měsíc)',
                false => 'ne (job se neopakuje v rámci kampaně a může trvat déle než jeden měsíc)'
            ]
        ]);



//        $formModifier = function (FormInterface $form, Company $company = null) {
//
//            $userCompanyRelations = null === $company ? array() : $company->getUserCompanyRelationsOfTemporalityStatus('enabled');
//
//            $form->add('commissionUserCompanyRelations', 'collection',
//                array(
//                    'type' => new CommissionUserCompanyRelationFormType($userCompanyRelations),
//                    'label' => 'Správci',
//                    'allow_add' => true,
//                    'allow_delete' => true,
//                    'by_reference' => false,
//                    'prototype' => true,
//                    'prototype_name' => 'usercompany__name__',
//                    'cascade_validation' => true,
//                )
//            );
//
//        };
//
//        $builder->addEventListener(
//            FormEvents::PRE_SET_DATA,
//            function (FormEvent $event) use ($formModifier) {
//
//                $data = $event->getData();
//
//                $formModifier($event->getForm(), $data->getCompany());
//            }
//        );
//
//        $builder->get('company')->addEventListener(
//            FormEvents::POST_SUBMIT,
//            function (FormEvent $event) use ($formModifier) {
//                // It's important here to fetch $event->getForm()->getData(), as
//                // $event->getData() will get you the client data (that is, the ID)
//                $company = $event->getForm()->getData();
//
//                // since we've added the listener to the child, we'll have to pass on
//                // the parent to the callback functions!
//                $formModifier($event->getForm()->getParent(), $company);
//            }
//        );

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Commission'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_commission';
    }
}
