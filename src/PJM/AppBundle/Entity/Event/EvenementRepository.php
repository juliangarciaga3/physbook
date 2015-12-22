<?php

namespace PJM\AppBundle\Entity\Event;

use Doctrine\ORM\EntityRepository;
use PJM\AppBundle\Entity\Boquette;
use PJM\AppBundle\Entity\User;

/**
 * EvenementRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EvenementRepository extends EntityRepository
{
    /**
     * @param User           $user
     * @param int            $max
     * @param string         $quand        'after' or 'before'
     * @param \DateTime|null $date
     * @param Evenement|null $eventExclure
     *
     * @return array
     */
    public function getEvents(User $user, $max = 6, $quand = 'after', \DateTime $date = null, Evenement $eventExclure = null)
    {
        if ($max <= 0) {
            return array();
        }

        if ($date === null) {
            $date = new \DateTime();
        }

        $eventsPublics = $this->__getEvents(null, $max, $quand, $date, $eventExclure);
        $eventsPrives = $this->__getEvents($user, $max, $quand, $date, $eventExclure);

        $res = array_merge($eventsPublics, $eventsPrives);

        $reverse = ($quand == 'before');
        usort($res, function (Evenement $a, Evenement $b) use ($reverse) {
            if ($reverse) {
                return $a->getDateDebut() < $b->getDateDebut();
            }

            return $a->getDateDebut() > $b->getDateDebut();
        });

        if ($reverse) {
            $res = array_reverse($res);
        }

        return $res;
    }

    private function __getEvents(User $user = null, $max, $quand, \DateTime $date, Evenement $eventExclure = null)
    {
        $qb = $this->createQueryBuilder('e');

        if ($user === null) {
            $qb
                ->where('e.public = true')
            ;
        } else {
            $qb
                ->innerJoin('e.invitations', 'i')
                ->where('e.public = false')
                ->andWhere('i.invite = :user')
                ->setParameter('user', $user)
            ;
        }

        if ($eventExclure !== null) {
            $qb
                ->andWhere('e != :eventExclure')
                ->setParameter('eventExclure', $eventExclure)
            ;
        }

        if ($quand == 'after') {
            $qb
                ->andWhere('e.dateDebut > :date')
                ->orderBy('e.dateDebut', 'ASC')
            ;
        } elseif ($quand == 'before') {
            $qb
                ->andWhere('e.dateDebut <= :date')
                ->orderBy('e.dateDebut', 'DESC')
            ;
        }

        $qb->setParameter('date', $date);

        if ($max !== null) {
            $qb
                ->setMaxResults($max)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function getNextEvents()
    {
        $qb = $this->createQueryBuilder('e');

        $qb
            ->where('e.dateDebut > :now')
            ->andWhere('e.dateDebut < :day')
            ->setParameter('now', new \DateTime())
            ->setParameter('day', new \DateTime('+1 day'))
        ;

        return $qb->getQuery()->getResult();
    }

    public function findBetweenDates(\DateTime $debut, \DateTime $fin, Boquette $boquette = null)
    {
        $qb = $this->createQueryBuilder('e');

        $qb
            ->where('e.dateDebut <= :fin')
            ->andWhere('e.dateFin >= :debut')
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
        ;

        if (isset($boquette)) {
            $qb
                ->andWhere('e.boquette = :boquette')
                ->setParameter('boquette', $boquette);

            if (null !== $boquette->getLieux()) {
                $qb
                    ->orWhere('e.lieu IN(:lieux)')
                    ->setParameter('lieux', $boquette->getLieux());
            }
        }

        return $qb->getQuery()->getResult();
    }
}
