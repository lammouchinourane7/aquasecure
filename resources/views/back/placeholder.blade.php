@extends('layouts.back')

@section('title', $title)

@section('content')
    <div class="bg-white border border-border rounded-xl">
        <x-empty-state :icon="$icon" :title="$title" />
    </div>
@endsection
