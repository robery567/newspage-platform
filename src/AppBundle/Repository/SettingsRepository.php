<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class SettingsRepository extends EntityRepository
{
    public function createQuery($dql = '')
    {
        return $this->getEntityManager()->createQuery($dql)->useQueryCache(true);
    }

    public function findAllPaginated($page = 1)
    {
        $query = $this->createQuery('
            SELECT s
            FROM AppBundle:Settings s
            ORDER BY s.name ASC
        ');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllSettings()
    {
        $query = $this->createQuery('
            SELECT s
            FROM AppBundle:Settings s
            ORDER BY s.name ASC
        ');

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }
}
