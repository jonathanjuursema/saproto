<script setup lang="ts">
import {router, useForm, usePage} from '@inertiajs/vue3';
import {computed, onUnmounted} from 'vue';
import InputGroup from '@/Components/Input/InputGroup.vue';
import SolidButton from '@/Components/SolidButton.vue';
import InfoText from '@/Components/InfoText.vue';
import InputField from '@/Components/Input/InputField.vue';
import InputType from '@/types/InputType.d.ts';

const page = usePage();

const themes = computed(() => page.props.themes);

const props = defineProps<{
  user: User;
  memberships: {
    previous: Array<Member>;
    pending: Array<Member>;
  };
}>();

const defaultFormData = computed(() => {
  return {
    show_birthday: props.user.show_birthday,
    phone: props.user.phone,
    phone_visible: props.user.phone_visible,
    receive_sms: props.user.receive_sms,
    website: props.user.website,
    show_omnomcom_total: props.user.show_omnomcom_total,
    show_omnomcom_calories: props.user.show_omnomcom_calories,
    disable_omnomcom: props.user.disable_omnomcom,
    keep_omnomcom_history: props.user.keep_omnomcom_history,
    theme: props.user.theme,
    show_achievements: props.user.show_achievements,
    profile_in_almanac: props.user.profile_in_almanac,
  };
});

const form = useForm('PersonalDetails', defaultFormData.value);

onUnmounted(
    router.on('before', (event) => {
      if (form.isDirty && event.detail.visit.method !== 'post') {
        return confirm('You have unsaved data, are you sure you want to leave?');
      }
    })
);
const postForm = () => {
  if (form.isDirty) {
    form.post(route('user::dashboard'), {
      preserveScroll: true,
      onSuccess: () => {
        form.defaults(defaultFormData.value);
        form.reset();
      },
    });
  }
};
</script>

