<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

const props = withDefaults(defineProps<{
    modelValue?: string | null;
    placeholder?: string;
    minHeight?: string;
}>(), {
    modelValue: '',
    placeholder: '',
    minHeight: '100px',
});

const emit = defineEmits<{ (e: 'update:modelValue', value: string): void }>();

const editorEl = ref<HTMLDivElement | null>(null);
let quill: Quill | null = null;
let lastEmitted = '';

// Quill laisse "<p><br></p>" quand le champ est vide : on renvoie une chaîne vide.
function currentHtml(): string {
    if (!quill) return '';
    return quill.getText().trim() === '' ? '' : quill.root.innerHTML;
}

onMounted(() => {
    if (!editorEl.value) return;
    quill = new Quill(editorEl.value, {
        theme: 'snow',
        placeholder: props.placeholder,
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ color: [] }],
                ['link'],
                ['clean'],
            ],
        },
    });

    lastEmitted = props.modelValue || '';
    quill.root.innerHTML = lastEmitted;

    quill.on('text-change', () => {
        lastEmitted = currentHtml();
        emit('update:modelValue', lastEmitted);
    });
});

// Si la valeur change de l'extérieur (ex. suppression d'une offre dans la liste),
// on recharge le contenu de l'éditeur.
watch(() => props.modelValue, (val) => {
    const v = val || '';
    if (quill && v !== lastEmitted) {
        lastEmitted = v;
        quill.root.innerHTML = v;
    }
});

onBeforeUnmount(() => {
    quill?.off('text-change');
    quill = null;
});
</script>

<template>
    <div class="rte-container" :style="{ '--rte-min-height': minHeight }">
        <div ref="editorEl"></div>
    </div>
</template>

<style>
.rte-container .ql-toolbar {
    border-top-left-radius: 0.375rem;
    border-top-right-radius: 0.375rem;
    background-color: #f9fafb;
    border-color: rgb(209 213 219);
}
.rte-container .ql-container {
    border-bottom-left-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
    border-color: rgb(209 213 219);
    background-color: #fff;
    font-family: inherit;
    font-size: 0.875rem;
}
.rte-container .ql-editor {
    min-height: var(--rte-min-height, 100px);
    max-height: 400px;
    overflow-y: auto;
    color: rgb(31 41 55);
}
</style>
