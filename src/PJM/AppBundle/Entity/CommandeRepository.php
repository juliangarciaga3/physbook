<?php

namespace PJM\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use PJM\UserBundle\Entity\User;

/**
 * CommandeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommandeRepository extends EntityRepository
{
    public function findByUserAndItemSlug(User $user, $item_slug)
    {
        $query = $this->createQueryBuilder('c')
                    ->where('c.user = :user')
                    ->join('c.item', 'i', 'WITH', 'i.slug = :item_slug')
                    ->setParameters(array(
                        'user' => $user,
                        'item_slug'  => $item_slug,
                    ))
                    ->orderBy('c.date', 'desc')
                    ->getQuery();

        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function findByItemSlug($item_slug)
    {
        $query = $this->createQueryBuilder('c')
                    ->join('c.item', 'i', 'WITH', 'i.slug = :item_slug')
                    ->setParameters(array(
                        'item_slug'  => $item_slug,
                    ))
                    ->orderBy('c.date', 'desc')
                    ->getQuery();

        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function findByItemSlugAndValid($item_slug, $valid = true)
    {
        $query = $this->createQueryBuilder('c')
                    ->where('c.valid = :valid')
                    ->join('c.item', 'i', 'WITH', 'i.slug = :item_slug')
                    ->setParameters(array(
                        'item_slug'  => $item_slug,
                        'valid' => $valid
                    ))
                    ->orderBy('c.date', 'desc')
                    ->getQuery();

        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function findByItemSlugAndValidAndAtDate($item_slug, $valid, \DateTime $date)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.valid = :valid')
            ->setParameter('valid', $valid)
            ->andWhere('c.dateDebut <= :dateDebut')
            ->setParameter('dateDebut', $date->setTime(23,59,59))
        ;

        if (!$valid) {
            $qb
                ->andWhere('c.dateFin >= :dateFin')
                ->setParameter('dateFin', $date->setTime(23,59,59))
            ;
        }

        $query = $qb
            ->join('c.item', 'i', 'WITH', 'i.slug = :item_slug')
            ->setParameter('item_slug', $item_slug)
            ->orderBy('c.date', 'desc')
            ->getQuery()
        ;

        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function callbackFindByItemSlug($item_slug)
    {
        return function($qb) use($item_slug) {
            $qb
                ->join('Commande.item', 'i', 'WITH', 'i.slug = :item_slug')
                ->setParameter('item_slug', $item_slug)
            ;
        };
    }
}
