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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#formCreate').on('submit', function(e) {
                e.preventDefault(); // Empêche l'envoi normal du formulaire

                // Réinitialiser les messages d'erreur
                $('.text-danger').text('');
                $('#loadingButton').hide(); // Cacher le bouton
                $('#loadingMessage').show(); // Afficher le message "Veuillez patienter"

                // Créer un objet FormData pour gérer le fichier et les autres champs
                var formData = new FormData(this);

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false, // Ne pas traiter les données
                    contentType: false, // Ne pas définir de type de contenu (important pour l'envoi de fichiers)
                    success: function(response) {
                        // Masquer le message de chargement
                        $('#loadingMessage').hide();
                        $('#loadingButton').show();

                        // Si succès, afficher un message de succès
                        alert(response.success);
                        $('#formCreate')[0].reset(); // Réinitialiser le formulaire
                        location.reload(); // Rafraîchit la page
                    },
                    error: function(xhr) {
                        // Masquer le message de chargement
                        $('#loadingMessage').hide();
                        $('#loadingButton').show();

                        // Si une erreur de validation se produit, afficher les erreurs dans les éléments correspondants
                        var errors = xhr.responseJSON.errors;

                        // Si les erreurs sont sous forme de texte global (non un tableau d'erreurs pour chaque champ)
                        if (typeof errors === 'string') {
                            // Afficher les erreurs globales dans un élément dédié (par exemple, "error-global")
                            $('#error-global').text(errors); // Affichage d'une erreur globale
                        }

                        if (errors) {
                            for (var field in errors) {
                                // Si un champ de formulaire a une erreur, on affiche l'erreur dans l'élément correspondant
                                $('#' + 'error-' + field).text(errors[
                                    field]); // Utilise l'ID de l'élément pour afficher l'erreur
                            }
                        }
                    }
                });
            });
        });
    </script>
@endpush

