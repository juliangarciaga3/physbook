<?php

namespace PJM\AppBundle\Entity;

use PJM\AppBundle\Entity\Notifications\NotificationSettings;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PJM\AppBundle\Entity\UserRepository")
 * @UniqueEntity("emailCanonical", message="Cet e-mail {{ emailCanonical }} existe déjà.")
 * @UniqueEntity("usernameCanonical", message="Ce nom d'utilisateur {{ usernameCanonical }} existe déjà.")
 */
class User extends BaseUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastActivity", type="datetime", nullable=true)
     */
    private $lastActivity;

    /**
     * @var string
     *
     * @ORM\Column(name="fams", type="string", length=20, nullable=true)
     * @Assert\NotBlank()
     */
    private $fams;

    /**
     * @var array
     *
     * @ORM\Column(name="nums", type="simple_array", length=20, nullable=true)
     */
    private $nums;

    /**
     * @var string
     *
     * @ORM\Column(name="tabagns", type="string", length=5, nullable=true)
     * @Assert\NotBlank()
     * @Assert\Choice(callback = {"PJM\AppBundle\Enum\UserEnum", "getTabagnsChoices"})
     */
    private $tabagns;

    /**
     * @var int
     *
     * @ORM\Column(name="proms", type="smallint", length=5, nullable=true)
     * @Assert\NotBlank()
     */
    private $proms;

    /**
     * @var string
     *
     * @ORM\Column(name="bucque", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $bucque;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=16, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="appartement", type="string", length=10, nullable=true)
     * @Assert\Regex(
     *     pattern="/(^([A-C])(\d)+([a-zA-Z]+)?$)|SKF/",
     *     match=true,
     *     message="Le kagib doit être de la forme A123."
     * ))
     */
    private $appartement;

    /**
     * @var string
     *
     * @ORM\Column(name="classe", type="string", length=20, nullable=true)
     */
    private $classe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="anniversaire", type="date", nullable=true)
     * @Assert\Date()
     */
    private $anniversaire;

    /**
     * @var bool
     *           1 si féminin
     *
     * @ORM\Column(name="genre", type="boolean")
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity="PJM\AppBundle\Entity\Responsable", mappedBy="user")
     **/
    private $responsables;

    /**
     * @ORM\OneToOne(targetEntity="PJM\AppBundle\Entity\Inbox\Inbox", inversedBy="user", cascade={"persist", "remove"})
     **/
    private $inbox;

    /**
     * Photo de profil.
     *
     * @ORM\OneToOne(targetEntity="PJM\AppBundle\Entity\Media\Photo", cascade={"persist", "remove"})
     **/
    private $photo;

    /**
     * @ORM\OneToMany(targetEntity="PJM\AppBundle\Entity\Media\Photo", mappedBy="proprietaire", cascade={"persist", "remove"})
     **/
    private $photosCreated;

    /**
     * @ORM\OneToMany(targetEntity="PJM\AppBundle\Entity\Compte", mappedBy="user")
     **/
    private $comptes;

    /**
     * @ORM\OneToMany(targetEntity="PJM\AppBundle\Entity\PushSubscription", mappedBy="user", cascade={"all"})
     **/
    private $pushSubscriptions;

    /**
     * @ORM\OneToMany(targetEntity="PJM\AppBundle\Entity\Notifications\Notification", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\OrderBy({"date" = "DESC"})
     **/
    private $notifications;

    /**
     * Réglages des notifications.
     *
     * @ORM\OneToOne(targetEntity="PJM\AppBundle\Entity\Notifications\NotificationSettings", mappedBy="user", cascade={"persist", "remove"})
     **/
    private $notificationSettings;

    public function __toString()
    {
        $user = $this->username;
        if (!empty($this->bucque)) {
            $user = $this->bucque.' '.$user;
        }

        if (!empty($this->prenom) || !empty($this->nom)) {
            $user .= ' (';
            if (!empty($this->prenom)) {
                $user .= $this->prenom;
            }
            if (!empty($this->nom)) {
                if (!empty($this->prenom)) {
                    $user .= ' ';
                }

                $user .= $this->nom;
            }
            $user .= ')';
        }

        return $user;
    }

    public function __construct()
    {
        parent::__construct();

        $this->responsables = new \Doctrine\Common\Collections\ArrayCollection();
        $this->photosCreated = new \Doctrine\Common\Collections\ArrayCollection();
        $this->comptes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->pushSubscriptions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->genre = 0;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fams.
     *
     * @param string $fams
     *
     * @return User
     */
    public function setFams($fams)
    {
        $this->fams = $fams;

        return $this;
    }

    /**
     * Get fams.
     *
     * @return string
     */
    public function getFams()
    {
        return $this->fams;
    }

    /**
     * Set tabagns.
     *
     * @param string $tabagns
     *
     * @return User
     */
    public function setTabagns($tabagns)
    {
        $this->tabagns = $tabagns;

        return $this;
    }

    /**
     * Get tabagns.
     *
     * @return string
     */
    public function getTabagns()
    {
        return $this->tabagns;
    }

    /**
     * Set proms.
     *
     * @param int $proms
     *
     * @return User
     */
    public function setProms($proms)
    {
        $this->proms = $proms;

        return $this;
    }

    /**
     * Get proms.
     *
     * @return int
     */
    public function getProms()
    {
        return $this->proms;
    }

    /**
     * Set bucque.
     *
     * @param string $bucque
     *
     * @return User
     */
    public function setBucque($bucque)
    {
        $this->bucque = $bucque;

        return $this;
    }

    /**
     * Get bucque.
     *
     * @return string
     */
    public function getBucque()
    {
        return $this->bucque;
    }

    /**
     * Set prenom.
     *
     * @param string $prenom
     *
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom.
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set nom.
     *
     * @param string $nom
     *
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom.
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set telephone.
     *
     * @param string $telephone
     *
     * @return User
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone.
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set appartement.
     *
     * @param string $appartement
     *
     * @return User
     */
    public function setAppartement($appartement)
    {
        $this->appartement = $appartement;

        return $this;
    }

    /**
     * Get appartement.
     *
     * @return string
     */
    public function getAppartement()
    {
        return $this->appartement;
    }

    /**
     * Set classe.
     *
     * @param string $classe
     *
     * @return User
     */
    public function setClasse($classe)
    {
        $this->classe = $classe;

        return $this;
    }

    /**
     * Get classe.
     *
     * @return string
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * Set anniversaire.
     *
     * @param string $anniversaire
     *
     * @return User
     */
    public function setAnniversaire($anniversaire)
    {
        if (gettype($anniversaire) == 'string') {
            $this->anniversaire = \DateTime::createFromFormat('d/m/Y', $anniversaire);
        } else {
            $this->anniversaire = $anniversaire;
        }

        return $this;
    }

    /**
     * Get anniversaire.
     *
     * @return \DateTime
     */
    public function getAnniversaire()
    {
        return $this->anniversaire;
    }

    /**
     * Set lastActivity.
     *
     * @param \DateTime $lastActivity
     *
     * @return User
     */
    public function setLastActivity($lastActivity)
    {
        $this->lastActivity = $lastActivity;

        return $this;
    }

    /**
     * Get lastActivity.
     *
     * @return \DateTime
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * Add responsables.
     *
     * @param \PJM\AppBundle\Entity\Responsable $responsables
     *
     * @return User
     */
    public function addResponsable(\PJM\AppBundle\Entity\Responsable $responsables)
    {
        $this->responsables[] = $responsables;

        return $this;
    }

    /**
     * Remove responsables.
     *
     * @param \PJM\AppBundle\Entity\Responsable $responsables
     */
    public function removeResponsable(\PJM\AppBundle\Entity\Responsable $responsables)
    {
        $this->responsables->removeElement($responsables);
    }

    /**
     * Get responsables.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getResponsables()
    {
        return $this->responsables;
    }

    /**
     * Set inbox.
     *
     * @param \PJM\AppBundle\Entity\Inbox\Inbox $inbox
     *
     * @return User
     */
    public function setInbox(\PJM\AppBundle\Entity\Inbox\Inbox $inbox)
    {
        $this->inbox = $inbox;

        return $this;
    }

    /**
     * Get inbox.
     *
     * @return \PJM\AppBundle\Entity\Inbox\Inbox
     */
    public function getInbox()
    {
        return $this->inbox;
    }

    /**
     * Set photo.
     *
     * @param \PJM\AppBundle\Entity\Media\Photo $photo
     *
     * @return User
     */
    public function setPhoto(\PJM\AppBundle\Entity\Media\Photo $photo = null)
    {
        $this->photo = $photo;

        if ($photo !== null) {
            $this->addPhotosCreated($photo);
        }

        return $this;
    }

    /**
     * Get photo.
     *
     * @return \PJM\AppBundle\Entity\Media\Photo
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set genre.
     *
     * @param bool $genre
     *
     * @return User
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;

        return $this;
    }

    /**
     * Get genre.
     *
     * @return bool
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * Add photosCreated.
     *
     * @param \PJM\AppBundle\Entity\Media\Photo $photosCreated
     *
     * @return User
     */
    public function addPhotosCreated(\PJM\AppBundle\Entity\Media\Photo $photosCreated)
    {
        $this->photosCreated[] = $photosCreated;

        return $this;
    }

    /**
     * Remove photosCreated.
     *
     * @param \PJM\AppBundle\Entity\Media\Photo $photosCreated
     */
    public function removePhotosCreated(\PJM\AppBundle\Entity\Media\Photo $photosCreated)
    {
        $this->photosCreated->removeElement($photosCreated);
    }

    /**
     * Get photosCreated.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPhotosCreated()
    {
        return $this->photosCreated;
    }

    /**
     * Add comptes.
     *
     * @param \PJM\AppBundle\Entity\Compte $comptes
     *
     * @return User
     */
    public function addCompte(\PJM\AppBundle\Entity\Compte $comptes)
    {
        $this->comptes[] = $comptes;

        return $this;
    }

    /**
     * Remove comptes.
     *
     * @param \PJM\AppBundle\Entity\Compte $comptes
     */
    public function removeCompte(\PJM\AppBundle\Entity\Compte $comptes)
    {
        $this->comptes->removeElement($comptes);
    }

    /**
     * Get comptes.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComptes()
    {
        return $this->comptes;
    }

    /**
     * Add pushSubscription.
     *
     * @param \PJM\AppBundle\Entity\PushSubscription $pushSubscription
     *
     * @return User
     */
    public function addPushSubscription(\PJM\AppBundle\Entity\PushSubscription $pushSubscription)
    {
        $this->pushSubscriptions[] = $pushSubscription;

        return $this;
    }

    /**
     * Remove pushSubscription.
     *
     * @param \PJM\AppBundle\Entity\PushSubscription $pushSubscription
     */
    public function removePushSubscription(\PJM\AppBundle\Entity\PushSubscription $pushSubscription)
    {
        $this->pushSubscriptions->removeElement($pushSubscription);
    }

    /**
     * Get pushSubscriptions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPushSubscriptions()
    {
        return $this->pushSubscriptions;
    }

    /**
     * Set notificationSettings.
     *
     * @param NotificationSettings $notificationSettings
     *
     * @return User
     */
    public function setNotificationSettings(NotificationSettings $notificationSettings = null)
    {
        $this->notificationSettings = $notificationSettings;

        return $this;
    }

    /**
     * Get notificationSettings.
     *
     * @return NotificationSettings
     */
    public function getNotificationSettings()
    {
        if ($this->notificationSettings === null) {
            $defaultSettings = new NotificationSettings();
            $defaultSettings->setUser($this);

            return $defaultSettings;
        }

        return $this->notificationSettings;
    }

    /**
     * Add notification.
     *
     * @param \PJM\AppBundle\Entity\Notifications\Notification $notification
     *
     * @return User
     */
    public function addNotification(\PJM\AppBundle\Entity\Notifications\Notification $notification)
    {
        $notification->setUser($this);
        $this->notifications[] = $notification;

        return $this;
    }

    /**
     * Remove notification.
     *
     * @param \PJM\AppBundle\Entity\Notifications\Notification $notification
     */
    public function removeNotification(\PJM\AppBundle\Entity\Notifications\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @return array
     */
    public function getNums()
    {
        return $this->nums;
    }

    /**
     * @param array $nums
     */
    public function setNums(array $nums)
    {
        $this->nums = $nums;
    }
}
