<?php

namespace PJM\AppBundle\Controller\Consos;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CvisController extends BoquetteController
{
    public function __construct()
    {
        $this->slug = 'cvis';
    }

    public function indexAction(Request $request)
    {
        $utils = $this->get('pjm.services.utils');
        $listeHistoriques = $utils->getHistorique($this->getUser(), $this->slug, 5);
        $produitMoment = $utils->getFeaturedItem($this->slug);
        $ziConsommateurs = $this->getTopConsommateurs(date('m')); // top du mois en cours
        $listeProduits = $this->getItems(true, 5);
        $stats = array(
            'achats' => $this->compterAchatsBoquette(),
            'saucisson' => $this->compterAchatsItem('saucisson'),
            'burgers' => $this->compterAchatsItem('burger')
        );

        return $this->render('PJMAppBundle:Consos:Cvis/index.html.twig', array(
            'boquetteSlug' => $this->slug,
            'solde' => $this->getSolde(),
            'listeHistoriques' => $listeHistoriques,
            'ziConsommateurs' => $ziConsommateurs,
            'produitMoment' => $produitMoment,
            'listeProduits' => $listeProduits,
            'stats' => $stats
        ));
    }

    /*
    * ADMIN
    */
    public function adminAction()
    {
        // TODO faire reloguer l'utilisateur sauf si redirection depuis l'admin

        return $this->render('PJMAppBundle:Admin:Consos/Cvis/index.html.twig', array(
            'boquetteSlug' => $this->slug
        ));
    }
}
