<?php

namespace Application\Model\Dao;

use Application\Model\Clothing;
use Application\Traits\QueryPaginationTrait;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Driver\ResultInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;

class ClothingDAO extends TableGateway {

    use QueryPaginationTrait;

    public function __construct(AdapterInterface $adapter) {
        
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new Clothing());

        parent::__construct('clothing', $adapter, null, $resultSetPrototype);
    }

    public function getClothingById(int $id): ?array {

        $sql = "SELECT * FROM clothing WHERE id = ?";

        $statement = $this->adapter->createStatement($sql);
        $result = $statement->execute([$id]);
        return $result->current();
    }

    public function getAllClothingPaged(
        int $page, int $page_size
    ) : ResultInterface {
        
        $sql = "SELECT * FROM clothing ORDER BY id";
        return $this->executePagedQuery(
            $sql,
            [],
            $page,
            $page_size
        );
    }
}
