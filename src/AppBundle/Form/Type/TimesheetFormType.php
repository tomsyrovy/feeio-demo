<?php

namespace AppBundle\Form\Type;

use AppBundle\DependencyInjection\ActivityManager;
use AppBundle\Entity\Company;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\VarDumper\VarDumper;
use UserBundle\Entity\User;

class TimesheetFormType extends AbstractType
{

    /**
     * @var User
     */
    private $user;

    private $commissions;

    private $activityManager;

    /**
     * TimesheetFormType constructor.
     *
     * @param \UserBundle\Entity\User $user
     * @param                         $commissions
     */
    public function __construct( User $user, $commissions ){
        $this->user        = $user;
        $this->commissions = $commissions;

        $this->activityManager = new ActivityManager($this->user);
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'required' => false,
                'widget' => 'single_text',
                'format' => 'd. M. y',
            ))
            ->add('duration', 'integer', array(
                'required' => false,
            ))
            ->add('description', 'text', array(
                'required' => false,
            ))
            ->add('commission', 'entity', array(
                'class' => 'AppBundle\Entity\Commission',
                'choices' => $this->commissions,
                'choice_label' => function ($commission) {
                    return $commission->getName().' ('.$commission->getNameOwn().')';
                },
            ))
        ;

        $formModifier = function (FormInterface $form, Company $company = null) {

            if($company === null){
                $activities = [];
            }else{
                $activities = $this->activityManager->getCompanyActivitiesWithFavourites($company);
            }

            $form->add('activity', 'entity', array(
                'class' => 'AppBundle\Entity\Activity',
                'choices' => $activities,
                'property' => 'name'
            ));

        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {

                $data = $event->getData();

                $company = $data->getCommission() === null ? null : $data->getCommission()->getCompany();

                $formModifier($event->getForm(), $company);
            }
        );

        $builder->get('commission')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $company = $event->getForm()->getData()->getCompany();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $company);
            }
        );

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Timesheet'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_timesheet';
    }
}
