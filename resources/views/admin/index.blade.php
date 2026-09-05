<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Administration
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Cartes du hub --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ route('admin.users.index') }}"
                   class="block bg-white shadow-sm rounded-lg p-6 border-l-4 border-indigo-500 hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-700">Utilisateurs</h3>
                    <p class="text-3xl font-bold mt-2">{{ $usersCount }}</p>
                    <p class="text-sm text-gray-500 mt-1">Comptes et rôles du personnel</p>
                </a>

                <a href="{{ route('admin.audit.index') }}"
                   class="block bg-white shadow-sm rounded-lg p-6 border-l-4 border-amber-500 hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-700">Journal d'audit</h3>
                    <p class="text-3xl font-bold mt-2">{{ $logsCount }}</p>
                    <p class="text-sm text-gray-500 mt-1">Traçabilité de toutes les actions</p>
                </a>

                <a href="{{ route('admin.settings.edit') }}"
                   class="block bg-white shadow-sm rounded-lg p-6 border-l-4 border-green-500 hover:shadow-md transition">
                    <h3 class="font-semibold text-gray-700">Paramètres</h3>
                    <p class="text-3xl font-bold mt-2">{{ $nbBeds }} lits</p>
                    <p class="text-sm text-gray-500 mt-1">Hôpital, service, lits, listes cliniques</p>
                </a>
            </div>

            {{-- État du service --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-1">Service en ce moment</h3>
                <p class="text-sm text-gray-500">
                    {{ $activeStays }} patient(s) hospitalisé(s) sur {{ $nbBeds }} lits configurés.
                </p>
            </div>

            {{-- Matrice des droits (lecture seule) --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-700 mb-4">Matrice des droits (rappel)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 border-b">
                                <th class="py-2 pr-4">Fonctionnalité</th>
                                <th class="pr-4 text-center">ADMIN</th>
                                <th class="pr-4 text-center">SENIOR</th>
                                <th class="pr-4 text-center">JUNIOR</th>
                                <th class="pr-4 text-center">INTERN</th>
                                <th class="text-center">NURSE</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-2 pr-4">Consultation (tableau, fiches, archives)</td>
                                <td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">Admission d'un patient</td>
                                <td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">Sortie — transfert</td>
                                <td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">Sortie — constatation de décès</td>
                                <td class="text-center">✔</td><td class="text-center">✔</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td>
                            </tr>
                            <tr>
                                <td class="py-2 pr-4">Administration (cette page)</td>
                                <td class="text-center">✔</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td><td class="text-center text-gray-300">—</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
