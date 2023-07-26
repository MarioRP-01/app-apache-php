<?php

namespace Application\Traits;
use Laminas\Db\Adapter\Driver\ResultInterface;

trait QueryPaginationTrait
{

    abstract private function getAdapter();

    /**
     * Add pagination to a query. The pagination will not be consistent if there 
     * is not an ORDER BY clause.
     */
    public function executePagedQuery(
        string $sql, 
        array $parameters = [], 
        int $start = 0, 
        int $limit = 9,

    ) : ResultInterface {

        $adapter = $this->getAdapter();

        $sql .= "
            OFFSET $start
            FETCH NEXT $limit ROWS ONLY
        ";

        $statement = $adapter->createStatement($sql);
        $result = $statement->execute($parameters);
        return $result;
    }
}

