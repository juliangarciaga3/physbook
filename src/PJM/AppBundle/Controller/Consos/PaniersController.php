<?php

namespace PJM\AppBundle\Controller\Consos;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use PJM\AppBundle\Entity\Item;

class PaniersController extends BoquetteController
{
    public function __construct()
    {
        $this->slug = 'paniers';
        $this->itemSlug = 'panier';
    }

    public function indexAction(Request $request)
    {
        return $this->render('PJMAppBundle:Consos:Paniers/index.html.twig', array(
            'solde' => $this->getSolde(),
            'prixPanier' => $this->getPrixPanier(),
        ));
    }

    public function getCurrentPanier()
    {
        $panier = $this->getItem($this->itemSlug);

        if (null === $panier) {
            $panier = new Item();
            $panier->setLibelle('Panier de fruits et légumes');
            $panier->setPrix(500);
            $panier->setSlug($this->itemSlug);
            $panier->setBoquette($this->getBoquette());
            $panier->setValid(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($panier);
            $em->flush();
        }

        return $panier;
    }

    public function getPrixPanier()
    {
        return $this->getCurrentPanier()->getPrix();
    }

    /*
    * ADMIN
    */
    public function adminAction()
    {
        // TODO faire reloguer l'utilisateur sauf si redirection depuis l'admin

        return $this->render('PJMAppBundle:Consos:Paniers/Admin/index.html.twig');
    }

    // ajout et liste paniers
    public function listePaniersAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $panier = new Item();
        $panier->setLibelle('Panier de fruits et légumes');
        $panier->setBoquette($this->getBoquette());
        $panier->setSlug($this->itemSlug);

        $form = $this->createForm(new PanierType(), $panier, array(
            'method' => 'POST',
            'action' => $this->generateUrl('pjm_app_consos_paniers_admin_listePaniers'),
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // on enregistre le nouveau panier et on désactive l'ancien
                $ancienPanier = $this->getCurrentPanier();
                $ancienPanier->setValid(false);
                $em->persist($ancienPanier);
                $em->persist($panier);

                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'success',
                    'Le panier a bien été ajouté.'
                );
            } else {
                $request->getSession()->getFlashBag()->add(
                    'danger',
                    'Un problème est survenu lors de l\'ajout du panier. Réessaye.'
                );

                $data = $form->getData();

                foreach ($form->getErrors() as $error) {
                    $request->getSession()->getFlashBag()->add(
                        'warning',
                        $error->getMessage()
                    );
                }
            }

            return $this->redirect($this->generateUrl('pjm_app_consos_paniers_admin_index'));
        }

        $datatable = $this->get("pjm.datatable.paniers.liste");
        $datatable->buildDatatableView();

        return $this->render('PJMAppBundle:Consos:Paniers/Admin/listePaniers.html.twig', array(
            'form' => $form->createView(),
            'datatable' => $datatable
        ));
    }

    // ajout et liste d'un crédit
    public function listeCreditsAction(Request $request)
    {
        return $this->gestionCredits(
            $request,
            'pjm_app_consos_paniers_admin_listeCredits',
            'pjm_app_consos_paniers_admin_creditsResults',
            'pjm_app_consos_paniers_admin_index'
        );
    }

    // action ajax de rendu de la liste des crédits
    public function creditsResultsAction()
    {
        return $this->creditsResults();
    }
}
