<?php
/** @var \App\User $user */
/** @var \App\SavedSearch $savedSearch */
/** @var \App\Person $person */
?>

@extends('_landing')

@section('title')
    <title>
        {{ $savedSearch->name() }} | {{ Util::appName() }}
    </title>
@endsection

@section('content')
    <div class="mx-auto max-w-2xl px-4 sm:px-6 mt-6 mb-12">
        <div class="text-center">
            <h2 class="text-3xl sm:text-3xl md:text-4xl tracking-tight leading-10 font-extrabold text-gray-900  sm:leading-none">
                {{ $savedSearch->name() }}
            </h2>
        </div>
    </div>

    <!-- This example requires Tailwind CSS v2.0+ -->
    <ul class="grid grid-cols-1 gap-12 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 mx-24 mb-12">
        @foreach ($people as $person )
            <li class="col-span-1 flex flex-col text-center bg-white rounded-lg shadow divide-y divide-gray-200 hover:scale-105 transform duration-150">
                <a href="{{ $person->url() }}">
                    <div class="flex-1 flex flex-col p-4">
                        <img class="w-32 h-32 flex-shrink-0 mx-auto bg-black rounded-full"
                             src="{{ $person->avatar() }}"
                             alt="">
                        <h3 class="mt-6 text-gray-900 text-lg font-medium">
                            {{ $person->name() }}
                        </h3>
                        <dl class="mt-1">
                            <dd class="mt-3 -ml-1 -mr-1">
                                @foreach ($person->publicTagNames() as $tag)
                                    <span
                                        class="inline-block m-1 px-2 py-1 text-gray-800 text-xs font-medium bg-gray-100 rounded-full">{{ $tag }}</span>
                                @endforeach
                            </dd>
                        </dl>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>

@endsection
