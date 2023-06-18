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
        int $page = 1, 
        int $page_size = 10,

    ) : ResultInterface {

        $adapter = $this->getAdapter();
        $offset = ($page - 1) * $page_size;

        $sql .= "
            OFFSET $offset
            FETCH NEXT $page_size ROWS ONLY
        ";

        $statement = $adapter->createStatement($sql);
        $result = $statement->execute($parameters);
        return $result;
    }
}

