<?php

namespace Application\Model\Pagination;

use Application\Model\Dto\Rest\DTORESTInterface;

/**
 * @template-covariant T of DTORESTInterface
 */
class Page implements PageInterface {

    /**
     * @param array<T> $results
     * @param int $limit
     * @param int $start
     * @param int $size
     * @param array<string, string> $_links
     * @return self
     */
    public function __construct(
        readonly array $results, 
        readonly int $limit,
        readonly int $start, 
        readonly int $size, 
        readonly array $_links
    ) {

    }

    public function getResults(): array {
        return $this->results;
    }

    public function getLimit(): int {
        return $this->limit;
    }

    public function getStart(): int {
        return $this->start;
    }

    function getSize(): int {
        return $this->size;
    }

    public function get_links(): array {
        return $this->_links;
    }

    public function offsetExists(mixed $offset):bool {
        return isset($this->results[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->results[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        $this->results[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void {
        unset($this->results[$offset]);
    }

}