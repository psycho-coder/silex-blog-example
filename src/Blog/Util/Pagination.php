<?php

namespace Blog\Util;

use Doctrine\DBAL\Query\QueryBuilder;

class Pagination
{
    protected $queryBuilder;
    protected $limit = 30;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Limit the results
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get results on specified page
     * @param  int $pageNumber
     * @return array
     */
    public function getPage($pageNumber)
    {
        $queryBuilder = $this->queryBuilder;

        $queryBuilder->setMaxResults($this->getLimit());
        if ( $pageNumber > 1 ) {
            $queryBuilder->setFirstResult(($pageNumber-1)*$this->getLimit());
        }

        return $queryBuilder->execute()->fetchAll();        
    }

    /**
     * @return int
     */
    public function countPages()
    {
        $rowsNumber  = $this->countRows();
        $pagesCount  = 1;

        if ( $rowsNumber > $this->limit ) {
            $division    = $rowsNumber/$this->limit;
            $pagesCount  = floor($division);
            if ( $pagesCount != $division ) {
                $pagesCount++;
            }
        }

        return $pagesCount;        
    }

    /**
     * @return int
     */
    protected function countRows()
    {
        $queryBuilder = $this->queryBuilder;
        // save a select part, because we gonna change it for a moment
        $selectPart   = $queryBuilder->getQueryPart('select');

        $number = $queryBuilder
            ->select('COUNT(*) as cnt')
            // get rid of limit and offset,
            // it will be recreated in getPage() method
            ->setMaxResults(null)
            ->setFirstResult(null)
            ->execute()
            ->fetch();

        // set select part back
        $queryBuilder->select($selectPart);

        return $number['cnt'];        
    }
}