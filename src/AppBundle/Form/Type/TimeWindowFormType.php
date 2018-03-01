<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\TimeWindow;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeWindowFormType extends AbstractType
{

    /**
     * @var TimeWindow
     */
    private $timeWindow;

    /**
     * @var int
     */
    private $offset;

    /**
     * TimeWindowFormType constructor.
     *
     * @param \AppBundle\Entity\TimeWindow $timeWindow
     * @param int                          $offset
     */
    public function __construct( TimeWindow $timeWindow, $offset = 37 ){
        $this->timeWindow = $timeWindow;
        $this->offset     = $offset;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('yearmonthFrom', 'entity', array(
                'class' => 'AppBundle\Entity\YearMonth',
                'property' => 'code',
                'query_builder' => function (EntityRepository $er) {
                    return
                        $er->createQueryBuilder('ym')->where('ym.id > :ym1')->andWhere('ym.id < :ym2')->setParameters(array(
                            'ym1' => $this->timeWindow->getYearmonthFrom()->getId()-$this->offset,
                            'ym2' => $this->timeWindow->getYearmonthFrom()->getId()+$this->offset-1
                        ));
                },
            ))
            ->add('yearmonthTo', 'entity', array(
                'class' => 'AppBundle\Entity\YearMonth',
                'property' => 'code',
                'query_builder' => function (EntityRepository $er) {
                    return
                        $er->createQueryBuilder('ym')->where('ym.id > :ym1')->andWhere('ym.id < :ym2')->setParameters(array(
                            'ym1' => $this->timeWindow->getYearmonthTo()->getId()-$this->offset,
                            'ym2' => $this->timeWindow->getYearmonthTo()->getId()+$this->offset-1
                        ));
                },
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\TimeWindow'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_timewindow';
    }
}
