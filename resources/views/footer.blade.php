<?php
/** @var \App\User $user */
?>

<div class="bg-gray-800" :class="{ 'opacity-0' : focusMode }">
    <div class="max-w-screen-xl mx-auto py-12 px-4 sm:px-6 lg:py-16 lg:px-8">
        <div class="xl:grid xl:grid-cols-3 xl:gap-8">
            <div class="xl:col-span-1 mb-8">
                <a href="/">
                    @include('svg.logo', ['classes' => 'h-8 w-auto sm:h-10 text-gray-500 hover:opacity-50 transition ease-in-out duration-150'])
                </a>

                <p class="mt-4 text-gray-400 text-xl leading-6">
                    Tag 'em and bag 'em
                </p>
            </div>

            <div class="grid grid-cols-2 gap-8 xl:col-span-2">
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <div>
                        <h4 class="text-sm leading-5 font-semibold tracking-wider text-gray-400 uppercase">
                            Manage
                        </h4>
                        <ul class="mt-4">
                            <li>
                                <a href="/account/people/new"
                                   v-shortkey="['n']" @shortkey="clickLink($event, 'n')"
                                   class="text-base leading-6 text-gray-300 hover:text-white">
                                    New Person
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="javascript://" @click="showKeyboardShortcuts"
                                   v-shortkey="['?']" @shortkey="showKeyboardShortcuts()" v-tooltip="'?'"
                                   class="text-base leading-6 text-gray-300 hover:text-white">
                                    Keyboard shortcuts
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="javascript://" @click="toggleFocusMode" v-shortkey="['f']" @shortkey="toggleFocusMode()"
                                   class="text-base leading-6 text-gray-300 hover:text-white" v-tooltip="'F key'">
                                    <template v-if="!focusMode">
                                        Enable focus mode
                                    </template>
                                    <template v-else>
                                        Disable focus mode
                                    </template>
                                </a>
                            </li>
                            @if (isset($user) && $user->isAdmin())
                                <li class="mt-4">
                                    <a href="{{ Util::airtableUrl() }}" target="_blank"
                                       v-shortkey="['shift', 'a']" @shortkey="clickLink($event, 'A')" v-tooltip="'Shift + A'"
                                       class="text-base leading-6 text-gray-300 hover:text-white">
                                        Airtable
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="mt-12 md:mt-0">
                        <h4 class="text-sm leading-5 font-semibold tracking-wider text-gray-400 uppercase">
                            Support
                        </h4>
                        <ul class="mt-4">
                            <li>
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Pricing
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Documentation
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Guides
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    API Status
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="md:grid md:grid-cols-2 md:gap-8">
                    <div>
                        <h4 class="text-sm leading-5 font-semibold tracking-wider text-gray-400 uppercase">
                            Social
                        </h4>
                        <ul class="mt-4">
                            <li>
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Twitter
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    LinkedIn
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="mt-12 md:mt-0">
                        <h4 class="text-sm leading-5 font-semibold tracking-wider text-gray-400 uppercase">
                            About
                        </h4>
                        <ul class="mt-4">
                            <li>
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Stuff
                                </a>
                            </li>
                            <li class="mt-4">
                                <a href="#" class="text-base leading-6 text-gray-300 hover:text-white">
                                    Terms
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 border-t border-gray-700 pt-8 md:flex md:items-center md:justify-between">
            <div class="flex md:order-2">
                <a href="https://twitter.com/kalenjordan" class="text-gray-400 hover:text-gray-300">
                    <span class="sr-only">Twitter</span>
                    @include('svg.icon-twitter', ['classes' => 'h-6 w-6'])
                </a>
                <a href="https://linkedin.com/in/kalen" class="ml-6 text-gray-400 hover:text-gray-300">
                    <span class="sr-only">LinkedIn</span>
                    @include('svg.icon-linkedin', ['classes' => 'h-6 w-6'])
                </a>
            </div>
            <p class="mt-8 text-base leading-6 text-gray-400 md:mt-0 md:order-1">
                &copy; {{ date('Y') }}. All rights reserved.
            </p>
        </div>
    </div>
</div>