<template>
  <div>
    <h1 class="text-4xl">Personal details</h1>
    <hr class="mb-4"/>
    <div class="flex space-x-8">
      <form @submit.prevent="postForm">
        <InputGroup name="name">
          Name
          <template #input>
            <InputField :value="`${user.name} (${user.calling_name})`" disabled></InputField>
          </template>
        </InputGroup>

        <InputGroup name="studies">
          Studies attended
          <template #input>
            <span class="rounded text-white py-1 px-2" :class="user.did_study_create ? 'bg-primary' : 'bg-dark'">
              <i class="fas fa-fw" :class="user.did_study_create ? 'fa-check-square' : 'fa-xmark-square'"></i>
              Creative Technology
            </span>
            <span
                class="rounded text-white py-1 px-2 ml-2"
                :class="user.did_study_itech ? 'bg-primary' : 'bg-back-light'"
            >
              <i class="fas fa-fw" :class="user.did_study_itech ? 'fa-check-square' : 'fa-xmark-square'"></i>
              Interaction Technology
            </span>
            <InfoText>Is this incorrect? Let the board know.</InfoText>
          </template>
        </InputGroup>

        <InputGroup name="birthdate">
          Birthdate
          <template #input>
            <InputField name="birthdate" disabled :value="user.birthdate"/>
            <InputField
                v-if="user.birthdate"
                v-model="form.show_birthday"
                :type="InputType.Checkbox"
                name="show_birthday"
                class="mb-4"
            >
              Show to members
            </InputField>
          </template>
        </InputGroup>

        <InputGroup name="edu_username">
          University account
          <template #input>
            <InputField
                name="edu_username"
                disabled
                :value="user.utwente_username ?? user.edu_username ?? 'Not linked'"
                :after-variant="user.edu_username ? 'danger' : 'info'"
                after-hover
            >
              <template #after>
                <a v-if="user.edu_username" class="px-2" :href="route('user::edu::delete')">
                  <i class="fas fa-unlink fa-fw"></i>
                </a>
                <a v-else class="px-2" :href="route('user::edu::add')">
                  <i class="fas fa-user-plus fa-fw"></i>
                </a>
              </template>
            </InputField>
          </template>
        </InputGroup>

        <InputGroup name="address">
          Address
          <template #input>
            <InputField
                name="address"
                disabled
                :value="user.address ? `${user.address.street} ${user.address.number}` : 'Let us know your address'"
                :after-variant="user.address ? (user.is_member ? 'info' : 'danger') : 'primary'"
                after-hover
            >
              <template #after>
                <a v-if="!user.address" class="px-2" :href="route('user::address::add')">
                  <i class="far fa-add fa-fw"></i>
                </a>
                <template v-else>
                  <a class="px-2" :href="route('user::address::edit')">
                    <i class="far fa-edit fa-fw"></i>
                  </a>
                  <a v-if="!user.is_member" class="px-2" :href="route('user::address::delete')">
                    <i class="fas fa-trash fa-fw"></i>
                  </a>
                </template>
              </template>
            </InputField>
          </template>
          <template #info>
            <span v-if="user.address_visible"><i class="fas fa-user-friends fa-fw me-2"></i> Visible to members</span>
            <span v-else><i class="fas fa-user-lock fa-fw me-2"></i> Visible to the board</span>
          </template>
        </InputGroup>

        <div v-if="user.address" class="mb-4">
          <SolidButton
              as="a"
              :variant="user.address_visible ? 'info' : 'primary'"
              :href="route('user::address::togglehidden')"
          >
            <span v-if="user.address_visible">Hide from members.</span>
            <span v-else> Make visible to members. </span>
          </SolidButton>
        </div>

        <InputGroup name="phone">
          Phone
          <template #input>
            <InputField v-model="form.phone" name="phone"/>
          </template>
        </InputGroup>

        <template v-if="user.phone">
          <InputField v-model="form.phone_visible" :type="InputType.Checkbox" name="phone_visible" class="mb-4"
          >Show to members
          </InputField>
          <InputField v-model="form.receive_sms" :type="InputType.Checkbox" name="receive_sms" class="mb-4"
          >Receive messages
          </InputField>
        </template>

        <InputGroup name="website">
          Website
          <template #input>
            <InputField v-model="form.website" name="website"/>
          </template>
        </InputGroup>

        <InputGroup name="omnomcom">
          OmNomCom
          <template #input>
            <template v-if="user.is_member">
              <div class="flex-row space-y-1">
                <InputField v-model="form.show_omnomcom_total" :type="InputType.Checkbox" name="show_omnomcom_total">
                  After checkout, show how much I've spent today.
                </InputField>
                <InfoText>
                  This feature was requested by members who want to be aware of how much they spend.
                </InfoText>

                <InputField
                    v-model="form.show_omnomcom_calories"
                    :type="InputType.Checkbox"
                    name="show_omnomcom_calories"
                >
                  After checkout, show how many calories I've bought today.
                </InputField>
                <InfoText>
                  This feature was requested by members who want to be aware of how much calories they eat.
                </InfoText>

                <InputField
                    v-model="form.disable_omnomcom"
                    :type="InputType.Checkbox"
                    name="disable_omnomcom"
                    :disabled="form.disable_omnomcom"
                >
                  Don't let me use the OmNomCom.
                </InputField>
                <InfoText
                ><i class="fas fa-warning me-1"></i>
                  Only the board can allow you access to the OmNomCom again.
                </InfoText>
                <InfoText
                ><i class="fas fa-info-circle me-1"></i> You can still sign-up for activities, and the board can
                  manually buy something for you if you need that.
                </InfoText>
                <InfoText>
                  This feature was requested by members who wanted some help controlling their personal spendings.
                </InfoText>
              </div>
            </template>

            <InputField v-model="form.keep_omnomcom_history" :type="InputType.Checkbox" name="keep_omnomcom_history">
              Keep my personal orderline history.
            </InputField>
            <InfoText>
              We are required to keep financial information for 7 years. If you disable this setting, your purchases
              will be anonymised after this time.
            </InfoText>
          </template>
        </InputGroup>

        <InputGroup name="theme">
          Theme
          <template #input>
            <InputField v-model="form.theme" :type="InputType.Select" name="theme" place-holder="Test">
              <option v-for="(theme, key) in themes" :key="key" :value="key">{{ theme }}</option>
            </InputField>
          </template>
          <template #info> This feature was requested by pretty much everyone.</template>
        </InputGroup>

        <InputGroup v-if="user.is_member">
          Privacy
          <template #input>
            <InputField v-model="form.show_achievements" :type="InputType.Checkbox" name="show_achievements">
              Show my achievements on my profile.
            </InputField>
            <InfoText>
              Achievements you obtain may reveal some personal details.<br/>
              Only members can see your achievements.
            </InfoText>

            <InputField v-model="form.profile_in_almanac" :type="InputType.Checkbox" name="profile_in_almanac">
              Use my profile picture in the Lustrum Almanac.
            </InputField>
            <InfoText>
              With this you allow for the use of your profile picture in the Lustrum Alamanac if one will be published
              during your Proto membership.
            </InfoText>
          </template>
        </InputGroup>

        <div class="flex items-center space-x-4">
          <SolidButton type="submit" variant="primary" :disabled="form.processing">Update personal details</SolidButton>
          <InfoText v-if="form.isDirty" text-color="text-warning"><i class="fas fa-warning"></i> Not saved</InfoText>
        </div>
      </form>
      <div class="w-6/12">
        <InputGroup>
          Profile picture
          <template #input>
            <img :src="user.generat"/>
            <InputField :type="InputType.File"/>
          </template>
        </InputGroup>
      </div>
    </div>
  </div>
</template>
