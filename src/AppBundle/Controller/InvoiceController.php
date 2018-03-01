<?php

namespace AppBundle\Controller;
use AppBundle\DependencyInjection\CommissionManager\CommissionManager;
use AppBundle\Entity\InvoiceItem;
use AppBundle\Form\Type\InvoiceItemFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Allocation controller.
 */
class InvoiceController extends BaseController
{
    /**
     * @Route("/commission/{commission_id}/invoice-item/list")
     * @Template()
     */
    public function listAction($commission_id){

        $em = $this->getDoctrine()->getManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){
            $this->addFlash('danger', 'Tato zakázka neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();

        $cm = new CommissionManager($em);
        $commissionWhereParticipate = $cm->getCommissionsWhereParticipate($user);
        $check = in_array($commission, $commissionWhereParticipate, true);

        if(!$check){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $criteria = [
            'commission' => $commission,
            'enabled' => true,
        ];
        $orderBy = [
            'created' => 'ASC'
        ];
        $entities = $em->getRepository('AppBundle:InvoiceItem')->findBy($criteria, $orderBy);

        $data = [
            'entities' => $entities,
            'commission2' => $commission,
            'commission' => [
                'data' => $commission
            ]
        ];

        return $data;

    }

    /**
     * @Route("/commission/{commission_id}/invoice-item/create")
     * @Template()
     */
    public function createAction($commission_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $commission = $em->getRepository('AppBundle:Commission')->find($commission_id);

        if(!$commission){
            $this->addFlash('danger', 'Tato zakázka neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $user = $this->getUser();

        $cm = new CommissionManager($em);
        $commissionWhereParticipate = $cm->getCommissionsWhereParticipate($user);
        $check = in_array($commission, $commissionWhereParticipate, true);

        if(!$check){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $entity = new InvoiceItem();
        $entity->setEnabled(true);
        $entity->setCommission($commission);
        $entity->setAuthor($user);
        $entity->setCreated(new \DateTime());

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $em->persist($entity);
                $em->flush();

                $this->addFlash('success', 'Položka k fakturaci byla vytvořena.');
                $params = [
                    'commission_id' => $commission_id
                ];
                return $this->redirectToRoute('app_invoice_list', $params);
            }else{
                $this->addFlash('danger', 'Položka k fakturaci nebyla vytvořena.');
            }
        }

        $data = [
            'form' => $form->createView(),
            'commission' => $commission,
        ];

        return $data;

    }

    /**
     * @Route("/invoice-item/{invoiceItem_id}/update")
     * @Template()
     */
    public function updateAction($invoiceItem_id, Request $request){

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:InvoiceItem')->find($invoiceItem_id);

        if(!$entity){
            $this->addFlash('danger', 'Tato položka k fakturaci neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $commission = $entity->getCommission();

        $user = $this->getUser();

        $cm = new CommissionManager($em);
        $commissionWhereParticipate = $cm->getCommissionsWhereParticipate($user);
        $check = in_array($commission, $commissionWhereParticipate, true);

        if(!$check){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $form = $this->getForm($entity);

        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                $em->persist($entity);
                $em->flush();

                $this->addFlash('success', 'Položka k fakturaci byla upravena.');
                $params = [
                    'commission_id' => $commission->getId(),
                ];
                return $this->redirectToRoute('app_invoice_list', $params);
            }else{
                $this->addFlash('danger', 'Položka k fakturaci nebyla upravena.');
            }
        }

        $data = [
            'form' => $form->createView(),
            'commission' => $commission,
        ];

        return $data;

    }

    /**
     * @Route("/invoice-item/{invoiceItem_id}/delete")
     */
    public function deleteAction($invoiceItem_id){

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:InvoiceItem')->find($invoiceItem_id);

        if(!$entity){
            $this->addFlash('danger', 'Tato položka k fakturaci neexistuje.');
            return $this->redirectToRoute('app_client_list');
        }

        $commission = $entity->getCommission();

        $user = $this->getUser();

        $cm = new CommissionManager($em);
        $commissionWhereParticipate = $cm->getCommissionsWhereParticipate($user);
        $check = in_array($commission, $commissionWhereParticipate, true);

        if(!$check){
            $this->addFlash('danger', 'K této zakázce nemáte přístup.');
            return $this->redirectToRoute('app_client_list');
        }

        $entity->setEnabled(false);
        $em->persist($entity);
        $em->flush();

        $this->addFlash('success', 'Položka k fakturaci byla odstraněna.');
        $params = [
            'commission_id' => $entity->getCommission()->getId(),
        ];

        return $this->redirectToRoute('app_invoice_list', $params);


    }

    /**
     * @param \AppBundle\Entity\InvoiceItem $entity
     *
     * @return \Symfony\Component\Form\Form
     */
    private function getForm(InvoiceItem $entity){

        $type = new InvoiceItemFormType();

        $form = $this->createForm($type, $entity);

        return $form;

    }
}
