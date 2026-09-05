<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Archives &amp; recherche
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm text-indigo-600 hover:underline">
                &larr; Retour au tableau
            </a>
        </div>
    </x-slot>

    @php
        $statusColors = [
            'active'      => 'bg-green-100 text-green-800 border-green-300',
            'transferred' => 'bg-blue-100 text-blue-800 border-blue-300',
            'deceased'    => 'bg-red-100 text-red-800 border-red-300',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Barre de recherche --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="GET" action="{{ route('patients.index') }}" class="flex items-center gap-3">
                    <input type="text" name="q" value="{{ $search }}" autofocus
                           placeholder="IPP, nom ou prénom du patient…"
                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                        Rechercher
                    </button>
                    @if($search !== '')
                        <a href="{{ route('patients.index') }}" class="text-sm text-gray-600 hover:underline whitespace-nowrap">
                            Effacer
                        </a>
                    @endif
                </form>
            </div>

            @if($search !== '')
                {{-- Résultats de recherche --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">
                        {{ $results->count() }} résultat(s) pour « {{ $search }} »
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pr-4">IPP</th>
                                    <th class="pr-4">Patient</th>
                                    <th class="pr-4">Âge / Sexe</th>
                                    <th class="pr-4">Séjours</th>
                                    <th class="pr-4">Dernier séjour</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($results as $patient)
                                    @php $lastStay = $patient->hospitalizations->first(); @endphp
                                    <tr class="border-b">
                                        <td class="py-2 pr-4 font-mono">{{ $patient->record_number }}</td>
                                        <td class="pr-4">
                                            <a href="{{ route('patients.show', $patient) }}" class="font-semibold text-indigo-600 hover:underline">
                                                {{ $patient->full_name }}
                                            </a>
                                        </td>
                                        <td class="pr-4">{{ $patient->age }} ans / {{ $patient->sex_category }}</td>
                                        <td class="pr-4">{{ $patient->hospitalizations_count }}</td>
                                        <td class="pr-4">
                                            @if($lastStay)
                                                {{ $lastStay->admission_dttm->format('d/m/Y') }}
                                                @if($lastStay->discharge_destination)
                                                    &rarr; {{ $lastStay->discharge_destination }}
                                                @endif
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            @if($lastStay)
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$lastStay->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                                    {{ $lastStay->status_label }}
                                                </span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-400">
                                            Aucun patient trouvé — vérifiez l'orthographe ou l'IPP.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                {{-- Sorties récentes --}}
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-700 mb-4">20 dernières sorties</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500 border-b">
                                    <th class="py-2 pr-4">Patient</th>
                                    <th class="pr-4">Lit</th>
                                    <th class="pr-4">Admission</th>
                                    <th class="pr-4">Sortie</th>
                                    <th class="pr-4">Destination</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDischarges as $stay)
                                    <tr class="border-b">
                                        <td class="py-2 pr-4">
                                            <a href="{{ route('patients.show', $stay->patient_id) }}" class="font-semibold text-indigo-600 hover:underline">
                                                {{ $stay->patient->full_name }}
                                            </a>
                                            <span class="text-xs text-gray-500">({{ $stay->patient->record_number }})</span>
                                        </td>
                                        <td class="pr-4">{{ $stay->bed_number }}</td>
                                        <td class="pr-4">{{ $stay->admission_dttm->format('d/m/Y H:i') }}</td>
                                        <td class="pr-4">{{ $stay->discharge_dttm->format('d/m/Y H:i') }}</td>
                                        <td class="pr-4">{{ $stay->discharge_destination ?? '—' }}</td>
                                        <td>
                                            <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $statusColors[$stay->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                                {{ $stay->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-4 text-center text-gray-400">
                                            Aucune sortie enregistrée pour le moment.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
