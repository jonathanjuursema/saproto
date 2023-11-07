<script setup lang="ts">
import CardComponent from '@/Components/CardComponent.vue';
import { computed } from 'vue';
import moment from 'moment';
import InfoText from '@/Components/InfoText.vue';

const props = defineProps<{
  membership: Member;
}>();

const is_current = computed(() => {
  if (props.membership.until) {
    return moment().isBetween(props.membership.created_at, props.membership.until);
  } else {
    return moment().isAfter(props.membership.created_at);
  }
});
</script>

<template>
  <CardComponent background="bg-back-light" class="max-w-lg">
    <div v-if="!is_current" class="flex items-center justify-between mb-4">
      <b>Current membership</b>
      <InfoText>Since {{ moment(membership.created_at).format('d-m-Y') }}</InfoText>
    </div>
    <div v-else class="flex items-center justify-between mb-4">
      <b>Past Membership</b>
      <InfoText
        >From {{ moment(membership.created_at).format('d-m-Y') }} till
        {{ moment(membership.until).format('d-m-Y') }}</InfoText
      >
    </div>
    <div class="text-sm">
      <div class="flex justify-between">
        <div>
          <span v-if="membership.member_type"> {{ membership.member_type }} member </span>
          <span v-else>
            Membership type undetermined
            <InfoText>Will be determined when membership fee is charged.</InfoText>
          </span>
        </div>
        <div>
          <div v-if="membership.is_pet">🐶 Pet member</div>
          <div v-if="membership.is_lifelong">🕰️ Lifelong member</div>
          <div v-if="membership.is_donor">💸 Generous donor</div>
          <div v-if="membership.is_honorary">😇 Honorary member</div>
          <div v-if="!membership.is_pending">⏲️ Pending member</div>
        </div>
      </div>
    </div>
  </CardComponent>
</template>

<style scoped></style>
