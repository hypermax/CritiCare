# Format CLIF — Spécification CritiCare (v1.0)

> Référence normative : **CLIF 2.1.0 Data Dictionary** — https://clif-icu.com/data-dictionary/data-dictionary-2.1.0
> Ce document fige comment CritiCare se conforme au *Common Longitudinal ICU data Format* (CLIF).
> Toute nouvelle table/colonne de l'application DOIT respecter ces règles.

## 1. Principes CLIF applicables à CritiCare

1. **Format longitudinal** : une ligne par événement daté (pas de colonnes « jour 1, jour 2… »).
2. **Clés** : `patient_id` (individu) et `hospitalization_id` (séjour). Dans CritiCare : `patients.record_number` (IPP) et `hospitalizations.id`.
3. **Double colonne systématique** : `*_name` = libellé brut saisi dans l'application ; `*_category` = valeur standardisée issue des listes mCIDE (minimum Common ICU Data Elements). Toute liste déroulante CritiCare doit définir ses deux versions.
4. **Dates/heures** : datetime conscient du fuseau, exporté en UTC au format `YYYY-MM-DD HH:MM:SS+00:00` ; dates seules `YYYY-MM-DD`. Stockage applicatif en heure locale (Africa/Algiers), conversion UTC à l'export.
5. **Sémantique** : en CLIF strict, une `hospitalization` couvre tout le séjour hospitalier ; les mouvements internes (entrée/sortie de réanimation) vivent dans la table `adt`. CritiCare v1 modélise le **séjour de réanimation** comme unité principale — voir §4 pour la règle d'export.
6. **Mortalité** : définie par `discharge_category = "Expired"`.
7. **Désidentification à l'export** : `first_name`, `last_name`, `phone`, `address` ne sont JAMAIS exportés ; seul `patient_id` (= IPP pseudonymisé) sort de l'application.

## 2. Tables CLIF 2.1.0 et périmètre CritiCare

| Table CLIF | Contenu | Statut CritiCare |
|---|---|---|
| `patient` | Démographie invariante | ✅ Existe (`patients`) |
| `hospitalization` | Une ligne par séjour | ✅ Existe (`hospitalizations`) |
| `adt` | Mouvements (lit, unité, entrée/sortie) | 🔶 Partiel : `bed_number` porté par `hospitalizations` ; table `adt` dédiée à créer quand changement de lit en cours de séjour |
| `vitals` | Signes vitaux horodatés | ⏳ Prochain chantier après la fiche patient |
| `respiratory_support` | Ventilation (device, mode, FiO2, PEEP…) | ⏳ Planifié |
| `medication_admin_continuous` | Perfusion continue (catécholamines, sédation) | ⏳ Planifié |
| `medication_admin_intermittent` | Doses discrètes (ATB, bolus) | ⏳ Planifié |
| `labs` | Biologie horodatée, unités de référence | ⏳ Planifié |
| `patient_assessments` | Scores et évaluations (GCS, RASS, douleur…) | ⏳ Planifié |
| `position` | Décubitus / prone | ⏳ Planifié |
| `crrt_therapy` | Épuration extrarénale continue | ⏳ Planifié |
| `microbiology_culture` / `microbiology_susceptibility` | Microbiologie | 🔵 Plus tard |
| `hospital_diagnosis` / `patient_procedures` | Diagnostics/procédures codés (CIM-10) | 🔵 Plus tard |
| `code_status` | Limitations thérapeutiques | 🔵 Plus tard |

## 3. Correspondance schéma CritiCare ↔ CLIF

### `patients` ↔ table CLIF `patient`

| CritiCare | CLIF | Règle |
|---|---|---|
| `record_number` | `patient_id` | IPP unique, pseudonyme d'export |
| `birth_date` | `birth_date` | Identique (`YYYY-MM-DD`) |
| `sex_category` | `sex_category` | Mapping : `M → Male`, `F → Female`, `X → Unknown` |
| — | `death_dttm` | À ajouter : renseigné à la clôture « décès » |
| `first_name`, `last_name`, `phone`, `address` | — | Champs locaux uniquement, jamais exportés |

### `hospitalizations` ↔ table CLIF `hospitalization`

