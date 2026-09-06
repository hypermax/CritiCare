<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nouvelle admission
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('admissions.store') }}">
                    @csrf

                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Identité du patient</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="record_number" value="N° IPP *" />
                            <x-text-input id="record_number" name="record_number" type="text" class="mt-1 block w-full" :value="old('record_number', request('record_number'))" required />
                            <x-input-error :messages="$errors->get('record_number')" class="mt-1" />
                            <p class="mt-1 text-xs text-gray-500">Patient déjà passé en réanimation ? Saisissez l'IPP puis Tab : ses informations seront préremplies.</p>
                            <p id="ipp-status" class="mt-1 text-sm"></p>
                        </div>
                        <div>
                            <x-input-label for="birth_date" value="Date de naissance *" />
                            <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date')" required />
                            <x-input-error :messages="$errors->get('birth_date')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="last_name" value="Nom *" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name')" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="first_name" value="Prénom *" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name')" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="sex_category" value="Sexe *" />
                            <select id="sex_category" name="sex_category" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">— Choisir —</option>
                                <option value="M" @selected(old('sex_category') === 'M')>Masculin</option>
                                <option value="F" @selected(old('sex_category') === 'F')>Féminin</option>
                                <option value="X" @selected(old('sex_category') === 'X')>Autre / indéterminé</option>
                            </select>
                            <x-input-error :messages="$errors->get('sex_category')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="phone" value="Téléphone (famille)" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                            <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                        </div>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Hospitalisation</h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <x-input-label for="bed_number" value="Lit *" />
                            <select id="bed_number" name="bed_number" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">— Choisir —</option>
                                @for ($i = 1; $i <= $bedCount; $i++)
                                    <option value="{{ $i }}" @disabled(in_array($i, $occupiedBeds)) @selected(old('bed_number') == $i)>
                                        Lit {{ $i }}{{ in_array($i, $occupiedBeds) ? ' — occupé' : '' }}
                                    </option>
                                @endfor
                            </select>
                            <x-input-error :messages="$errors->get('bed_number')" class="mt-1" />
                        </div>
                        <div>
                            <x-input-label for="admission_source" value="Provenance" />
                            <select id="admission_source" name="admission_source" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">— Choisir —</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service }}" @selected(old('admission_source') === $service)>{{ $service }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('admission_source')" class="mt-1" />
                        </div>
                        <div class="sm:col-span-2">
                            <x-input-label for="admission_diagnosis" value="Diagnostic d’admission" />
                            <x-text-input id="admission_diagnosis" name="admission_diagnosis" type="text" class="mt-1 block w-full" :value="old('admission_diagnosis')" />
                            <x-input-error :messages="$errors->get('admission_diagnosis')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 underline">
                            Annuler
                        </a>
                        <x-primary-button>
                            Admettre le patient
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ippInput = document.getElementById('record_number');
            const statusEl = document.getElementById('ipp-status');
            const fields = {
                last_name:    document.getElementById('last_name'),
                first_name:   document.getElementById('first_name'),
                birth_date:   document.getElementById('birth_date'),
                sex_category: document.getElementById('sex_category'),
                phone:        document.getElementById('phone'),
            };

            ippInput.addEventListener('blur', async function () {
                const ipp = ippInput.value.trim();
                statusEl.textContent = '';
                statusEl.className = 'mt-1 text-sm';
                if (ipp === '') return;

                try {
                    const resp = await fetch('{{ route('patients.lookup') }}?record_number=' + encodeURIComponent(ipp), {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (! resp.ok) return;
                    const data = await resp.json();

                    if (! data.found) return; // Nouveau patient : rien à préremplir

                    fields.last_name.value    = data.last_name ?? '';
                    fields.first_name.value   = data.first_name ?? '';
                    fields.birth_date.value   = data.birth_date ?? '';
                    fields.sex_category.value = data.sex_category ?? '';
                    fields.phone.value        = data.phone ?? '';

                    if (data.deceased) {
                        statusEl.textContent = '⚠ Ce patient est décédé lors d’un séjour précédent — vérifiez l’IPP.';
                        statusEl.classList.add('text-red-600', 'font-semibold');
                    } else {
                        statusEl.innerHTML = '✔ Patient connu — informations préremplies, vérifiez-les. <a href="' + data.fiche_url + '" target="_blank" class="underline">Voir la fiche</a>';
                        statusEl.classList.add('text-green-700');
                    }
                } catch (e) {
                    // Réseau indisponible : le garde-fou côté serveur reste en place
                }
            });

            // Réadmission depuis la fiche patient : l'IPP est déjà rempli via l'URL,
            // on déclenche le préremplissage existant automatiquement.
            if (ippInput.value.trim() !== '') {
                ippInput.dispatchEvent(new Event('blur'));
            }
        });
    </script>
</x-app-layout>
