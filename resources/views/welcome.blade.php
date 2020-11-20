<?php
/** @var \App\User $user */
/** @var \App\User $teamMember */
?>

@extends('_landing')

@section('title')
    <title>
        people.link | {{ Util::appName() }}
    </title>
@endsection

@section('content')
    <div class="my-10 mx-auto max-w-screen-xl px-4 sm:my-12 sm:px-6 md:my-16 lg:my-20 xl:my-28">
        <div class="text-center">
            <h2 class="text-4xl tracking-tight leading-10 font-extrabold text-gray-900 sm:text-5xl sm:leading-none md:text-6xl">
                Do some <br class='lg:hidden'/> <span class='text-indigo-600'>cool stuff</span>
            </h2>
        </div>
    </div>
@endsection
