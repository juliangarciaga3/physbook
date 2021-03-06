<?php

namespace PJM\AppBundle\Datatables\Admin;

use PJM\AppBundle\Datatables\BaseDatatable;
use PJM\AppBundle\Services\Image as ImageService;

/**
 * Class BoquettesDatatable.
 */
class BoquettesDatatable extends BaseDatatable
{
    private $imageExt;

    public function setImageExt(ImageService $imageExt)
    {
        $this->imageExt = $imageExt;
    }

    /**
     * {@inheritdoc}
     */
    public function buildDatatable($locale = null)
    {
        parent::buildDatatable($locale);

        $this->options->setOption('order', [[0, 'asc']]);

        $this->ajax->set(array(
            'url' => $this->router->generate('pjm_app_admin_boquettesResults'),
        ));

        $this->columnBuilder
            ->add('nom', 'column', array(
                'title' => 'Nom',
            ))
            ->add('slug', 'column', array(
                'title' => 'Slug',
            ))
            ->add('image.id', 'column', array('visible' => false))
            ->add('image.ext', 'column', array('visible' => false))
            ->add('image.alt', 'column', array(
                'title' => 'Logo',
                'width' => '100px',
            ))
            ->add('caisseSMoney', 'column', array(
                'title' => 'Caisse S-Money',
            ))
            ->add(null, 'action', array(
                'title' => 'Actions',
                'actions' => array(
                    array(
                        'route' => 'pjm_app_admin_gestionBoquettes',
                        'route_parameters' => array(
                            'boquette' => 'id',
                        ),
                        'label' => 'Modifier',
                        'icon' => 'glyphicon glyphicon-edit',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => 'Modifier',
                            'class' => 'btn btn-default btn-xs',
                            'role' => 'button',
                        ),
                    ),
                ),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getLineFormatter()
    {
        $formatter = function ($line) {
            $line['image']['alt'] = !empty($line['image']['id']) ?
                $this->imageExt->html($line['image']['id'], $line['image']['ext'], $line['image']['alt']) :
                "Pas d'image";

            return $line;
        };

        return $formatter;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'PJM\AppBundle\Entity\Boquette';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'boquettes_datatable';
    }
}
