<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Commission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\VarDumper\VarDumper;

class CostFormType extends AbstractType
{

    /**
     * @var
     */
    protected $commissions;

    /**
     * @var
     */
    protected $yearmonths;

    /**
     * CostFormType constructor.
     *
     * @param      $commissions
     * @param      $yearmonths
     */
    public function __construct( $commissions, $yearmonths ){
        $this->commissions      = $commissions;
        $this->yearmonths       = $yearmonths;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $required = isset($options['required']) ? $options['required'] : true;

        $builder
            ->add('commission', 'entity', [
                'class' => 'AppBundle\Entity\Commission',
                'choices' => $this->commissions,
                'property' => 'name',
                'required' => $required,
            ])
            ->add('title', 'text', array(
                'required' => $required))
            ->add('yearmonthPlan', 'entity', [
                'class' => 'AppBundle\Entity\Yearmonth',
                'choices' => $this->yearmonths,
                'property' => 'code',
                'required' => false,
            ])
            ->add('priceNonVatPlan', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 2))
            ->add('vatRatePlan', 'percent', array(
                'required' => false,
                'type' => 'integer',
                'scale' => 2))
            ->add('rebillingPriceNonVatPlan', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 2))
            ->add('rebillingVatRatePlan', 'percent', array(
                'required' => false,
                'type' => 'integer',
                'scale' => 2))
            ->add('yearmonthReal', 'entity', [
                'class' => 'AppBundle\Entity\Yearmonth',
                'choices' => $this->yearmonths,
                'property' => 'code',
                'required' => false,
            ])
            ->add('priceNonVatReal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 2))
            ->add('vatRateReal', 'percent', array(
                'required' => false,
                'type' => 'integer',
                'scale' => 2))
            ->add('rebillingPriceNonVatReal', 'money', array(
                'required' => false,
                'currency' => 'CZK',
                'scale' => 2))
            ->add('rebillingVatRateReal', 'percent', array(
                'required' => false,
                'type' => 'integer',
                'scale' => 2))
            ->add('receivedDocumentNumber', 'text', array(
                'required' => false
                ))
            ->add('issuedDocumentNumber', 'text', array(
                'required' => false
                ))
        ;

        $this->addEventListeners($builder);

    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     */
    protected function addEventListeners(FormBuilderInterface $builder){

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {

                $data = $event->getData();

                $this->formModify($event->getForm(), $data->getCommission());

            }
        );

        $builder->get('commission')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $commission = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $this->formModify($event->getForm()->getParent(), $commission);
            }
        );

    }

    /**
     * @param \Symfony\Component\Form\FormInterface $form
     * @param \AppBundle\Entity\Commission|null     $commission
     */
    protected function formModify(FormInterface $form, Commission $commission = null){

        $suppliers = null === $commission ? array() : $commission->getCompany()->getContactsOfTypeCode('supplier');

        $form->add('supplier', 'entity', array(
            'label' => 'Dodavatel',
            'class'       => 'AppBundle\Entity\Contact',
            'choices'     => $suppliers,
            'property' => 'title',
            'required' => false,
        ));

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Cost'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_cost';
    }
}
