<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\CampaignManager;
use AppBundle\Entity\Commission;
use AppBundle\Entity\Source;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllocationContainerListItemFormType extends AbstractType
{

    /**
     * @var Commission
     */
    private $commission;

    /**
     * @var AllocationContainer
     */
    private $allocationContainer;

    /**
     * AllocationContainerFormType constructor.
     *
     * @param \AppBundle\Entity\Commission          $commission
     * @param \AppBundle\Entity\AllocationContainer $allocationContainer
     */
    public function __construct( Commission $commission, AllocationContainer $allocationContainer ){
        $this->commission          = $commission;
        $this->allocationContainer = $allocationContainer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        if($this->allocationContainer->getYearmonth()){
//            $choices = [
//                0 => 'Pracovník',
//            ];
//        }else{
            $choices = [
                1 => 'Ext. zdroj',
                0 => 'Pracovník',
            ];
//        }
        $builder->add('externalSource', 'choice', [
            'choices' => $choices,
        ]);
        $builder->add('generalSource', 'entity', [
            'required' => false,
            'class' => 'AppBundle\Entity\Source',
            'choices' => $this->commission->getCampaign()->getSourceList()->getSources(),
            'choice_label' => function ($source) {
                return $source->getJobPosition()->getName().' ('.$source->getRateExternal().' Kč)';
            },
        ]);
        $builder->add('concreteSource', 'entity', [
            'required' => false,
            'class' => 'AppBundle\Entity\UserCompany',
            'choices' => $this->commission->getUserCompanies(),
            'choice_label' => function ($userCompany) {
                if(!$userCompany->getData()->getJobPosition()){
                    $jp = '';
                }else{
                    $jp = ' - '.$userCompany->getData()->getJobPosition()->getName();
                }
                return $userCompany->getUser()->getFullname().' ('.$userCompany->getData()->getRateInternal().' Kč) '.$jp;
            },
        ]);
        $builder->add('generalSourceExt', 'text', [
            'required' => false,
        ]);
        $builder->add('concreteSourceExt', 'text', [
            'required' => false,
        ]);
        $builder->add('unit', 'choice', [
            'required' => true,
            'choices' => [
                'ks' => 'ks',
                'h' => 'h',
                'm' => 'm',
                'm2' => 'm2',
                'l' => 'l',
                '-' => '-',
            ]
        ]);
        $builder->add('quantityPlan', 'integer', [
            'required' => false,
        ]);
        $builder->add('buyingPricePlan', 'text', [
            'required' => false,
        ]);
        $builder->add('sellingPricePlan', 'text', [
            'required' => false,
        ]);
        $builder->add('quantityReal', 'number', [
            'required' => false,
        ]);
        $builder->add('buyingPriceReal', 'text', [
            'required' => false,
        ]);
        $builder->add('sellingPriceReal', 'text', [
            'required' => false,
        ]);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AllocationContainerListItem',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_allocationcontainerlistitem';
    }

}
