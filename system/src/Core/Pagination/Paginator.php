<?php

namespace Stride\Core\Pagination;

class Paginator
{
    public function __construct(
        public int $total,
        public int $perPage,
        public int $page
    ) {}

    public function offset(): int
    {
        return ($this->page - 1) * $this->perPage;
    }

    public function pages(): int
    {
        return (int) ceil($this->total / $this->perPage);
    }
    
    // Spec includes a helper function `paginate(Paginator $p)`
    // usually in helpers/views, but Paginator object logic is here.
}
