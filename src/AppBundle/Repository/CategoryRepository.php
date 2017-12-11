<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class CategoryRepository extends EntityRepository
{
    public function createQuery($dql = '')
    {
        return $this->getEntityManager()->createQuery($dql)->useQueryCache(true);
    }

    public function findAll()
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            ORDER BY
                c.position ASC,
                c.name ASC
        ');

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findCategoriesIn(array $in)
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE c.isActive = :active
            AND c.name IN (:categories)
        ')->setParameter('active', true)->setParameter('categories', $in);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findOneSubcategory($slug)
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE c.slug = :slug
            AND c.isActive = :active
        ')->setParameter('slug', $slug)->setParameter('active', true);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        } catch (NonUniqueResultException $e) {
            return [];
        }
    }

    public function findOnePublic()
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE c.isActive = :active
        ')->setParameter('active', true);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        } catch (NonUniqueResultException $e) {
            return [];
        }
    }

    public function findAllActive()
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE c.isActive = :active
            ORDER BY
                c.position ASC,
                c.name ASC
        ');

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findOneById($id)
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE
                c.isActive = :active
                AND c.id = :id
            ORDER BY
                c.position ASC,
                c.name ASC
        ')->setParameter('active', true)->setParameter('id', $id);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        } catch (NonUniqueResultException $e) {
            return [];
        }
    }

    public function findAllParents()
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE c.parent IS NULL
            ORDER BY
                c.position ASC,
                c.name ASC
        ');

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllChildren($id)
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE
                c.isActive = :active
                AND c.parent = :parent
            ORDER BY
                c.position ASC,
                c.name ASC
        ')->setParameter('active', true)->setParameter('parent', $id);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllRetardedChildren($id)
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE
                c.isActive = :active
                AND c.parent = :parent
            ORDER BY
                c.position ASC,
                c.name ASC
        ')->setParameter('active', true)->setParameter('parent', $id)->setMaxResults(9);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllSubcategories()
    {
        $query = $this->createQuery('
            SELECT c
            FROM AppBundle:Category c
            WHERE
                c.parent IS NOT NULL
                AND c.isActive = :active
            ORDER BY
                c.position ASC
        ')->setParameter('active', true);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllCountedActive()
    {
        $q = $this->createQuery('
            SELECT COUNT(c.id) AS counted
            FROM AppBundle:Category c
            WHERE
                c.parent IS NOT NULL
                AND c.isActive = :active
        ')->setParameter('active', true);

        try {
            return $q->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findOneArticleInSubcategories()
    {
        $articles = [];
        $sections = $this->findAllSubcategories();

        foreach ($sections as $section) {
            $query = $this->createQuery('
              SELECT a, c, u
                FROM AppBundle:Article a 
                LEFT JOIN a.category c 
                LEFT JOIN a.author u 
                WHERE
                    c.id = :category
                ORDER BY
                    a.updatedAt DESC,
                    a.addedAt DESC
            ')->setParameter('category', $section->getId())->setMaxResults(1);

            if (!is_null($result = $query->getResult())) {
                if (isset ($result[0])) {
                    $articles[] = $result[0];
                }
            }
        }

        return $articles;
    }

    public function findCategoryId($slug)
    {
        $query = $this->createQuery('SELECT c.id FROM AppBundle:Category c WHERE c.slug = :slug')->setParameter('slug', $slug);

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }
}
