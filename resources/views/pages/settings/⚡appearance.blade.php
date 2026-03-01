<?php

use Livewire\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    <x-pages::settings.layout :heading="__('Appearance')" :subheading="__('Update the appearance settings for your account')">
        <div x-data="{
            appearance: localStorage.getItem('appearance') || 'system',
            updateAppearance(value) {
                this.appearance = value;
                localStorage.setItem('appearance', value);
        
                if (value === 'dark') {
                    document.documentElement.classList.add('dark');
                } else if (value === 'light') {
                    document.documentElement.classList.remove('dark');
                } else {
                    if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
                this.$dispatch('appearance-changed', value);
            }
        }"
            class="grid grid-cols-3 gap-3 p-1.5 bg-zinc-100 dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700">

            <!-- Light -->
            <button type="button" @click="updateAppearance('light')"
                :class="appearance === 'light' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' :
                    'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all">
                <x-heroicon-o-sun class="size-4" />
                <span>{{ __('Light') }}</span>
            </button>

            <!-- Dark -->
            <button type="button" @click="updateAppearance('dark')"
                :class="appearance === 'dark' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' :
                    'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all">
                <x-heroicon-o-moon class="size-4" />
                <span>{{ __('Dark') }}</span>
            </button>

            <!-- System -->
            <button type="button" @click="updateAppearance('system')"
                :class="appearance === 'system' ? 'bg-white dark:bg-zinc-700 text-zinc-900 dark:text-white shadow-sm' :
                    'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300'"
                class="flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-sm font-bold transition-all">
                <x-heroicon-o-computer-desktop class="size-4" />
                <span>{{ __('System') }}</span>
            </button>
        </div>
    </x-pages::settings.layout>
</section>

<x-slot:title>{{ __('Appearance') }}</x-slot:title>
