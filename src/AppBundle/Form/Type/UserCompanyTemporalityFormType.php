<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserCompanyTemporalityFormType extends AbstractType
{

    private $company;

    public function __construct(Company $company) {

        $this->company = $company;

    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jobposition', 'entity', array(
                'label' => 'Pracovní pozice',
                'class' => 'AppBundle:JobPosition',
                'choices' => $this->company->getJobPositionsEnabled(),
                'property' => 'name',
            ))
            ->add('rateInternal', 'money', array(
                'label' => 'Interní sazba',
                'required' => false,
                'currency' => 'CZK',
                'scale' => 0,
            ))
            ->add('hours', 'integer', array(
                'label' => 'Časová kapacita',
                'required' => false,
                'scale' => 0,
            ))
            ->add('role', 'entity', array(
                'label' => 'Role',
                'class' => 'AppBundle:Role',
                'choices' => $this->company->getRolesEnabled(),
                'property' => 'name',
            ))
//            ->add('status', 'entity', array(
//                'label' => 'Stav',
//                'class' => 'AppBundle:UserCompanyTemporalityStatus',
//                'query_builder' => function (EntityRepository $er) {
//                    return $er->createQueryBuilder('s')
//                              ->where('s.automation = :automation')
//                              ->orderBy('s.id', 'ASC')
//                              ->setParameter('automation', false);
//                },
//                'property' => 'name',
//            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\UserCompanyTemporality'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_usercompanytemporality';
    }
}
