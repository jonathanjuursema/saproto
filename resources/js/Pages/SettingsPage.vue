<script setup lang="ts">
import Layout from '@/Layout/GenericLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import PersonalDetails from './Settings/PersonalDetails.vue';
import NavPill from '@/Components/NavPill.vue';
import { ref } from 'vue';
import type { Component } from 'vue';
import Membership from '@/Pages/Settings/MembershipSettings.vue';
import Card from '@/Components/CardComponent.vue';

const props = defineProps<{
  user: User;
  memberships: {
    previous: Array<Member>;
    pending: Array<Member>;
  };
  settingsPage: string | null;
}>();

const settings: Record<string, { name: string; component: Component }> = {
  personal: { name: 'Personal details', component: PersonalDetails },
  membership: { name: 'Membership', component: Membership },
};

const setting = ref(props.settingsPage ? props.settingsPage : Object.keys(settings)[0]);

function openSettings(newSetting: string) {
  setting.value = newSetting;
}
</script>

<template>
  <Layout>
    <Head title="Dashboard" />
    <Card>
      <template #header> Dashboard </template>
      <div class="flex justify-evenly space-x-8">
        <div class="flex-col space-y-2">
          <NavPill
            v-for="(page, key) in settings"
            :key="key"
            :active="setting === key"
            @click="router.get(route('user::dashboard') + `/${key}`)"
          >
            {{ page.name }}
          </NavPill>
        </div>
        <div class="flex-grow">
          <component :is="settings[setting].component" :user="user" :memberships="memberships" />
        </div>
      </div>
    </Card>
  </Layout>
</template>
