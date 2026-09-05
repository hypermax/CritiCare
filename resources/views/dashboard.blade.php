@php
    $statusColors = [
        'active'      => 'bg-green-100 text-green-800 border-green-300',
        'deceased'    => 'bg-red-100 text-red-800 border-red-300',
        'transferred' => 'bg-blue-100 text-blue-800 border-blue-300',
    ];
    $rowColors = [
        'active'      => 'bg-green-50',
        'deceased'    => 'bg-red-50',
        'transferred' => 'bg-blue-50',
    ];
    $cardBorders = [
        'active'      => 'border-green-500',
        'deceased'    => 'border-red-500',
        'transferred' => 'border-blue-500',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-y-2">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patients en réanimation
            </h2>
            <div class="flex flex-wrap items-center gap-4">
                <div class="inline-flex items-center bg-white border border-gray-300 rounded-lg p-1 gap-1" role="group" aria-label="Mode d'affichage">
                    <button type="button" id="btn-view-list" aria-pressed="true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        Liste
                    </button>
                    <button type="button" id="btn-view-cards" aria-pressed="false"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition text-gray-600 hover:bg-gray-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/>
                        </svg>
                        Cartes
                    </button>
                </div>
                @if(auth()->user()->hasRole('ADMIN'))
                    <a href="{{ route('admin.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-800 hover:underline">
                        Administration
                    </a>
                @endif
                <a href="{{ route('patients.index') }}" class="text-sm font-semibold text-indigo-600 hover:underline">
                    Archives &amp; recherche
                </a>
                <a href="{{ route('admissions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    + Nouvelle admission
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border-l-4 border-green-500 shadow rounded-lg px-4 py-3 flex items-baseline justify-between">
                    <span class="text-sm text-gray-500">Hospitalisés</span>
                    <span class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</span>
                </div>
                <div class="bg-white border-l-4 border-blue-500 shadow rounded-lg px-4 py-3 flex items-baseline justify-between">
                    <span class="text-sm text-gray-500">Transférés</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $stats['transferred'] }}</span>
                </div>
                <div class="bg-white border-l-4 border-red-500 shadow rounded-lg px-4 py-3 flex items-baseline justify-between">
                    <span class="text-sm text-gray-500">Décédés</span>
                    <span class="text-2xl font-bold text-red-600">{{ $stats['deceased'] }}</span>
                </div>
            </div>

            {{-- Vue Liste (tableau) --}}
            <div id="view-list">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Âge / Sexe</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Séjour</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnostic</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($hospitalizations as $h)
                                <tr class="{{ $rowColors[$h->status] ?? '' }}">
                                    <td class="px-4 py-3 font-bold text-lg">{{ $h->bed_number }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('patients.show', $h->patient_id) }}" class="font-semibold text-indigo-600 hover:underline">{{ $h->patient->full_name }}</a>
                                        <div class="text-xs text-gray-500">IPP : {{ $h->patient->record_number }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $h->patient->age }} ans / {{ $h->patient->sex_category }}</td>
                                    <td class="px-4 py-3">{{ $h->admission_dttm->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3">J{{ (int) $h->admission_dttm->diffInDays(now()) + 1 }}</td>
                                    <td class="px-4 py-3">{{ $h->admission_diagnosis ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$h->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ $h->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($h->status === 'active')
                                            @if (auth()->user()->hasAnyRole(['ADMIN', 'SENIOR', 'JUNIOR']))
                                                <a href="{{ route('discharges.edit', $h) }}" class="inline-flex items-center px-3 py-1 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600">
                                                    Sortie
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-500">
                                                Sorti le {{ $h->discharge_dttm?->format('d/m/Y') }}
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                        Aucune hospitalisation enregistrée pour le moment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Vue Cartes (compacte) --}}
            <div id="view-cards" class="hidden">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    @forelse ($hospitalizations as $h)
                        <div class="bg-white shadow rounded-lg border-l-4 {{ $cardBorders[$h->status] ?? 'border-gray-300' }} px-3 py-2.5 flex flex-col gap-1.5 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between gap-2">
                                <span class="inline-flex items-center bg-indigo-50 text-indigo-700 font-bold text-xs px-2 py-0.5 rounded">
                                    Lit {{ $h->bed_number }}
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$h->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                    {{ $h->status_label }}
                                </span>
                            </div>
                            <div class="flex items-baseline gap-2 min-w-0">
                                <a href="{{ route('patients.show', $h->patient_id) }}" class="font-bold text-sm text-indigo-600 hover:underline truncate">{{ $h->patient->full_name }}</a>
                                <span class="text-xs text-gray-500 shrink-0">{{ $h->patient->record_number }}</span>
                            </div>
                            <div class="text-xs text-gray-600 truncate">
                                {{ $h->patient->age }} ans / {{ $h->patient->sex_category }}
                                <span class="text-gray-300 mx-1">&bull;</span>
                                J{{ (int) $h->admission_dttm->diffInDays(now()) + 1 }}
                                <span class="text-gray-300 mx-1">&bull;</span>
                                {{ $h->admission_dttm->format('d/m H:i') }}
                            </div>
                            <div class="text-xs text-gray-700 bg-gray-50 rounded px-2 py-1 leading-snug line-clamp-2 min-h-[2.1rem]">
                                {{ $h->admission_diagnosis ?? '—' }}
                            </div>
                            <div class="flex justify-end pt-1.5 mt-auto border-t border-gray-100">
                                @if ($h->status === 'active')
                                    @if (auth()->user()->hasAnyRole(['ADMIN', 'SENIOR', 'JUNIOR']))
                                        <a href="{{ route('discharges.edit', $h) }}" class="inline-flex items-center px-2.5 py-1 bg-amber-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-600">
                                            Sortie
                                        </a>
                                    @endif
                                @else
                                    <span class="text-xs text-gray-500">
                                        Sorti le {{ $h->discharge_dttm?->format('d/m/Y') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center text-gray-500 py-8">
                            Aucune hospitalisation enregistrée pour le moment.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btnList   = document.getElementById('btn-view-list');
            const btnCards  = document.getElementById('btn-view-cards');
            const viewList  = document.getElementById('view-list');
            const viewCards = document.getElementById('view-cards');
            if (!btnList || !btnCards || !viewList || !viewCards) return;

            const ACTIVE   = ['bg-indigo-600', 'text-white'];
            const INACTIVE = ['text-gray-600', 'hover:bg-gray-100'];

            function paint(btn, isActive) {
                btn.classList.remove(...ACTIVE, ...INACTIVE);
                btn.classList.add(...(isActive ? ACTIVE : INACTIVE));
                btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
            }

            function setView(mode) {
                const isList = mode !== 'cards';
                viewList.classList.toggle('hidden', !isList);
                viewCards.classList.toggle('hidden', isList);
                paint(btnList, isList);
                paint(btnCards, !isList);
                try { localStorage.setItem('criticare.dashboard.view', isList ? 'list' : 'cards'); } catch (e) {}
            }

            btnList.addEventListener('click', function () { setView('list'); });
            btnCards.addEventListener('click', function () { setView('cards'); });

            let saved = 'list';
            try { saved = localStorage.getItem('criticare.dashboard.view') || 'list'; } catch (e) {}
            setView(saved);
        })();
    </script>
</x-app-layout>
