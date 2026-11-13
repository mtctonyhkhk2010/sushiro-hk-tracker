<?php

use App\Models\Store;

it('renders Hong Kong store names when the app locale has no translation', function () {
    app()->setLocale('en');
    config(['app.fallback_locale' => 'en']);

    $store = new Store(['name' => ['zh_HK' => '上環店']]);
    $store->id = 1;

    expect($store->name)->toBe('上環店');

    $this->blade('@include("livewire.store-card", ["store" => $store])', ['store' => $store])
        ->assertSee('上環店');
});

it('prefers the requested translation when one exists', function () {
    app()->setLocale('en');

    $store = new Store(['name' => ['zh_HK' => '上環店', 'en' => 'Sheung Wan']]);

    expect($store->name)->toBe('Sheung Wan');
});
