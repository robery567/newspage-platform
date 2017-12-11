<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class UserRepository extends EntityRepository
{
    public function createQuery($dql = '')
    {
        return $this->getEntityManager()->createQuery($dql)->useQueryCache(true);
    }

    public function findAllRedactors()
    {
        $query = $this->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE
                u.roles NOT LIKE :roles
                AND u.position != \'Sysop\'
                AND u.position != \'Robot\'
        ')->setParameter('roles', '%ROLE_USER%');

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findAllCountedRedactors()
    {
        $query = $this->createQuery('
            SELECT COUNT(u.id)
            FROM AppBundle:User u
            WHERE
                u.roles LIKE :roles
        ')->setParameter('roles', '%ROLE_REDACTOR%');

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findAllPaginated($page = 1)
    {
        $query = $this->createQuery('
            SELECT u
            FROM AppBundle:User u
        ');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findTeamPaginated($page)
    {
        $query = $this->createQuery('
            SELECT u
            FROM AppBundle:User u
            WHERE u.roles LIKE :roles
        ')->setParameter('roles', '%ROLE_REDACTOR%');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }
}