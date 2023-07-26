<?php

namespace Application\Model\Dto\Rest;

interface DTORESTInterface extends \JsonSerializable{

    /** @return array<string, string> */
    public function get_links(): array;

    public function set_links(array $_links): self;

    /** @return array<string, string> */
    public function get_expandable(): array;

    public function set_expandable(array $_expandable): self;

}
