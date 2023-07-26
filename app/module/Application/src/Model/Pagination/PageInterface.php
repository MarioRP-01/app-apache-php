<?php

namespace Application\Model\Pagination;

use Application\Model\Dto\Rest\DTORESTInterface;

/**
 * Interface for paginated results.
 * @link Based o this api https://developer.atlassian.com/server/confluence/pagination-in-the-rest-api/
 */
interface PageInterface extends \ArrayAccess {

    /** @return array<DTORESTInterface> */
    public function getResults(): array;

    /** @return int Max number of elements of the page */
    public function getLimit(): int;

    /** @return int Position of the first element. Starts in 0. */
    public function getStart(): int;

    /** @return int Number of results in the call */
    public function getSize(): int;

    /** @return array<string, string> */
    public function get_links(): array;
}