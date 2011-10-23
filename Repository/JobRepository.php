<?php

namespace NineThousand\Bundle\NineThousandJobqueueBundle\Repository;

use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    public function findAllByQuery($query = array())
    {
        $result = array();
        $entitiesQb = $this->createQueryBuilder('j');

        // scheduled filter
        if (!$query['scheduled']) {
            $entitiesQb->andWhere('j.schedule is NULL');
        } else {
            $entitiesQb->andWhere('j.schedule is not NULL');
        }

        // status filter
        if (false !== strpos($query['status'], 'f') || (0 === $query['status'])) {
            $entitiesQb
                ->andWhere('j.status = :status')
                ->setParameter('status', 'fail');
        } elseif (false !== strpos($query['status'], 's') || (1 === $query['status'])) {
            $entitiesQb
                ->andWhere('j.status = :status')
                ->setParameter('status', 'success');
        } elseif (false !== strpos($query['status'], 'r')) {
            $entitiesQb
                ->andWhere('j.retry = :retry')
                ->andWhere('j.attempts < j.maxRetries')
                ->setParameter('retry', 1);
        } elseif (false !== strpos($query['status'], 'p')) {
            $entitiesQb
                ->andWhere('j.retry <> :retry')
                ->andWhere('j.active = :active')
                ->setParameter('retry', 1)
                ->setParameter('active', 1);
        }

        if (null !== $query['limit']) {
            $entitiesCountQb = clone $entitiesQb;
            $entitiesCountQb->setParameters($entitiesQb->getParameters());

            $entitiesCountQb
                ->resetDQLPart('select')
                ->select('COUNT(j)');

            $result['totalResults'] = $entitiesCountQb->getQuery()->getSingleScalarResult();
        }

        $entitiesQb
            ->setMaxResults($query['limit'])
            ->setFirstResult($query['offset'])
            ->orderBy('j.lastrunDate ', $query['reverse'] ? 'ASC' : 'DESC');

        $result['entities'] = $entitiesQb->getQuery()->getResult();

        if (!isset($result['totalResults'])) {
            $result['totalResults'] = count($result['entities']);
        }

        return $result;
    }
}
