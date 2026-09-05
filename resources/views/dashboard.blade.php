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
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Patients en réanimation
            </h2>
            <a href="{{ route('admissions.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                + Nouvelle admission
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border-l-4 border-green-500 shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Hospitalisés</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active'] }}</div>
                </div>
                <div class="bg-white border-l-4 border-blue-500 shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Transférés</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['transferred'] }}</div>
                </div>
                <div class="bg-white border-l-4 border-red-500 shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Décédés</div>
                    <div class="text-3xl font-bold text-red-600">{{ $stats['deceased'] }}</div>
                </div>
            </div>

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
                                        @else
                                            <span class="text-xs text-gray-400">—</span>
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
    </div>
</x-app-layout>
