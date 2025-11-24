@extends('layouts.app')
@section('page.title','Activity')
@section('content')
  <div class="space-y-4">
    @foreach([
      ['Cilok, Risol','12 Maret 2025, 09:25','2 item','Rp 7.000','Rp 10.000','https://images.unsplash.com/photo-1625940525064-0185d5cfa3fe'],
      ['Air Mineral','10 Maret 2025, 14:09','1 item','Rp 3.000',null,'https://images.unsplash.com/photo-1547721064-da6cfb341d50'],
    ] as $o)
      <div class="rounded-2xl bg-gray-200 p-4 flex gap-4 items-center">
        <img src="{{ $o[5] }}" class="w-16 h-16 rounded-xl object-cover" alt="">
        <div class="flex-1">
          <div class="font-semibold text-lg">{{ $o[0] }}</div>
          <div class="text-xs text-gray-600">{{ $o[1] }}</div>
          <div class="mt-2 flex items-center gap-2">
            <span class="badge">Selesai</span>
            <button class="btn btn-light" data-ripple>Ulasan</button>
          </div>
        </div>
        <div class="text-right">
          <div class="badge">{{ $o[2] }}</div>
          <div class="text-sm mt-2">{{ $o[3] }}</div>
          @if($o[4]) <div class="text-sm">{{ $o[4] }}</div> @endif
          <button class="btn btn-light mt-2" data-ripple>Beli Lagi</button>
        </div>
      </div>
    @endforeach
  </div>
@endsection
