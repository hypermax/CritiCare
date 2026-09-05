<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Paramètres du service
            </h2>
            <a href="{{ route('admin.index') }}" class="text-sm text-indigo-600 hover:underline">
                &larr; Administration
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="hospital_name" value="Nom de l'hôpital" />
                        <x-text-input id="hospital_name" name="hospital_name" type="text" class="mt-1 block w-full" :value="old('hospital_name', $hospitalName)" />
                        <x-input-error :messages="$errors->get('hospital_name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="service_name" value="Nom du service" />
                        <x-text-input id="service_name" name="service_name" type="text" class="mt-1 block w-full" :value="old('service_name', $serviceName)" />
                        <x-input-error :messages="$errors->get('service_name')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="nb_beds" value="Nombre de lits de réanimation *" />
                        <x-text-input id="nb_beds" name="nb_beds" type="number" min="1" max="100" class="mt-1 block w-32" :value="old('nb_beds', $nbBeds)" required />
                        <p class="text-xs text-gray-500 mt-1">Appliqué immédiatement au formulaire d'admission et à la validation côté serveur.</p>
                        <x-input-error :messages="$errors->get('nb_beds')" class="mt-1" />
                    </div>

                    <div>
                        <x-input-label for="services" value="Services de provenance / destination (un par ligne) *" />
                        <textarea id="services" name="services" rows="12" required
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('services', $services) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Alimente les listes « Provenance » (admission) et « Destination » (sortie). « Autre hôpital » et « Domicile » sont ajoutés automatiquement à la sortie.</p>
                        <x-input-error :messages="$errors->get('services')" class="mt-1" />
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <a href="{{ route('admin.index') }}" class="text-sm text-gray-600 hover:underline">Annuler</a>
                        <x-primary-button>
                            Enregistrer les paramètres
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
