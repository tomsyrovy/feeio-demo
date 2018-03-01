<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Company;
use AppBundle\Entity\UserCompanyTemporalityStatus;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CompanyGroupFormModelType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'Název skupiny',
                'required' => false,
            ))
            ->add('members', 'hidden' );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Form\Model\CompanyGroupFormModel'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_companygroupmodel';
    }
}
