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
    <person inline-template
            :person='{!! \App\Util::json($person->toData()) !!}'>
        <div class="pb-20 mx-auto max-w-2xl px-4 sm:px-6 mt-6">
            <div class="text-center">
                <img class="inline h-32 w-32 rounded-full mb-8"
                     src="{{ $person->avatar() }}" alt=""/>
                <h2 class="mb-8 text-3xl sm:text-3xl md:text-4xl tracking-tight leading-10 font-extrabold text-gray-900  sm:leading-none">
                    {{ $person->name() }}
                </h2>
                <div>
                    <span v-for="tag in public_tags"
                          class="inline-flex items-center px-4 py-1 rounded-full font-medium bg-gray-100 text-gray-800 text-lg m-1">
                        @{{ tag }}
                    </span>
                    @if ($user)
                        <a href="javascript://"
                            v-shortkey="['t']" @shortkey="togglePublicTag"
                            @click="togglePublicTag"
                            class="inline-flex items-center px-4 py-1 rounded-full font-medium bg-gray-100 text-gray-800 text-lg -ml-1 m-1">
                            @include('svg.icon-plus', ['classes' => 'h-3 w-3 inline mr-1 text-gray-800'])
                            New Tag
                        </a>
                    @endif
                </div>
            </div>

            @if ($user)
                <div v-if="isAddingPublicTag" class="add-public-tag max-w-sm mx-auto mt-8">
                    <public-tag-select
                        :person='{!! \App\Util::json($person->toData()) !!}'
                        :api-key="'{{ $user->apiKey() }}'" :api-url="'/api/hero-action'">
                    </public-tag-select>
                </div>
            @endif
        </div>
    </person>
@endsection
