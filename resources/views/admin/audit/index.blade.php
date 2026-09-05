<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Journal d'audit
            </h2>
            <a href="{{ route('admin.users.index') }}" class="text-sm text-indigo-600 hover:underline">
                Gestion des utilisateurs &rarr;
            </a>
        </div>
    </x-slot>

    @php
        // Palette par famille d'action (classes Tailwind complètes, scannées par Vite)
        $actionColors = [
            'auth'      => 'bg-gray-100 text-gray-800 border-gray-300',
            'patient'   => 'bg-indigo-100 text-indigo-800 border-indigo-300',
            'admission' => 'bg-green-100 text-green-800 border-green-300',
            'discharge' => 'bg-amber-100 text-amber-800 border-amber-300',
            'user'      => 'bg-purple-100 text-purple-800 border-purple-300',
        ];
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date / Heure</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilisateur</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Détails</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($logs as $log)
                            @php $prefix = explode('.', $log->action)[0]; @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm whitespace-nowrap">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td class="px-4 py-3 text-sm">{{ $log->user->name ?? 'Système' }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $actionColors[$prefix] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                                        {{ $log->action_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">{{ $log->description }}</td>
                                <td class="px-4 py-3 text-xs text-gray-500">{{ $log->ip_address ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Journal vide pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
