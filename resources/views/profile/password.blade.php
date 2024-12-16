@extends('layouts.master', [
    'title' => 'Stocks',
])

@push('haut')
    <link href="{{ asset('assets/table/css') }}/dataTables.tailwindcss.css" />
@endpush

@push('bas')
    <script src="{{ asset('assets/table/js') }}/jquery-3.7.1.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.js"></script>
    <script src="{{ asset('assets/table/js') }}/dataTables.tailwindcss.js"></script>
    <script>
        $(document).ready(function() {
            $('#example').DataTable();
        });
        $('#example').DataTable({
            dom: "<'flex justify-between items-center'<'flex items-center'l><'flex items-center'f>>" +
                "<'mt-4'tr>" +
                "<'flex justify-between items-center'<'p-2'i><'p-2'p>>",
        });
    </script>
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Modification de mot de passe
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            <div class="card px-4 pb-4 sm:px-5">
                <div>
                    <div class="mt-5">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <form action="{{ url('password') }}" method="POST" role="form">
                            @csrf
                            <div class="px-4 py-4 sm:px-5">
                                <div class="mt-4 space-y-4">
                                    <label class="block">
                                        <span>Mot de passe actuel</span>
                                        <input name="code" required
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Saisir le mot de passe encours" type="password" />
                                    </label>
                                    <label class="block">
                                        <span>Nouveau mot de passe</span>
                                        <input name="codenew" required
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Saisir le nouveau mot de passe" type="password" />
                                    </label>
                                    <label class="block">
                                        <span>Confirmer mot de passe</span>
                                        <input name="codeconfirm" required
                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                            placeholder="Répeter le nouveau mot de passe" type="password" />
                                    </label>
                                    <div class="space-x-2 text-right">
                                        <button type="submit"
                                            class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                            Modifier
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
    </main>
@endsection
