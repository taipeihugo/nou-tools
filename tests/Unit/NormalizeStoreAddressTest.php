<?php

use App\Models\DiscountStore;
use NouTools\Domains\DiscountStores\Actions\NormalizeStoreAddress;

it('normalizes store address for geocoding query', function (string $city, string $district, string $address, string $expected) {
    $store = new DiscountStore;
    $store->city = $city;
    $store->district = $district;
    $store->address = $address;

    $normalized = app(NormalizeStoreAddress::class)($store);

    expect($normalized)->toBe($expected);
})->with([
    'full city/district prefix and floor info' => [
        '台北市',
        '中正區',
        '台北市中正區中山路1巷2弄3號4樓之5',
        '中山路1巷2弄 3',
    ],
    'without city and district prefix' => [
        '台北市',
        '中正區',
        '中山路1巷2弄3號4樓之5',
        '中山路1巷2弄 3',
    ],
    'normalize tai to tai variant and keep section' => [
        '臺北市',
        '士林區',
        '臺北市士林區至善路二段221號',
        '至善路二段 221',
    ],
    'returns original address when pattern does not match' => [
        '台北市',
        '大同區',
        '承德路',
        '承德路',
    ],
    'strip building' => [
        '臺中市',
        '北區',
        '臺中市北區三民路三段161號C棟B3樓',
        '三民路三段 161',
    ],
]);
