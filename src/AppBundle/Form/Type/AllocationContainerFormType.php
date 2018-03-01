<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\AllocationContainer;
use AppBundle\Entity\Commission;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllocationContainerFormType extends AbstractType
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
    public function __construct( \AppBundle\Entity\Commission $commission, \AppBundle\Entity\AllocationContainer $allocationContainer ){
        $this->commission          = $commission;
        $this->allocationContainer = $allocationContainer;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//           $builder ->add('yearmonth', 'entity', [
//                'label' => 'Alokace',
//                'class' => 'AppBundle\Entity\YearMonth',
//                'property' => 'code',
//                'required' => false,
//                'query_builder' => function (EntityRepository $er) {
//
//                    $now = new \DateTime();
//                    $nowMonth = $now->format('n');
//                    $nowYear = $now->format('Y');
//
//                    $criteria = [
//                        'month' => $nowMonth,
//                        'year' => $nowYear,
//                    ];
//                    $ymNow = $er->findOneBy($criteria);
//
//                    return
//                        $er->createQueryBuilder('ym')->where('ym >= :ym1')->andWhere('ym <= :ym2')->setParameters(array(
//                            'ym1' => $ymNow,
//                            'ym2' => $this->commission->getEndDate(),
//                        ));
//                },
//        ]);

        $builder->add('allocationContainerLists', 'collection', array(
            'label' => 'Seznamy',
            'type' => new AllocationContainerListFormType($this->commission, $this->allocationContainer),
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'prototype' => true,
            'prototype_name' => 'allocationContainerList__name__',
            'cascade_validation' => true,
        ));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AllocationContainer'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_allocationcontainer';
    }
}
