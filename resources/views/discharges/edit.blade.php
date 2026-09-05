<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Sortie de patient
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Patient concerné</h3>
                <dl class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <dt class="text-gray-500">Nom</dt>
                        <dd class="font-semibold">{{ $hospitalization->patient->full_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">IPP</dt>
                        <dd class="font-semibold">{{ $hospitalization->patient->record_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Lit</dt>
                        <dd class="font-semibold">{{ $hospitalization->bed_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Admis le</dt>
                        <dd class="font-semibold">{{ $hospitalization->admission_dttm->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-gray-500">Diagnostic d’admission</dt>
                        <dd class="font-semibold">{{ $hospitalization->admission_diagnosis ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('discharges.update', $hospitalization) }}">
                    @csrf
                    @method('PUT')

                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Devenir du patient</h3>

                    <div class="space-y-3 mb-6">
                        <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-blue-50 has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="outcome" value="transferred" class="mt-1" @checked(old('outcome') === 'transferred') required>
                            <span class="ml-3">
                                <span class="block font-semibold text-blue-700">Transféré</span>
                                <span class="block text-sm text-gray-500">Le patient quitte la réanimation vers un autre service. Le lit se libère.</span>
                            </span>
                        </label>

                        @if (auth()->user()->hasAnyRole(['ADMIN', 'SENIOR']))
                            <label class="flex items-start p-4 border rounded-lg cursor-pointer hover:bg-red-50 has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                <input type="radio" name="outcome" value="deceased" class="mt-1" @checked(old('outcome') === 'deceased') required>
                                <span class="ml-3">
                                    <span class="block font-semibold text-red-700">Décédé</span>
                                    <span class="block text-sm text-gray-500">Décès du patient pendant son séjour en réanimation.</span>
                                </span>
                            </label>
                        @else
                            <div class="p-4 border border-dashed rounded-lg text-sm text-gray-500">
                                La constatation de décès est réservée au médecin senior.
                            </div>
                        @endif
                        <x-input-error :messages="$errors->get('outcome')" class="mt-1" />
                    </div>

                    {{-- Destination : visible sauf si décès --}}
                    <div class="mb-6 {{ old('outcome') === 'deceased' ? 'hidden' : '' }}" id="destination-block">
                        <x-input-label for="discharge_destination" value="Destination (obligatoire si transfert)" />
                        <select id="discharge_destination" name="discharge_destination" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">— Choisir —</option>
                            <option value="Cardiologie" @selected(old('discharge_destination') === 'Cardiologie')>Cardiologie</option>
                            <option value="Gynécologie" @selected(old('discharge_destination') === 'Gynécologie')>Gynécologie</option>
                            <option value="Hématologie" @selected(old('discharge_destination') === 'Hématologie')>Hématologie</option>
                            <option value="Maladies infectieuses" @selected(old('discharge_destination') === 'Maladies infectieuses')>Maladies infectieuses</option>
                            <option value="Médecine interne" @selected(old('discharge_destination') === 'Médecine interne')>Médecine interne</option>
                            <option value="Médecine légale" @selected(old('discharge_destination') === 'Médecine légale')>Médecine légale</option>
                            <option value="Néonatologie" @selected(old('discharge_destination') === 'Néonatologie')>Néonatologie</option>
                            <option value="Neurologie" @selected(old('discharge_destination') === 'Neurologie')>Neurologie</option>
                            <option value="Pédiatrie" @selected(old('discharge_destination') === 'Pédiatrie')>Pédiatrie</option>
                            <option value="Pneumologie" @selected(old('discharge_destination') === 'Pneumologie')>Pneumologie</option>
                            <option value="Urgences" @selected(old('discharge_destination') === 'Urgences')>Urgences</option>
                            <option value="Autre hôpital" @selected(old('discharge_destination') === 'Autre hôpital')>Autre hôpital</option>
                            <option value="Domicile" @selected(old('discharge_destination') === 'Domicile')>Domicile</option>
                        </select>
                        <x-input-error :messages="$errors->get('discharge_destination')" class="mt-1" />
                    </div>

                    {{-- Cause de décès : visible uniquement si décès --}}
                    <div class="mb-6 {{ old('outcome') === 'deceased' ? '' : 'hidden' }}" id="death-cause-block">
                        <x-input-label for="death_cause" value="Cause de décès (obligatoire si décès)" />
                        <textarea id="death_cause" name="death_cause" rows="3" maxlength="500"
                                  class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                  placeholder="Ex. : choc septique réfractaire, AVC ischémique étendu…">{{ old('death_cause') }}</textarea>
                        <x-input-error :messages="$errors->get('death_cause')" class="mt-1" />
                    </div>

                    <div class="bg-amber-50 border border-amber-300 text-amber-800 px-4 py-3 rounded-lg mb-6 text-sm">
                        Cette action clôture définitivement l’hospitalisation. Vérifiez le devenir avant de valider.
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Annuler
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                            Valider la sortie
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios     = document.querySelectorAll('input[name="outcome"]');
            const destBlock  = document.getElementById('destination-block');
            const destSelect = document.getElementById('discharge_destination');
            const deathBlock = document.getElementById('death-cause-block');
            const deathInput = document.getElementById('death_cause');

            function toggleFields() {
                const checked = document.querySelector('input[name="outcome"]:checked');
                const value   = checked ? checked.value : null;

                destBlock.classList.toggle('hidden', value === 'deceased');
                deathBlock.classList.toggle('hidden', value !== 'deceased');

                destSelect.required = (value !== 'deceased');
                deathInput.required = (value === 'deceased');
            }

            radios.forEach(radio => radio.addEventListener('change', toggleFields));
            toggleFields();
        });
    </script>
</x-app-layout>
