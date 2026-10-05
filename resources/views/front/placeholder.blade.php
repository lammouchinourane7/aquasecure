@extends('layouts.front')

@section('title', $title)

@section('content')
    <div class="max-w-4xl mx-auto px-6 py-16">
        <div class="bg-white border border-border rounded-xl">
            <x-empty-state :icon="$icon" :title="$title" />
        </div>
    </div>
@endsection
