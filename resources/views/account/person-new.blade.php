@extends('account._app')

@section('title')
    <title>New Person | {{ env('APP_NAME') }}</title>
@endsection

@section('content')
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:leading-9 sm:truncate">
                        New Person
                    </h2>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-6 lg:px-8">
        <form action="/account/persons/new" method="POST">
            {{ csrf_field() }}
            <div>
                <div>
                    <div class="mt-6 max-w-md mx-auto">

                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium leading-5 text-gray-700">
                                Name or Twitter Handle
                            </label>
                            <div class="mt-1 rounded-md shadow-sm">
                                <input name="name" id="name" type="text"
                                       class="form-input block w-full transition duration-150 ease-in-out sm:text-sm sm:leading-5"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 border-t border-gray-200 pt-5">
                <div class="flex justify-end">
                    <span class="inline-flex rounded-md shadow-sm">
                        <a href="/" class="py-2 px-4 border border-gray-300 rounded-md text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-50 active:text-gray-800 transition duration-150 ease-in-out">
                            Cancel
                        </a>
                    </span>
                    <span class="ml-3 inline-flex rounded-md shadow-sm">
                        <input type="submit" value="Save"
                               v-shortkey="['meta', 'enter']" @shortkey="clickLink($event, '', false)" v-tooltip="'Cmd + Enter'"
                               class="inline-flex justify-center py-2 px-4 border border-transparent text-sm leading-5 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:shadow-outline-indigo active:bg-indigo-700 transition duration-150 ease-in-out">
                    </span>
                </div>
            </div>
        </form>

    </main>
@endsection
