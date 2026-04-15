<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-800 tracking-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden rounded-xl border border-slate-100 shadow-sm">
                <div class="p-6 text-slate-700">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
