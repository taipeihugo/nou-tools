<?php

namespace NouTools\Domains\DiscountStores\DataTransferObjects;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

final class SubmitDiscountStoreDTO extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $name,
        #[Required]
        public string $type,
        #[Required]
        public int $category_id,
        #[Max(50)]
        public ?string $city = null,
        #[Max(50)]
        public ?string $district = null,
        #[Max(500)]
        public string $address = '',
        #[Max(255)]
        public string $verification_method = '',
        #[Required]
        public string $discount_details = '',
        public ?string $notes = null,
    ) {}
}
