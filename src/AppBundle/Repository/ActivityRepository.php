<?php
/**
 * ActivityRepository File Doc Comment
 *
 * PHP version 7.2
 *
 * @category ActivityRepository
 * @package  Repository
 * @author   WildCodeSchool <contact@wildcodeschool.fr>
 */
namespace AppBundle\Repository;

use \Doctrine\ORM\EntityRepository;

/**
 * ActivityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 *
 * @category ActivityRepository
 * @package  Repository
 * @author   WildCodeSchool <contact@wildcodeschool.fr>
 */
class ActivityRepository extends EntityRepository
{
    /**
     * Search activities according to user's search
     *
     * @param String    $name        Activity name
     * @param String    $type        Activity Type
     * @param \DateTime $date        Ending date
     * @param Int       $amountStart Amount searching range
     * @param Int       $amountEnd   Amount searching range
     *
     * @return mixed
     */
    public function search(string $name, string $type, \DateTime $date,
        int $amountStart, int $amountEnd
    ) {
    
        if (!$name && $type == "0") {
            return $this->_searchAll($date, $amountStart, $amountEnd);
        }

        return $this->_searchByName($name, $type, $date, $amountStart, $amountEnd);
    }

    /**
     * Search activities according to user's search without name
     *
     * @param \DateTime $date        Ending date
     * @param int       $amountStart Amount searching range
     * @param int       $amountEnd   Amount searching range
     *
     * @return mixed
     */
    private function _searchAll(\DateTime $date, int $amountStart, int $amountEnd)
    {
        $query = $this->createQueryBuilder('act')
            ->INNERJOIN('act.organizationActivities', 'org')
            ->INNERJOIN('act.activities', 'off')
            ->SELECT('act, org, off')
            ->WHERE('off.date >= :date')
            ->ANDWHERE('off.amount BETWEEN :amountStart AND :amountEnd')
            ->setParameter(':amountStart', $amountStart)
            ->setParameter(':amountEnd', $amountEnd)
            ->setParameter('date', $date)
            ->GROUPBY('off')
            ->ORDERBY('act.creationDate');

        return $query->getQuery()->getResult();
    }

    /**
     * Search activities according to user's search with a name
     *
     * @param String    $name        Activity name
     * @param String    $type        Activity Type
     * @param \DateTime $date        Ending date
     * @param int       $amountStart Amount searching range
     * @param int       $amountEnd   Amount searching range
     *
     * @return mixed
     */
    private function _searchByName(string $name, string $type, \DateTime $date,
        int $amountStart, int $amountEnd
    ) {
        $query = $this->createQueryBuilder('act')
            ->INNERJOIN('act.organizationActivities', 'org')
            ->INNERJOIN('act.activities', 'off')
            ->SELECT('act, org')
            ->WHERE('act.name LIKE :name')
            ->ORWHERE('org.name LIKE :name')
            ->setParameter('name', '%' . $name . '%');
        if ($type == "Activité de streaming"
            || $type == "Equipe eSport"
            || $type == "Évènement eSport"
        ) {
            $query
                ->ANDWHERE('act.type LIKE :type')
                ->setParameter('type', '%' . $type . '%');
        }
        $query
            ->ANDWHERE('off.date >= :date')
            ->ANDWHERE('off.amount BETWEEN :amountStart AND :amountEnd')
            ->setParameter('date', $date)
            ->setParameter(':amountStart', $amountStart)
            ->setParameter(':amountEnd', $amountEnd)
            ->GROUPBY('off')
            ->ORDERBY('act.creationDate');

        return $query->getQuery()->getResult();
    }
}
