<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';

// Props sent from backend
const props = defineProps<{
  mail: {
    MAIL_MAILER: string;
    MAIL_HOST: string;
    MAIL_PORT: string | number;
    MAIL_USERNAME: string;
    MAIL_PASSWORD: string;
    MAIL_ENCRYPTION: string | null;
    MAIL_FROM_ADDRESS: string;
    MAIL_FROM_NAME: string;
  }
}>();

const form = useForm({
  MAIL_MAILER: props.mail.MAIL_MAILER || 'smtp',
  MAIL_HOST: props.mail.MAIL_HOST || '',
  MAIL_PORT: String(props.mail.MAIL_PORT || '587'),
  MAIL_USERNAME: props.mail.MAIL_USERNAME || '',
  MAIL_PASSWORD: props.mail.MAIL_PASSWORD || '',
  MAIL_ENCRYPTION: props.mail.MAIL_ENCRYPTION ?? 'tls',
  MAIL_FROM_ADDRESS: props.mail.MAIL_FROM_ADDRESS || '',
  MAIL_FROM_NAME: props.mail.MAIL_FROM_NAME || '',
});

const submit = () => {
  // Ensure PORT is numeric for backend validator but form uses strings
  form.transform((data) => ({
    ...data,
    MAIL_PORT: Number(data.MAIL_PORT || 0),
  })).post(route('admin.mail-settings.update'));
};
</script>

<template>
  <AppLayout>
    <Head title="SMTP Settings" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h1 class="text-2xl font-semibold mb-6">Configuration SMTP</h1>
            <p class="text-sm text-gray-600 mb-6">
              Configurez ici les paramètres SMTP. Ces valeurs seront écrites dans le fichier .env et appliquées immédiatement.
            </p>

            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="col-span-1">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_MAILER">Mailer</label>
                  <select id="MAIL_MAILER" v-model="form.MAIL_MAILER"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="smtp">SMTP</option>
                    <option value="log">Log</option>
                    <option value="sendmail">Sendmail</option>
                    <option value="array">Array</option>
                    <option value="failover">Failover</option>
                    <option value="mailgun">Mailgun</option>
                    <option value="postmark">Postmark</option>
                    <option value="ses">SES</option>
                    <option value="ses-v2">SES v2</option>
                  </select>
                </div>

                <div class="col-span-1">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_ENCRYPTION">Chiffrement</label>
                  <select id="MAIL_ENCRYPTION" v-model="form.MAIL_ENCRYPTION"
                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                    <option value="tls">TLS (587)</option>
                    <option value="ssl">SSL (465)</option>
                    <option value="starttls">STARTTLS</option>
                    <option value="">Aucun</option>
                  </select>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_HOST">Hôte SMTP</label>
                  <input id="MAIL_HOST" v-model="form.MAIL_HOST" type="text"
                         placeholder="smtp.gmail.com, smtp.mailtrap.io, ..."
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_PORT">Port</label>
                  <input id="MAIL_PORT" v-model="form.MAIL_PORT" type="number" min="1"
                         placeholder="587 pour TLS, 465 pour SSL"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_USERNAME">Nom d'utilisateur</label>
                  <input id="MAIL_USERNAME" v-model="form.MAIL_USERNAME" type="text"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_PASSWORD">Mot de passe</label>
                  <input id="MAIL_PASSWORD" v-model="form.MAIL_PASSWORD" type="password"
                         autocomplete="new-password"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_FROM_ADDRESS">Adresse d'expédition</label>
                  <input id="MAIL_FROM_ADDRESS" v-model="form.MAIL_FROM_ADDRESS" type="email"
                         placeholder="hello@example.com"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700" for="MAIL_FROM_NAME">Nom d'expéditeur</label>
                  <input id="MAIL_FROM_NAME" v-model="form.MAIL_FROM_NAME" type="text"
                         placeholder="${APP_NAME}"
                         class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" />
                </div>
              </div>

              <div class="flex items-center justify-end mt-6">
                <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary"
                        :disabled="form.processing">
                  Enregistrer
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
