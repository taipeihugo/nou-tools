<?php

namespace NouTools\Domains\DiscountStores\ViewModels;

use App\Models\DiscountStoreCategory;
use Illuminate\Support\Collection;

final readonly class DiscountStoreIndexPageViewModel
{
    /**
     * @param  Collection<int, DiscountStoreCategory>  $categories
     * @param  Collection<int, string>  $cities
     */
    public function __construct(
        public Collection $stores,
        public Collection $categories,
        public Collection $cities,
        public ?int $selectedCategoryId,
        public ?string $selectedType,
        public ?string $search,
        public ?string $selectedCity,
    ) {}
}
