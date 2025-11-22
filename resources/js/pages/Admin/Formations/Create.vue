<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { ref, watch, onMounted } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Formations', href: route('admin.formations.index') },
  { title: 'Créer', href: route('admin.formations.create') }
];

const form = useForm({
  title: '',
  description: '',
  image: null as File | null,
  attachment: null as File | null,
  date_mode: 'single' as 'single' | 'range',
  date: '',
  start_time: '',
  end_time: '',
  start_date: '',
  end_date: '',
  duration_hours: '' as any,
  duration_per_day_hours: '' as any,
  price: '' as any,
    currency: 'EUR' as string | null, // Valeur par défaut modifiée à 'EUR'

});

const imagePreview = ref<string | null>(null);

const editorRef = ref<HTMLDivElement | null>(null);
let editor: Quill;

onMounted(() => {
  if (editorRef.value) {
    editor = new Quill(editorRef.value, {
      theme: 'snow',
      modules: {
        toolbar: [
          ['bold', 'italic', 'underline', 'strike'],
          [{ list: 'ordered' }, { list: 'bullet' }],
          [{ header: [1, 2, 3, 4, 5, 6, false] }],
          [{ color: [] }, { background: [] }],
          ['link', 'image'],
          ['clean']
        ]
      },
      placeholder: 'Décrivez la formation en détail...'
    });
  }
});

function onImageChange(e: Event) {
  const input = e.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    form.image = input.files[0];
    const reader = new FileReader();
    reader.onload = ev => imagePreview.value = ev.target?.result as string;
    reader.readAsDataURL(input.files[0]);
  }
}

function onAttachmentChange(e: Event) {
  const input = e.target as HTMLInputElement;
  if (input.files && input.files[0]) {
    form.attachment = input.files[0];
  }
}

watch(() => form.date_mode, (mode) => {
  if (mode === 'single') {
    form.start_date = '';
    form.end_date = '';
    form.duration_per_day_hours = '' as any;
  } else {
    form.date = '';
    form.duration_hours = '' as any;
    form.start_time = '';
    form.end_time = '';
  }
});

// --- Time and duration synchronization (single date mode) ---
const isUpdatingEndTime = ref(false);
const isUpdatingDuration = ref(false);

function parseTimeToMinutes(time: string): number | null {
  if (!time) return null;
  const [h, m] = time.split(':').map(Number);
  if (Number.isNaN(h) || Number.isNaN(m)) return null;
  return h * 60 + m;
}

function minutesToTimeStr(totalMinutes: number): string {
  // keep within 0..1439 (same-day assumption)
  totalMinutes = Math.max(0, Math.min(23 * 60 + 59, Math.round(totalMinutes)));
  const h = Math.floor(totalMinutes / 60);
  const m = totalMinutes % 60;
  return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
}

// If duration and start_time are provided, auto-compute end_time
watch([
  () => form.duration_hours,
  () => form.start_time,
  () => form.date_mode
], ([duration, startTime, mode]) => {
  if (mode !== 'single') return;
  if (isUpdatingDuration.value) return; // avoid reacting while we're updating duration from times
  if (duration === '' || duration === null || duration === undefined) return;
  const dur = Number(duration);
  const startMin = parseTimeToMinutes(startTime as string);
  if (!Number.isFinite(dur) || dur <= 0 || startMin === null) return;
  const endMin = startMin + dur * 60;
  isUpdatingEndTime.value = true;
  form.end_time = minutesToTimeStr(endMin);
  // small timeout to release flag after watchers settle
  setTimeout(() => { isUpdatingEndTime.value = false; }, 0);
});

// If start_time and end_time are provided, auto-compute duration_hours
watch([
  () => form.start_time,
  () => form.end_time,
  () => form.date_mode
], ([startTime, endTime, mode]) => {
  if (mode !== 'single') return;
  if (isUpdatingEndTime.value) return; // avoid reacting while we're updating end_time from duration
  const s = parseTimeToMinutes(startTime as string);
  const e = parseTimeToMinutes(endTime as string);
  if (s === null || e === null) return;
  let diff = e - s;
  if (diff < 0) {
    // If end is before start, do not auto-set negative; reset duration
    diff = 0;
  }
  const hours = diff / 60;
  isUpdatingDuration.value = true;
  form.duration_hours = Number(hours.toFixed(2));
  setTimeout(() => { isUpdatingDuration.value = false; }, 0);
});

function submit() {
  if (editor) {
    form.description = editor.root.innerHTML;
  }
  form.post(route('admin.formations.store'), {
    forceFormData: true,
  });
}
</script>

