<?php
namespace AppBundle\Repository;

use AppBundle\Entity\Article;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class ArticleRepository extends EntityRepository
{
    private function createQuery($dql = '')
    {
        return $this->getEntityManager()->createQuery($dql)->useQueryCache(true);
    }

    public function findAll()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            JOIN a.category c
            JOIN a.author u
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setMaxResults(Article::MAX_PAGE_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllPaginated($page = 1)
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a 
            LEFT JOIN a.category c 
            LEFT JOIN a.author u
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ');

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllCountedArticles()
    {
        $query = $this->createQuery('
            SELECT COUNT(a.id) AS articles_count
            FROM AppBundle:Article a
        ');

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findAllCountedArticlesByYear()
    {
        $query = $this->createQuery("
            SELECT
                CONCAT('anul ', YEAR(a.addedAt)) AS label,
                COUNT(a.id) AS value
            FROM AppBundle:Article a
            GROUP BY label
        ");

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findByCurrentCountedArticles()
    {
        $query = $this->createQuery('
            SELECT COUNT(a.id) AS article_number
            FROM AppBundle:Article a
            WHERE YEAR(a.addedAt) = :currentYear
        ')->setParameter('currentYear', date('Y'));

        try {
            return $query->getSingleScalarResult();
        } catch (NoResultException $e) {
            return 0;
        }
    }

    public function findOneBySlugIn($category, $article)
    {
        $query = $this->createQuery('
            SELECT a, c, u, t
            FROM AppBundle:Article a
            LEFT JOIN a.author u
            LEFT JOIN a.category c
            LEFT JOIN a.tags t
            WHERE
                c.slug = :category
                AND a.slug = :article
                AND c.isActive = :active
        ')->setParameter('category', $category)->setParameter('article', $article)->setParameter('active', true);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllLatest()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            LEFT JOIN a.author u
            LEFT JOIN a.category c
            WHERE
                c.isActive = :active
                AND a.type = :normal
                AND a.updatedAt <= :now
                AND a.addedAt <= :now
                AND c.name != \'ANUNTURI\'
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('active', true)
            ->setParameter('normal', Article::TYPE_NORM)
            ->setParameter('now', new \DateTime())
            ->setMaxResults(Article::MAX_RECENT_ARTICLES);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllVideo($page = 1)
    {
        $query = $this->createQuery('
            SELECT a, u, c
            FROM AppBundle:Article a
            LEFT JOIN a.author u
            LEFT JOIN a.category c
            WHERE a.type = :video
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('video', Article::TYPE_VIDEO)->setMaxResults(Article::MAX_VIDEO_ARTICLE);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllHot($page = 1)
    {
        $query = $this->createQuery('
            SELECT a, u, c
            FROM AppBundle:Article a
            LEFT JOIN a.author u 
            LEFT JOIN a.category c 
            WHERE a.type = :hot
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('hot', Article::TYPE_HOT)->setMaxResults(Article::MAX_HOT_ARTICLE);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllRecommended()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            JOIN a.category c
            JOIN a.author u
            WHERE a.type = :recommended
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('recommended', Article::TYPE_REC)->setMaxResults(Article::MAX_REC_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findNewRecommended()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            JOIN a.category c
            JOIN a.author u
            WHERE a.type = :recommended
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('recommended', Article::TYPE_REC)->setMaxResults(6);

        try {
            $data = [];
            $result = $query->getResult();

            for ($i = 0; $i < count($result); $i++) {
                if ($i % 2 == 0) {
                    $column = 0;
                } else {
                    $column = 1;
                }

                $data[$column][] = $result[$i];
            }

            return $data;
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findOneById($id)
    {
        $query = $this->createQuery('SELECT a.media, a.mediaType FROM AppBundle:Article a WHERE a.id = :id')->setParameter('id', $id);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllBySimilarTags($tags)
    {
        $query = $this->createQuery('
            SELECT a, c, u, t
            FROM AppBundle:Article a
            LEFT JOIN a.category c
            LEFT JOIN a.author u
            LEFT JOIN a.tags t
            WHERE t.name IN (:tags)
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('tags', $tags)->setMaxResults(Article::MAX_PAGE_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllInCategory($categoryName)
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            LEFT JOIN a.category c
            LEFT JOIN a.author u
            WHERE c.slug = :categoryName
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('categoryName', $categoryName)->setMaxResults(Article::MAX_PAGE_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllInCategoryWithParent($category, $page = 1)
    {
        $query = $this->createQuery('
            SElECT a, c, u
            FROM AppBundle:Article a 
            LEFT JOIN a.category c 
            LEFT JOIN a.author u
            WHERE
              c.parent = :parent
            ORDER BY
              a.updatedAt DESC,
              a.addedAt DESC
        ')->setParameter('parent', $category);

        $paginator = new Pagerfanta(new DoctrineORMAdapter($query));
        $paginator->setMaxPerPage(30);
        $paginator->setCurrentPage($page);

        return $paginator;
    }

    public function findAllMostViewed()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            LEFT JOIN a.category c
            LEFT JOIN a.author u 
            WHERE
                a.views > 0
                AND a.addedAt >= :date
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC,
                a.views DESC
        ')->setParameter('date', new \DateTime('last week'))->setMaxResults(Article::MAX_MOST_VIEWED_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findOneInSubcategory($category)
    {
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
        ')->setParameter('category', $category)->setMaxResults(1);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findAllRssFeed()
    {
        $query = $this->createQuery('
            SELECT a
            FROM AppBundle:Article a
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setMaxResults(Article::MAX_RSS_FEED_ARTICLE);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAllRssFeedInCategory($category)
    {
        $query = $this->createQuery('
            SELECT a, c
            FROM AppBundle:Article a
            JOIN a.category c
            WHERE c.slug = :category
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setMaxResults(Article::MAX_RSS_FEED_ARTICLE)->setParameter('category', $category);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAIndependent()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            JOIN a.category c
            JOIN a.author u
            WHERE a.type = :recommended
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('recommended', Article::TYPE_HOT)->setMaxResults(2);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findAThree()
    {
        $query = $this->createQuery('
            SELECT a, c, u
            FROM AppBundle:Article a
            JOIN a.category c
            JOIN a.author u
            WHERE a.type = :type
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('type', Article::TYPE_AD)->setMaxResults(3);

        try {
            return $query->getResult();
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findVideos()
    {
        $query = $this->createQuery('
            SELECT a, u, c
            FROM AppBundle:Article a
            LEFT JOIN a.author u
            LEFT JOIN a.category c
            WHERE a.type = :video
            ORDER BY
                a.updatedAt DESC,
                a.addedAt DESC
        ')->setParameter('video', Article::TYPE_VIDEO)->setMaxResults(Article::MAX_VIDEO_ARTICLE);

        try {
            $results = $query->getResult();
            $first = $results[0];
            unset($results[0]);
            return [
                'first' => $first,
                'rest' => array_values($results),
            ];
        } catch (NoResultException $e) {
            return [];
        }
    }

    public function findVideoBy(string $videoId)
    {
        $query = $this->createQuery('
            SELECT a, u, c
            FROM AppBundle:Article a
            LEFT JOIN a.author u
            LEFT JOIN a.category c
            WHERE a.type = :video
              AND a.articleId = :videoId
        ')->setParameters([
            'video' => Article::TYPE_VIDEO,
            'videoId' => $videoId,
        ]);

        try {
            return $query->getSingleResult();
        } catch (NoResultException $e) {
            return [];
        }
    }
}
