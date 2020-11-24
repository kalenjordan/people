<?php
/** @var \App\User $user */
/** @var \App\Person $person */
?>

@extends('_landing')

@section('title')
    <title>
        {{ $person->name() }} | {{ \App\Util::appName() }}
    </title>
@endsection

@section('content')
    <person inline-template
            :person='{!! \App\Util::json($personData) !!}'>
        <div class="pb-20 mx-auto max-w-2xl px-4 sm:px-6 mt-6">
            <div class="text-center">
                <img class="inline h-32 w-32 rounded-full mb-8"
                     src="{{ $person->avatar() }}" alt=""/>
                <h2 class="mb-8 text-3xl sm:text-3xl md:text-4xl tracking-tight leading-10 font-extrabold text-gray-900  sm:leading-none">
                    {{ $person->name() }}
                </h2>
                <div class="mb-2" v-if="!isAddingPrivateTag">
                    <span v-for="tag in public_tags" :class="{ 'opacity-50' : publicTagsProcessing }"
                          class="inline-flex items-center px-4 py-1 rounded-full font-medium bg-yellow-200 text-yellow-800 text-lg m-1">
                        @{{ tag }}
                    </span>
                    @if ($user)
                        <a href="javascript://" v-if="!isAddingPublicTag" :class="{ 'opacity-50' : publicTagsProcessing }"
                            v-shortkey="['t']" @shortkey="togglePublicTag"
                            @click="togglePublicTag"
                            class="inline-flex items-center px-4 py-1 rounded-full font-medium bg-yellow-200 text-yellow-800 text-lg -ml-1 m-1">
                            @include('svg.icon-plus', ['classes' => 'h-3 w-3 inline mr-1 text-gray-800'])
                            New Tag
                        </a>
                    @endif
                </div>
                <div class="mb-2" v-if="!isAddingPublicTag">
                    <span v-for="tag in private_tags" :class="{ 'opacity-50' : privateTagsProcessing }"
                          class="inline-flex border-4 border-dashed items-center px-4 py-1 rounded-full font-medium bg-gray-100 text-gray-800 text-lg m-1">
                        @{{ tag.name }}
                    </span>
                    @if ($user)
                        <a href="javascript://" v-if="!isAddingPrivateTag" :class="{ 'opacity-50' : privateTagsProcessing }"
                           v-shortkey="['shift', 't']" @shortkey="togglePrivateTag"
                           @click="togglePrivateTag"
                           class="inline-flex items-center border-4 border-dashed px-4 py-1 rounded-full font-medium bg-gray-100 text-gray-800 text-lg -ml-1 m-1">
                            @include('svg.icon-plus', ['classes' => 'h-3 w-3 inline mr-1 text-gray-800'])
                            New Private Tag
                        </a>
                    @endif
                </div>
            </div>

            @if ($user)
                <div v-if="isAddingPublicTag" class="add-public-tag max-w-sm mx-auto mt-8">
                    <tag-select
                        :placeholder="'Add or remove a public tag'"
                        :type="'public'"
                        :person='{!! \App\Util::json($personData) !!}'
                        :api-key="'{{ $user->apiKey() }}'">
                    </tag-select>
                </div>

                <div v-if="isAddingPrivateTag" class="add-private-tag max-w-sm mx-auto mt-8">
                    <tag-select
                        :placeholder="'Add or remove a private tag'"
                        :type="'private'"
                        :person='{!! \App\Util::json($personData) !!}'
                        :api-key="'{{ $user->apiKey() }}'">
                    </tag-select>
                </div>
            @endif
        </div>
    </person>
@endsection