<template>
  <Head title="Créer une formation" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 bg-white">
      <h2 class="text-2xl font-semibold">Créer une formation</h2>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block text-sm font-medium mb-1">Titre de la formation</label>
          <input v-model="form.title" type="text" class="w-full px-3 py-2 border rounded" placeholder="Ex: Initiation à Excel" />
          <p v-if="form.errors.title" class="text-red-600 text-sm mt-1">{{ form.errors.title }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <div ref="editorRef" class="quill-container"></div>
          <p v-if="form.errors.description" class="text-red-600 text-sm mt-1">{{ form.errors.description }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium mb-1">Image</label>
            <input type="file" accept="image/*" @change="onImageChange" />
            <p class="text-sm text-gray-500">Formats: JPG, PNG. Max 4 Mo.</p>
            <p v-if="form.errors.image" class="text-red-600 text-sm mt-1">{{ form.errors.image }}</p>
            <div v-if="imagePreview" class="mt-2">
              <img :src="imagePreview" alt="Prévisualisation" class="w-40 h-40 object-cover rounded border" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Pièce jointe (PDF)</label>
            <input type="file" accept=".pdf,.doc,.docx,.txt" @change="onAttachmentChange" />
            <p class="text-sm text-gray-500">Optionnel. Max 10 Mo.</p>
            <p v-if="form.errors.attachment" class="text-red-600 text-sm mt-1">{{ form.errors.attachment }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div>
            <label class="block text-sm font-medium mb-1">Prix</label>
            <input v-model.number="form.price" type="number" min="0" step="0.01" class="w-full px-3 py-2 border rounded" placeholder="Ex: 50000" />
            <p v-if="form.errors.price" class="text-red-600 text-sm mt-1">{{ form.errors.price }}</p>
          </div>
            <div>
                <label class="block text-sm font-medium mb-1">Devise</label>
                <select v-model="form.currency" class="w-full px-3 py-2 border rounded">
                    <option value="EUR">Euro (EUR)</option>
                    <option value="FCFA">Franc CFA (FCFA)</option>
                </select>
                <p v-if="form.errors.currency" class="text-red-600 text-sm mt-1">{{ form.errors.currency }}</p>
            </div>

        </div>

        <div>
          <label class="block text-sm font-medium mb-2">Dates de formation</label>
          <div class="flex gap-6 items-center mb-4">
            <label class="flex items-center gap-2">
              <input type="radio" value="single" v-model="form.date_mode" />
              <span>Une seule date</span>
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" value="range" v-model="form.date_mode" />
              <span>Période (début & fin)</span>
            </label>
          </div>

          <div v-if="form.date_mode === 'single'" class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
              <label class="block text-sm font-medium mb-1">Date</label>
              <input v-model="form.date" type="date" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.date" class="text-red-600 text-sm mt-1">{{ form.errors.date }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Heure de début</label>
              <input v-model="form.start_time" type="time" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.start_time" class="text-red-600 text-sm mt-1">{{ form.errors.start_time }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Heure de fin</label>
              <input v-model="form.end_time" type="time" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.end_time" class="text-red-600 text-sm mt-1">{{ form.errors.end_time }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Durée (heures)</label>
              <input v-model.number="form.duration_hours" type="number" min="0" step="0.25" class="w-full px-3 py-2 border rounded" />
              <p class="text-xs text-gray-500">Saisir la durée pour calculer automatiquement l'heure de fin (si l'heure de début est définie).</p>
              <p v-if="form.errors.duration_hours" class="text-red-600 text-sm mt-1">{{ form.errors.duration_hours }}</p>
            </div>
          </div>

          <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
              <label class="block text-sm font-medium mb-1">Date de début</label>
              <input v-model="form.start_date" type="date" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.start_date" class="text-red-600 text-sm mt-1">{{ form.errors.start_date }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Date de fin</label>
              <input v-model="form.end_date" type="date" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.end_date" class="text-red-600 text-sm mt-1">{{ form.errors.end_date }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Durée par jour (heures)</label>
              <input v-model.number="form.duration_per_day_hours" type="number" min="1" class="w-full px-3 py-2 border rounded" />
              <p v-if="form.errors.duration_per_day_hours" class="text-red-600 text-sm mt-1">{{ form.errors.duration_per_day_hours }}</p>
            </div>
          </div>
        </div>

        <div>
          <button type="submit" :disabled="form.processing" class="px-4 py-2 bg-blue-600 text-white rounded">
            {{ form.processing ? 'Enregistrement...' : 'Créer' }}
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>

<style>
.quill-container .ql-editor {
  min-height: 200px;
  max-height: 500px;
  overflow-y: auto;
}
</style>
