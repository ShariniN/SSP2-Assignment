@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="container mx-auto py-6">
        <h1 class="text-2xl font-bold mb-4">Your Shopping Cart</h1>

        {{-- Livewire component --}}
        <livewire:cart-component />
    </div>
@endsection