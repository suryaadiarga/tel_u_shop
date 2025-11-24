@extends('layouts.app')
@section('title', 'Cart')
@section('content')
    <section class="section">
        @if(!$items->count())
            <x-empty icon="👜" text="No item in your cart" />
        @else
            {{-- daftar item cart --}}
        @endif
    </section>
@endsection