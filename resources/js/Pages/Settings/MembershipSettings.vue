<script setup lang="ts">
import moment from 'moment';

defineProps<{
  user: User;
  memberships: {
    previous: Array<Member>;
    pending: Array<Member>;
  };
}>();
</script>

<template>
  <div>
    <h1>Membership</h1>
    <hr />
    <table class="table table-borderless table-sm mb-2">
      <tbody>
        <template v-if="user.is_member">
          <tr>
            <th>Member since</th>
            <td>
              <span v-if="moment(user.member.created_at).unix() > 0">
                {{ moment(user.member.created_at).format('MMMM D, Y') }}
              </span>
              <span v-else> Before we kept track </span>
            </td>
          </tr>
          <tr v-if="user.member.until">
            <th><b>Member until</b></th>
            <td>
              <span class="badge rounded-pill bg-danger">
                {{ moment(user.member.until).format('d-m-Y') }}
              </span>
            </td>
          </tr>
          <tr>
            <th>Proto username</th>
            <td>{{ user.member.proto_username }}</td>
          </tr>
          <template v-if="user.is_active_member">
            <tr>
              <th>Active in committee(s)</th>
              <td>Yes! <i class="far fa-thumbs-up"></i></td>
            </tr>
            <tr>
              <th>Member e-mail</th>
              <td>
                {{ user.member.proto_username }}<span class="text-muted">@</span
                ><span class="text-muted">{{ "config('proto.emaildomain')" }}</span
                ><br />
                <sup class="text-muted">Forwards to {{ user.email }}</sup>
              </td>
            </tr>
          </template>
          <tr>
            <th>Membership type</th>
            <td v-if="user.member.member_type">
              {{ user.member.member_type }} member
              <br />
              <sup class="text-muted"
                >€ {{ user.member.membership_orderline.total_price }} was paid on
                {{ moment(user.member.membership_orderline.created_at).format('F j, Y') }}</sup
              >
            </td>
            <td v-else>
              Not yet determined
              <br />
              <sup class="text-muted">Will be determined when membership fee is charged for this year.</sup>
            </td>
          </tr>
          <tr v-if="user.member.is_honorary || user.member.is_donor || user.member.is_lifelong || user.member.is_pet">
            <th>Special status</th>
            <td>
              <span v-if="user.member.is_honorary" class="badge rounded-pill bg-primary">
                Honorary member! <i class="fas fa-trophy ms-1"></i>
              </span>

              <span v-if="user.member.is_donor" class="badge rounded-pill bg-primary">
                Donor <i class="far fa-hand-holding-usd ms-1"></i>
              </span>

              <span v-if="user.member.is_lifelong" class="badge rounded-pill bg-primary">
                Lifelong member <i class="fas fa-clock s-1"></i>
              </span>

              <span v-if="user.member.is_pet" class="badge rounded-pill bg-primary">
                Pet member <i class="fas fa-cat ms-1"></i>
              </span>
            </td>
          </tr>
          <tr>
            <th>Current Membership</th>
            Since
            {{
              moment(user.member.created_at).unix() > 0 ? moment(user.member.created_at).format('d-m-Y') : 'forever'
            }}
            <br />
            <td v-if="user.member.membership_form">
              <a
                :href="route('memberform::download::signed', { id: user.member.membership_form_id })"
                class="badge rounded-pill bg-info"
              >
                Download membership form <i class="fas fa-download ms-1"></i>
              </a>
            </td>
            <td v-else>
              <span class="badge rounded-pill bg-warning">
                No digital membership form <i class="fas fa-times-circle ms-1"></i>
              </span>
            </td>
          </tr>
        </template>
        <tr v-if="memberships['previous'].length > 0">
          <th>Previous Membership(s)</th>
          <template v-for="membership in memberships['previous']" :key="membership">
            {{ moment(membership.created_at).unix() > 0 ? moment(membership.created_at).format('d-m-Y') : 'forever' }}
            - {{ moment(membership.deleted_at).format('d-m-Y') }} <br />
            <td v-if="membership.membership_form">
              <a
                :href="route('memberform::download::signed', { id: membership.membership_form_id })"
                class="badge rounded-pill bg-info"
              >
                Download membership form <i class="fas fa-download ms-1"></i>
              </a>
            </td>
            <td v-else>
              <span class="badge rounded-pill bg-warning">
                No digital membership form <i class="fas fa-times-circle ms-1"></i>
              </span>
            </td>
          </template>
        </tr>

        <tr v-if="memberships['pending'].length > 0">
          <th>Pending Membership</th>
          <template v-for="membership in memberships['pending']" :key="membership">
            {{ moment(membership.created_at).unix() > 0 ? moment(membership.created_at).format('d-m-Y') : 'forever' }}
            - {{ moment(membership.deleted_at).format('d-m-Y') }} <br />
            <td v-if="membership.membership_form">
              <a
                :href="route('memberform::download::signed', { id: membership.membership_form_id })"
                class="badge rounded-pill bg-info"
              >
                Download membership form <i class="fas fa-download ms-1"></i>
              </a>
            </td>
            <td v-else>
              <span class="badge rounded-pill bg-warning">
                No digital membership form <i class="fas fa-times-circle ms-1"></i>
              </span>
            </td>
          </template>
        </tr>
      </tbody>
    </table>

    <small>
      If you would like to end your membership, please contact the secretary at
      <a href="mailto:secretary@proto.utwente.nl">secretary@proto.utwente.nl</a>.
    </small>
    <br /><br />
    <h1>SEPA</h1>
    <template v-if="user.bank">
      <table class="table table-borderless table-sm text-muted mb-0">
        <tbody>
          <tr>
            <th>Type</th>
            <td>Recurring</td>
          </tr>
          <tr>
            <th>Issued on</th>
            <td>{{ user.bank.created_at }}</td>
          </tr>
          <tr>
            <th>Authorisation reference</th>
            <td>{{ user.bank.machtigingid }}</td>
          </tr>
          <tr>
            <th>Creditor identifier</th>
            <td>{{ "config('proto.sepa_info')->creditor_id" }}</td>
          </tr>
        </tbody>
      </table>
      <div class="btn-group btn-block">
        <a
          v-if="!user.is_member"
          class="btn btn-outline-danger w-50"
          data-bs-toggle="modal"
          data-bs-target="#bank-modal-cancel"
        >
          Cancel authorization
        </a>

        <a class="btn btn-outline-info w-50" :href="route('user::bank::edit')"> Update authorization </a>
      </div>
    </template>
    <a v-else type="submit" class="btn btn-outline-info btn-block mb-3" :href="route('user::bank::add')">
      Issue SEPA direct withdrawal authorisation
    </a>
    <hr />
  </div>
</template>
