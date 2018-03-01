<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Commission;
use AppBundle\Entity\Source;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AllocationUnitFormType extends AbstractType
{
    /**
     * @var Commission
     */
    private $commission;

    /**
     * AllocationContainerFormType constructor.
     *
     * @param \AppBundle\Entity\Commission          $commission
     */
    public function __construct( Commission $commission){
        $this->commission          = $commission;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('yearMonth', 'entity', [
//                'label' => false,
//                'class' => 'AppBundle\Entity\YearMonth',
//                'property' => 'code',
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
//            ])
//            ->add('userCompany', 'entity', [
//                'label' => false,
//                'class' => 'AppBundle\Entity\UserCompany',
//                'choices' => $this->commission->getUserCompanies(),
//                'choice_label' => function ($userCompany) {
//                    return $userCompany->getUser()->getFullname();
//                },
//            ])
            ->add('hoursPlan', 'integer', [
                'label' => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\AllocationUnit',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_allocationunit';
    }
}
