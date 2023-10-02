<script setup lang="ts">
import Layout from '@/Layout/GenericLayout.vue';
import { Head } from '@inertiajs/vue3';
import PersonalDetails from './Settings/PersonalDetails.vue';
import NavPill from '@/Components/NavPill.vue';
import { ref } from 'vue';
import Membership from '@/Pages/Settings/MembershipSettings.vue';
import Card from '@/Components/CardComponent.vue';

defineProps<{
  user: User;
  memberships: {
    previous: Array<Member>;
    pending: Array<Member>;
  };
}>();

const settings = {
  'Personal details': PersonalDetails,
  Membership: Membership,
};

const setting = ref(Object.keys(settings)[0]);

function openSettings(newSetting) {
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
          <NavPill v-for="(page, name) in settings" :key="name" :active="setting === name" @click="openSettings(name)">
            {{ name }}
          </NavPill>
        </div>
        <div class="flex-grow">
          <component :is="settings[setting]" :user="user" :memberships="memberships" />
        </div>
      </div>
    </Card>
  </Layout>
</template>
