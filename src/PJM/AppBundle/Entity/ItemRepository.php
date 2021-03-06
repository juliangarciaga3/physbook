<?php

namespace PJM\AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * ItemRepository.
 */
class ItemRepository extends EntityRepository
{
    public function findBySlugRegex($item_slug_regex)
    {
        $query = $this->createQueryBuilder('i')

                    ->where('REGEXP(i.slug, :item_slug_regex) = 1')
                    ->setParameters(array(
                        'item_slug_regex' => $item_slug_regex,
                    ))
                    ->orderBy('i.date', 'desc')
                    ->getQuery();

        try {
            $res = $query->getResult();
        } catch (\Doctrine\Orm\NoResultException $e) {
            $res = null;
        }

        return $res;
    }

    public function getLastItem($slug, $valid = true)
    {
        $res = ($valid === 'any') ?
            $this->findBy(array('slug' => $slug), array('date' => 'DESC'), 1) :
            $this->findBy(array('slug' => $slug, 'valid' => $valid), array('date' => 'DESC'), 1);

        if (empty($res)) {
            return;
        }

        return $res[0];
    }

    public function callbackFindBySlug($slug)
    {
        return function (QueryBuilder $qb) use ($slug) {
            $qb
                ->andWhere('item.slug = :slug')
                ->setParameter('slug', $slug)
            ;
        };
    }

    public function findByBoquetteSlug($boquette_slug, $valid = null, $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->join('i.boquette', 'b', 'WITH', 'b.slug = :boquette_slug')
            ->setParameter('boquette_slug', $boquette_slug)
            ->orderBy('i.date', 'desc')
        ;

        if (isset($valid)) {
            $qb
                ->andWhere('i.valid = :valid')
                ->setParameter('valid', $valid)
            ;
        }

        if (isset($limit)) {
            $qb
                ->setMaxResults($limit)
            ;
        }

        if (isset($offset)) {
            $qb
                ->setFirstResult($offset)
            ;
        }

        $query = $qb->getQuery();

        return $query->getResult();
    }

    public function callbackFindByBoquetteSlug($boquette_slug)
    {
        return function (QueryBuilder $qb) use ($boquette_slug) {
            $qb
                ->join('item.boquette', 'b', 'WITH', 'b.slug = :boquette_slug')
                ->setParameter('boquette_slug', $boquette_slug)
            ;
        };
    }
}
