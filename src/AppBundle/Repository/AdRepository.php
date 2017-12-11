<?php
namespace AppBundle\Repository;

use AppBundle\Util\AdChoiceConverter;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class AdRepository extends EntityRepository
{
    private function createQuery($dql = '')
    {
        return $this->getEntityManager()->createQuery($dql)->useQueryCache(true);
    }

    public function findOneByUuid($uuid)
    {
        $query = $this->createQuery('
            SELECT ad
            FROM AppBundle:Ad ad
            WHERE ad.uuid = :uuid
                AND ad.addedAt <= :today
                AND ad.expiredAt >= :today
        ')->setParameter('uuid', $uuid)->setParameter('today', new \DateTime());

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllPaginated($page = 1)
    {
        $query = $this->createQuery('
            SELECT ad
            FROM AppBundle:Ad ad
        ');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllPaginatedFromUser($user, $page = 1)
    {
        $query = $this->createQuery('
            SELECT ad
            FROM AppBundle:Ad ad
            WHERE ad.advertiser = :user
        ')->setParameter('user', $user);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    /**
     * @deprecated use AdRepository::findByPosition()
     */
    public function findByRandom($limit = null, $position)
    {
        $message = 'Method ' . self::class . '::' . __METHOD__ . ' is deprecated and will be removed ASAP.'
                 . 'Please use ' . self::class . '::findOneRandomByPosition() instead.';
        trigger_error($message, E_USER_DEPRECATED);

        return $this->findByPosition($position);
    }

    public function findByPosition($position)
    {
        $count = AdChoiceConverter::getDisplayCountFor($position);

        $query = $this->createQuery('
            SELECT ad
            FROM AppBundle:Ad ad
            WHERE ad.position = :position
              AND ad.expiredAt >= :today
            ORDER BY RAND()
        ')->setMaxResults($count[$position])->setParameters([
            'position' => $position,
            'today' => new \DateTime(),
        ]);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }
}
