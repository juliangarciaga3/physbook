<?php

namespace PJM\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * TransactionRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TransactionRepository extends EntityRepository
{
    public function findByUserAndBoquetteSlug(User $user, $boquetteSlug, $limit = null, $status = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->where('t.status = \'OK\'')
            ->join('t.compte', 'c', 'WITH', 'c.user = :user')
            ->join('c.boquette', 'b', 'WITH', 'b.slug = :boquette_slug')
            ->setParameter('user', $user)
            ->setParameter('boquette_slug', $boquetteSlug)
            ->orderBy('t.date', 'desc')
        ;

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($status !== null) {
            $qb
                ->andWhere('t.status = :status')
                ->setParameter('status', $status)
            ;
        }

        return $qb->getQuery()->getResult();
    }

    public function buildFindByBoquette(Boquette $boquette, $limit = null, $status = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->join('t.compte', 'c', 'WITH', 'c.boquette = :boquette')
            ->setParameter('boquette', $boquette)
            ->orderBy('t.date', 'desc')
        ;

        if ($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if ($status !== null) {
            if ($status == 'notNull') {
                $qb->andWhere('t.status IS NOT NULL');
            } else {
                $qb
                    ->andWhere('t.status = :status')
                    ->setParameter('status', $status)
                ;
            }
        }

        return $qb;
    }

    public function findByCompteAndValid(Compte $compte, $valid = 'OK')
    {
        $status = $valid ? 'OK' : 'NOK';
        $query = $this->createQueryBuilder('t')
                    ->select('SUM(t.montant) AS somme')
                    ->where('t.status = :status')
                    ->andWhere('t.compte = :compte')
                    ->setParameters(array(
                        'compte' => $compte,
                        'status' => $status,
                    ))
                    ->orderBy('t.date', 'desc')
                    ->getQuery();

        try {
            $res = $query->getSingleScalarResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function callbackFindByBoquetteSlug($boquette_slug)
    {
        return function (QueryBuilder $qb) use ($boquette_slug) {
            $qb
                ->andWhere('transaction.status IS NOT NULL')
                ->join('transaction.compte', 'c')
                ->join('c.boquette', 'b', 'WITH', 'b.slug = :boquette_slug')
                ->setParameter('boquette_slug', $boquette_slug)
            ;
        };
    }

    public function callbackFindByUser(User $user)
    {
        return function (QueryBuilder $qb) use ($user) {
            $qb
                ->andWhere('transaction.status IS NOT NULL')
                ->join('transaction.compte', 'c')
                ->join('c.user', 'u', 'WITH', 'u = :user')
                ->setParameter('user', $user)
            ;
        };
    }
}
