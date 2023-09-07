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

        parent::__construct('suse_clothing', $adapter, null, $resultSetPrototype);
    }

    public function getClothingById(string $uuid): ?array {

        $sql = "SELECT * FROM suse_clothing WHERE uuid = ?";

        $statement = $this->adapter->createStatement($sql);
        $result = $statement->execute([$uuid]);
        return $result->current();
    }

    public function getAllClothingPaged(
        int $start, 
        int $limit
    ) : ResultInterface {
        
        $sql = "SELECT * FROM suse_clothing ORDER BY uuid";
        return $this->executePagedQuery(
            $sql,
            [],
            $start,
            $limit
        );
    }
}
