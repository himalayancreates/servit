@extends('portal.layouts.app')

@section('title', $title)

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
</div>

<div class="bg-white rounded-xl border border-gray-200 px-8 py-12 text-center">
    <div class="w-12 h-12 rounded-full bg-orange-50 flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    </div>
    <p class="text-gray-600 text-sm">{{ $message }}</p>
</div>
@endsection