| CritiCare | CLIF | Règle |
|---|---|---|
| `id` | `hospitalization_id` | Identifiant du séjour de réanimation |
| `admission_dttm` | `admission_dttm` | Identique (entrée en réanimation) |
| `discharge_dttm` | `discharge_dttm` | Identique (sortie de réanimation) |
| `admission_source` (11 services) | `admission_type_name` | Libellé brut conservé tel quel |
| — | `admission_type_category` | À dériver : mapping des 11 services + admissions directes vers les catégories mCIDE |
| `status` / `discharge_destination` | `discharge_category` | Mapping ci-dessous |
| `bed_number` | `adt.location_name` | Avec `location_category = "icu"` |
| — | `age_at_admission` | Calculé à l'export depuis `birth_date` |

**Mapping `status` + destination → `discharge_category` :**

| CritiCare | `discharge_category` CLIF |
|---|---|
| `active` | `Still Admitted` |
| `deceased` | `Expired` |
| `transferred` → Domicile | `Home` |
| `transferred` → Autre hôpital | `Acute Care Hospital` |
| `transferred` → autre service du même hôpital | Voir §4 (mouvement `adt`, le séjour hospitalier CLIF continue) |

## 4. Écart conceptuel connu (décision v1)

CritiCare est une application **périmètre réanimation** : sa `hospitalization` = le séjour en réanimation, pas le séjour hospitalier complet. En CLIF strict, un transfert réanimation → service du même hôpital ne clôt PAS l'hospitalization : c'est une ligne `adt` (`out_dttm` de l'ICU, `location_category = "ward"` ensuite).

Règle d'export figée pour CritiCare v1 :
- chaque séjour de réanimation exporte une ligne `hospitalization` + une ligne `adt` (`location_name = bed_number`, `location_category = "icu"`, `in_dttm = admission_dttm`, `out_dttm = discharge_dttm`) ;
- `discharge_category` suit le mapping du §3 ; le cas « autre service du même hôpital » sera raffiné en v2 si un jour l'application couvre l'aval hospitalier.

## 5. Conventions pour les prochaines tables

Noms de tables et colonnes **identiques** au dictionnaire CLIF 2.1.0 (snake_case). Points de vigilance par table :

- **`vitals`** : `hospitalization_id`, `recorded_dttm`, `vital_name`, `vital_category`, `vital_value`, `meas_site_name`. Les 9 `vital_category` mCIDE : `heart_rate`, `resp_rate`, `sbp`, `dbp`, `map`, `spo2`, `temp_c`, `height_cm`, `weight_kg`.
- **`respiratory_support`** : table large, une ligne par relevé horodaté. `device_category` ∈ {`IMV`, `NIPPV`, `CPAP`, `High Flow NC`, `Face Mask`, `Trach Collar`, `Nasal Cannula`, `Room Air`, `Other`} ; `mode_category` standardisé (Assist Control, SIMV, Pression support/CPAP…) ; champs `fio2_set`, `peep_set`, `resp_rate_set`, `lpm_set`, `tracheostomy`.
- **`medication_admin_continuous`** : `med_category` = principe actif standardisé (`norepinephrine`, `epinephrine`, `vasopressin`, `propofol`, `midazolam`…) ; `med_group` (`vasoactives`, `sedation`, `anticoagulation`…) ; `med_dose` + `med_dose_unit` ; **l'arrêt d'une perfusion = une ligne `med_dose = 0` avec `mar_action_category = "stopped"`** (pas de colonne end_dttm).
- **`labs`** : une ligne par résultat ; `lab_category` mCIDE, `lab_value` (brut) + `lab_value_numeric`, `reference_unit` imposée par catégorie (convertir à la saisie si besoin).
- **`patient_assessments`** : `assessment_category` (`gcs_total`, `gcs_eye`, `gcs_verbal`, `gcs_motor`, `rass`…), `assessment_group` (`Neurological`, `Sedation`, `Pain`…), résultat dans `numerical_value` / `categorical_value` / `text_value`.

## 6. Check-list avant toute nouvelle fonctionnalité

- [ ] Le nom de table/colonne suit le dictionnaire CLIF 2.1.0
- [ ] Chaque liste déroulante a son couple `*_name` / `*_category`
- [ ] Les horodatages sont des `datetime` (pas de `date` tronquée pour les événements)
- [ ] La clé de rattachement est `hospitalization_id` (jamais `patient_id` seul pour les événements de séjour, sauf `code_status` et `microbiology_culture`)
- [ ] La migration est versionnée + ce document mis à jour si nouvelle table
