<?php

namespace NouTools\Domains\DiscountStores\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class ReportDiscountStoreDTO extends Data
{
    public function __construct(
        #[Required]
        public bool $is_valid,
        public ?string $comment = null,
    ) {}
}
