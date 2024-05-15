<?php

namespace App\Livewire;

use App\Models\PushSubscription;
use App\Models\Store;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ShowStore extends Component
{
    public Store $store;
    public bool $queueModal = false;

    public function render()
    {
        return view('livewire.show-store');
    }

    public function like()
    {
        $liked_stores = session('liked_stores', []);
        $liked_stores[] = $this->store->id;
        session(['liked_stores' => array_unique($liked_stores)]);
    }

    public function unlike()
    {
        $liked_stores = session('liked_stores', []);
        $liked_stores = array_filter($liked_stores, function ($value) {
            return $value !== $this->store->id;
        });
        session(['liked_stores' => $liked_stores]);
    }

    #[Computed]
    public function pushSubscription()
    {
        return PushSubscription::where('session_id', session()->getId())->where('done', false)->first();
    }

    public function cancelSubscription()
    {
        return PushSubscription::where('session_id', session()->getId())->delete();
    }
}