@section('content')
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        @include('layouts.status')

        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                {{ __('messages.mesStcks') }}
            </h2>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-5 lg:gap-6">
            <div class="card px-4 pb-4 sm:px-5">
                <div class="my-3 flex h-8 items-center justify-between px-4 sm:px-5">
                    <h2 class="font-medium tracking-wide text-slate-700 line-clamp-1 dark:text-navy-100 lg:text-base">
                    </h2>
                    <div x-data="usePopper({ placement: 'bottom-end', offset: 4 })" @click.outside="isShowPopper &amp;&amp; (isShowPopper = false)"
                        class="inline-flex">
                        <div x-data="{ showModal: false }">
                            <a href="{{ url('export') }}"
                                class="btn relative bg-secondary font-medium text-white hover:bg-secondary-focus focus:bg-secondary-focus active:bg-secondary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                {{ __('messages.export') }}
                            </a>
                            <button @click="showModal = true"
                                class="btn relative bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                {{ __('messages.import') }}
                            </button>
                            <template x-teleport="#x-teleport-target">
                                <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                    x-show="showModal" role="dialog" @keydown.window.escape="showModal = false">
                                    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                        @click="showModal = false" x-show="showModal" x-transition:enter="ease-out"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in" x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"></div>
                                    <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                        x-show="showModal" x-transition:enter="easy-out"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100" x-transition:leave="easy-in"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95">
                                        <div
                                            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                            <h3 class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                {{ __('messages.import') }}
                                            </h3>
                                            <button @click="showModal = !showModal"
                                                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4.5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        <form id="formCreate" action="{{ route('stocks.store') }}" method="POST"
                                            role="form" enctype="multipart/form-data">
                                            @csrf
                                            <div class="px-4 py-4 sm:px-5">
                                                <div class="mt-4 space-y-4">
                                                    <label class="block">
                                                        <span>{{ __('messages.file') }}</span><br>
                                                        <small><em>{{ __('messages.click') }}</em></small>
                                                        <input name="fichier" id="fichier" required
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Veuillez sélectionner le fichier" type="file"
                                                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" />
                                                        <div id="error-global" style="color: red;"></div>
                                                        <span style="color: red;" id="error-fichier"></span>
                                                    </label>
                                                    {{-- <br>
                                                    <hr>
                                                    <div class="text-center"><strong>Ou</strong></div>
                                                    <hr>
                                                    <label class="block">
                                                        <span>Code</span>
                                                        <input name="code"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Saisir le code" type="text" />
                                                    </label>
                                                    <label class="block">
                                                        <span>Quantité</span>
                                                        <input name="quantite"
                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                            placeholder="Saisir la quantité" type="number" />
                                                    </label> --}}
                                                    <div class="space-x-2 text-right">
                                                        <div id="loadingMessage" style="display: none;">
                                                            <p style="color: #018ea9">{{ __('messages.warning') }}</p>
                                                        </div>
                                                        <div id="loadingButton">
                                                            <button type="submit"
                                                                class="btn min-w-[7rem] rounded-full bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                {{ __('messages.import') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
                <div>
                    <div class="mt-5">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table id="example" class="is-zebra w-full text-left">
                                <thead>
                                    <tr>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Code
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            {{ __('messages.quantite') }}
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Date create
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap bg-slate-200 px-4 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Date update
                                        </th>
                                        <th style="background: #018ea9; color: white;"
                                            class="whitespace-nowrap rounded-r-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($stocks as $liste)
                                        <tr>
                                            <td class="whitespace-nowrap rounded-l-lg px-4 py-3 sm:px-5">
                                                {{ $liste->code_stock }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->quantite_initiale }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->created_at }}
                                            </td>
                                            <td class="whitespace-nowrap px-4 py-3 sm:px-5">
                                                {{ $liste->updated_at }}
                                            </td>
                                            <td data-column-id="actions" class="gridjs-td">
                                                <span>
                                                    <div class="flex justify-center space-x-2">
                                                        <div x-data="{ showModal{{ $liste->id }}: false }">
                                                            <button @click="showModal{{ $liste->id }} = true"
                                                                class="btn size-8 p-0 text-info hover:bg-info/20 focus:bg-info/20 active:bg-info/25">
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <template x-teleport="#x-teleport-target">
                                                                <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                                    x-show="showModal{{ $liste->id }}" role="dialog"
                                                                    @keydown.window.escape="showModal{{ $liste->id }} = false">
                                                                    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                                                        @click="showModal{{ $liste->id }} = false"
                                                                        x-show="showModal{{ $liste->id }}"
                                                                        x-transition:enter="ease-out"
                                                                        x-transition:enter-start="opacity-0"
                                                                        x-transition:enter-end="opacity-100"
                                                                        x-transition:leave="ease-in"
                                                                        x-transition:leave-start="opacity-100"
                                                                        x-transition:leave-end="opacity-0"></div>
                                                                    <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                                                        x-show="showModal{{ $liste->id }}"
                                                                        x-transition:enter="easy-out"
                                                                        x-transition:enter-start="opacity-0 scale-95"
                                                                        x-transition:enter-end="opacity-100 scale-100"
                                                                        x-transition:leave="easy-in"
                                                                        x-transition:leave-start="opacity-100 scale-100"
                                                                        x-transition:leave-end="opacity-0 scale-95">
                                                                        <div
                                                                            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                                            <h3
                                                                                class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                                                {{ __('messages.edit') }}
                                                                            </h3>
                                                                            <button
                                                                                @click="showModal{{ $liste->id }} = !showModal{{ $liste->id }}"
                                                                                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="size-4.5" fill="none"
                                                                                    viewBox="0 0 24 24"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <form
                                                                            action="{{ route('stocks.update', $liste->id) }}"
                                                                            method="POST" role="form">
                                                                            @csrf
                                                                            @method('PATCH')
                                                                            <div class="px-4 py-4 sm:px-5">
                                                                                <div class="mt-4 space-y-4">
                                                                                    <label class="block">
                                                                                        {{-- <span>Code</span> --}}
                                                                                        <input name="code" required
                                                                                            hidden
                                                                                            value="{{ $liste->code_stock }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="Saisir le code"
                                                                                            type="text" />
                                                                                    </label>
                                                                                    <label class="block">
                                                                                        <span>{{ __('messages.quantite') }}</span>
                                                                                        <input name="quantite" required
                                                                                            value="{{ $liste->quantite_initiale }}"
                                                                                            class="form-input mt-1.5 w-full rounded-lg border border-slate-300 bg-transparent px-3 py-2 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:hover:border-navy-400 dark:focus:border-accent"
                                                                                            placeholder="{{ __('messages.quantite') }}"
                                                                                            type="number" />
                                                                                    </label>
                                                                                    <div class="space-x-2 text-right">
                                                                                        <button type="submit"
                                                                                            class="btn min-w-[7rem] rounded-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                            {{ __('messages.edit') }}
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <div x-data="{ showModalDelete{{ $liste->id }}: false }">
                                                            <button @click="showModalDelete{{ $liste->id }} = true"
                                                                class="btn size-8 p-0 text-error hover:bg-error/20 focus:bg-error/20 active:bg-error/25">
                                                                <i class="fa fa-trash-alt"></i>
                                                            </button>
                                                            <template x-teleport="#x-teleport-target">
                                                                <div class="fixed inset-0 z-[100] flex flex-col items-center justify-center overflow-hidden px-4 py-6 sm:px-5"
                                                                    x-show="showModalDelete{{ $liste->id }}"
                                                                    role="dialog"
                                                                    @keydown.window.escape="showModalDelete{{ $liste->id }} = false">
                                                                    <div class="absolute inset-0 bg-slate-900/60 transition-opacity duration-300"
                                                                        @click="showModalDelete{{ $liste->id }} = false"
                                                                        x-show="showModalDelete{{ $liste->id }}"
                                                                        x-transition:enter="ease-out"
                                                                        x-transition:enter-start="opacity-0"
                                                                        x-transition:enter-end="opacity-100"
                                                                        x-transition:leave="ease-in"
                                                                        x-transition:leave-start="opacity-100"
                                                                        x-transition:leave-end="opacity-0"></div>
                                                                    <div class="relative w-full max-w-lg origin-top rounded-lg bg-white transition-all duration-300 dark:bg-navy-700"
                                                                        x-show="showModalDelete{{ $liste->id }}"
                                                                        x-transition:enter="easy-out"
                                                                        x-transition:enter-start="opacity-0 scale-95"
                                                                        x-transition:enter-end="opacity-100 scale-100"
                                                                        x-transition:leave="easy-in"
                                                                        x-transition:leave-start="opacity-100 scale-100"
                                                                        x-transition:leave-end="opacity-0 scale-95">
                                                                        <div
                                                                            class="flex justify-between rounded-t-lg bg-slate-200 px-4 py-3 dark:bg-navy-800 sm:px-5">
                                                                            <h3
                                                                                class="text-base font-medium text-slate-700 dark:text-navy-100">
                                                                                {{ __('messages.zero') }}
                                                                            </h3>
                                                                            <button
                                                                                @click="showModalDelete{{ $liste->id }} = !showModalDelete{{ $liste->id }}"
                                                                                class="btn -mr-1.5 size-7 rounded-full p-0 hover:bg-slate-300/20 focus:bg-slate-300/20 active:bg-slate-300/25 dark:hover:bg-navy-300/20 dark:focus:bg-navy-300/20 dark:active:bg-navy-300/25">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="size-4.5" fill="none"
                                                                                    viewBox="0 0 24 24"
                                                                                    stroke="currentColor"
                                                                                    stroke-width="2">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        d="M6 18L18 6M6 6l12 12"></path>
                                                                                </svg>
                                                                            </button>
                                                                        </div>
                                                                        <form
                                                                            action="{{ route('stocks.destroy', $liste->id) }}"
                                                                            method="POST" role="form">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <div class="px-4 py-4 sm:px-5">
                                                                                <div class="mt-4 space-y-4">
                                                                                    <label class="block">
                                                                                        {{ __('messages.zerostock') }}
                                                                                    </label>
                                                                                    <div class="space-x-2 text-right">
                                                                                        <button type="submit"
                                                                                            class="btn min-w-[7rem] rounded-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">
                                                                                            {{ __('messages.soumettre') }}
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </main>
@endsection
