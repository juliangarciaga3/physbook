<?php

namespace PJM\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vacances
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PJM\AppBundle\Entity\VacancesRepository")
 */
class Vacances
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date")
     * @Assert\NotBlank()
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date")
     * @Assert\NotBlank()
     */
    private $dateFin;


    /**
     * @var boolean
     *
     * @ORM\Column(name="credite_brags", type="boolean")
     */
    private $crediteBrags;


    public function __construct()
    {
        $this->dateDebut = new \DateTime();
        $this->dateFin = new \DateTime();
        $this->crediteBrags = false;
    }

    public function getNbJours()
    {
        return $this->dateFin->diff($this->dateDebut)->days+1;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set crediteBrags
     *
     * @param boolean $crediteBrags
     * @return Vacances
     */
    public function setCrediteBrags($crediteBrags)
    {
        $this->crediteBrags = $crediteBrags;

        return $this;
    }

    /**
     * Get crediteBrags
     *
     * @return boolean
     */
    public function getCrediteBrags()
    {
        return $this->crediteBrags;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Vacances
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Vacances
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }
}
