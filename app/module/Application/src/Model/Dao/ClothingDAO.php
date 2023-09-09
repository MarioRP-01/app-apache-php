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

        $sql = "
            SELECT c.*, s.name AS size, g.name AS gender
            FROM suse_clothing c
                INNER JOIN suse_size s ON c.size_id = s.id
                INNER JOIN suse_gender g ON c.gender_id = g.id
            WHERE c.uuid = :uuid
        ";

        $params = [
            'uuid' => $uuid
        ];

        $statement = $this->adapter->createStatement($sql, $params);
        $result = $statement->execute();
        return $result->current();
    }

    public function getAllClothingPaged(
        int $start, 
        int $limit
    ) : ResultInterface {
        
        $sql = "
        SELECT c.*, s.name AS size, g.name AS gender
        FROM suse_clothing c
            INNER JOIN suse_size s ON c.size_id = s.id
            INNER JOIN suse_gender g ON c.gender_id = g.id
        ";

        return $this->executePagedQuery(
            $sql,
            [],
            $start,
            $limit
        );
    }
}
