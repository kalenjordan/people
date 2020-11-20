<?php
/** @var \App\User $user */
/** @var \App\Person $person */
?>

@extends('_landing')

@section('title')
    <title>
        {{ $person->name() }} | {{ Util::appName() }}
    </title>
@endsection

@section('content')
    <div class="pb-20 mx-auto max-w-2xl px-4 sm:px-6">
        <div class="text-center">
            <img class="inline h-24 w-24 rounded-full mt-16 mb-8"
                 src="{{ $person->avatar() }}" alt=""/>
            <h2 class="text-3xl sm:text-3xl  md:text-4xl tracking-tight leading-10 font-extrabold text-gray-900  sm:leading-none">
                {{ $person->name() }}
            </h2>
        </div>
    </div>
@endsection
