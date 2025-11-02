@extends('layouts.app')

@section('content')
    <div class="bg-hero text-white">
        <div class="max-w-md mx-auto px-5 pt-6 pb-20 curve-mask">
            <h1 class="text-2xl font-bold">Cart</h1>
        </div>
    </div>

    <div class="max-w-md mx-auto px-5 -mt-10">
        <div
            class="rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800 shadow-soft p-10 text-center">
            <div class="text-5xl mb-2">👜</div>
            <div class="text-neutral-500">No item in your cart</div>
        </div>
    </div>
@endsection