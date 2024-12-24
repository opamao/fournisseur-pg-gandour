@extends('layouts.master', [
    'title' => 'Tableau de bord',
])

@section('content')
    <!-- Main Content Wrapper -->
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="mt-4 grid grid-cols-12 gap-4 sm:mt-5 sm:gap-5 lg:mt-6 lg:gap-6">
            <div class="card col-span-12 lg:col-span-12">
                <div class="mt-4 grid grid-cols-2 gap-3 px-4 sm:mt-5 sm:grid-cols-4 sm:gap-5 sm:px-5 lg:mt-6">
                    <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                        <div class="flex justify-between space-x-1">
                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $nbreStock }}
                            </p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-primary dark:text-accent" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="mt-1 text-xs+">{{ __('messages.nStock') }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-100 p-4 dark:bg-navy-600">
                        <div class="flex justify-between">
                            <p class="text-xl font-semibold text-slate-700 dark:text-navy-100">
                                {{ $totalStock }}
                            </p>
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-success" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                            </svg>
                        </div>
                        <p class="mt-1 text-xs+">{{ __('messages.tStock') }}</p>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </main>
@endsection
