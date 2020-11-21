<?php
/** @var \App\User $user */
/** @var \App\SavedSearch $savedSearch */
?>

@extends('_landing')

@section('title')
    <title>
        people.link | {{ Util::appName() }}
    </title>
@endsection

@section('content')
    <div id="talent" class="py-12 px-4 sm:px-6 lg:px-8">
        <div class="relative max-w-7xl mx-auto">
            <div class="mt-12 grid gap-12 max-w-lg mx-auto lg:grid-cols-3 lg:max-w-none">
                @foreach ($savedSearches as $savedSearch)
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden hover:scale-105 transform duration-150">
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <a href="{{ $savedSearch->url() }}">
                                <div class="flex-1">
                                    <h3 class="mt-2 text-xl leading-7 font-semibold text-gray-900">
                                        {{ $savedSearch->name() }}
                                    </h3>
                                    @if ($savedSearch->description())
                                        <p class="mt-3 text-base leading-6 text-gray-500">
                                            {{ $savedSearch->description() }}
                                        </p>
                                    @endif
                                    <div class="mt-3 flex relative z-0 overflow-hidden">
                                        @foreach ($savedSearch->avatars() as $avatar)
                                            <img
                                                class="relative z-30 inline-block h-14 w-14 rounded-full text-white shadow-solid"
                                                src="{{ $avatar }}"
                                                alt=""/>
                                        @endforeach
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
