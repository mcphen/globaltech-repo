<script setup lang="ts">
import TextLink from '@/components/TextLink.vue';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthBase title="Registration Disabled" description="User registration is currently disabled">
        <Head title="Register" />

        <div class="text-center p-6">
            <p class="mb-4">Registration is currently disabled. New accounts are managed by administrators.</p>
            <p class="text-sm text-muted-foreground">
                Already have an account?
                <TextLink :href="route('login')" class="underline underline-offset-4">Log in</TextLink>
            </p>
        </div>
    </AuthBase>
</template>
