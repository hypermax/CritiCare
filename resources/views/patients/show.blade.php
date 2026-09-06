<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Fiche patient — {{ $patient->full_name }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">
                &larr; Retour au tableau
            </a>
        </div>
    </x-slot>

    @php
        // Même palette que le tableau de bord (classes Tailwind complètes, scannées par Vite)
        $statusColors = [
            'active'      => 'bg-green-100 text-green-800 border-green-300',
            'transferred' => 'bg-blue-100 text-blue-800 border-blue-300',
            'deceased'    => 'bg-red-100 text-red-800 border-red-300',
        ];
        $deceasedStay = $patient->hospitalizations->firstWhere('status', 'deceased');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Bandeau décès (prioritaire) --}}
            @if($deceasedStay)
                <div class="bg-red-50 border border-red-300 rounded-lg p-4">
                    <p class="font-semibold text-red-800">
                        Patient décédé le {{ $deceasedStay->discharge_dttm?->format('d/m/Y') ?? '—' }}
                    </p>
                    @if($deceasedStay->death_cause)
                        <p class="text-sm text-red-700">Cause : {{ $deceasedStay->death_cause }}</p>
                    @endif
                </div>
            @endif

            {{-- Bandeau séjour actif --}}
            @if($activeStay)
                <div class="bg-green-50 border border-green-300 rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-green-800">
                            Séjour en cours — Lit {{ $activeStay->bed_number }}
                            <span class="text-sm font-normal">
                                (J{{ (int) $activeStay->admission_dttm->diffInDays(now()) + 1 }})
                            </span>
                        </p>
                        <p class="text-sm text-green-700">
                            Admis le {{ $activeStay->admission_dttm->format('d/m/Y H:i') }}
                            — {{ $activeStay->admission_diagnosis }}
                            — Provenance : {{ $activeStay->admission_source }}
                        </p>
                    </div>
                    @if(auth()->user()->hasAnyRole(['ADMIN', 'SENIOR', 'JUNIOR']))
                        <a href="{{ route('discharges.edit', $activeStay) }}"
                           class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm whitespace-nowrap">
                            Clôturer le séjour
                        </a>
                    @endif
                </div>
            @endif

            {{-- Identité + résumé --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Identité</h3>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">IPP</dt>
                            <dd class="font-mono">{{ $patient->record_number }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Nom</dt>
                            <dd>{{ $patient->last_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Prénom</dt>
                            <dd>{{ $patient->first_name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Naissance</dt>
                            <dd>{{ $patient->birth_date->format('d/m/Y') }} ({{ $patient->age }} ans)</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Sexe</dt>
                            <dd>{{ $patient->sex_category }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Téléphone</dt>
                            <dd>{{ $patient->phone ?? '—' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Adresse</dt>
                            <dd>{{ $patient->address ?? '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">Résumé</h3>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold">{{ $patient->hospitalizations->count() }}</p>
                            <p class="text-xs text-gray-500">Séjours</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">{{ $totalDays }}</p>
                            <p class="text-xs text-gray-500">Jours cumulés</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold">{{ $activeStay ? 'Oui' : 'Non' }}</p>
                            <p class="text-xs text-gray-500">Hospitalisé</p>
                        </div>
                    </div>
                    @if(! $deceasedStay && ! $activeStay)
                        <div class="mt-6">
                            <a href="{{ route('admissions.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                                + Nouvelle admission
                            </a>
                        </div>
                    @elseif($activeStay)
                        <p class="mt-6 text-sm text-gray-500">
                            Patient actuellement hospitalisé (lit n° {{ $activeStay->bed_number }}) — clôturez le séjour en cours avant toute réadmission.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Historique des séjours --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Historique des séjours</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">Lit</th>
                                <th class="pr-4">Admission</th>
                                <th class="pr-4">Sortie</th>
                                <th class="pr-4">Jours</th>
                                <th class="pr-4">Diagnostic</th>
                                <th class="pr-4">Provenance / Destination</th>
                                <th class="pr-4">Statut</th>
                                <th>Saisi par</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patient->hospitalizations as $stay)
                                <tr class="border-b">
                                    <td class="py-2 pr-4">{{ $stay->bed_number }}</td>
                                    <td class="pr-4">{{ $stay->admission_dttm->format('d/m/Y H:i') }}</td>
                                    <td class="pr-4">{{ $stay->discharge_dttm?->format('d/m/Y H:i') ?? '—' }}</td>
                                    <td class="pr-4">
                                        J{{ (int) $stay->admission_dttm->diffInDays($stay->discharge_dttm ?? now()) + 1 }}
                                    </td>
                                    <td class="pr-4">{{ $stay->admission_diagnosis ?? '—' }}</td>
                                    <td class="pr-4">
                                        {{ $stay->admission_source }}
                                        @if($stay->status === 'deceased' && $stay->death_cause)
                                            <br><span class="text-xs text-red-700">Décès : {{ $stay->death_cause }}</span>
                                        @elseif($stay->discharge_destination)
                                            &rarr; {{ $stay->discharge_destination }}
                                        @endif
                                    </td>
                                    <td class="pr-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$stay->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                            {{ $stay->status_label }}
                                        </span>
                                    </td>
                                    <td>{{ $stay->creator->name ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="py-4 text-center text-gray-400">
                                        Aucun séjour enregistré.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
