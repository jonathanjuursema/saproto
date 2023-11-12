<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed, ref, Ref } from 'vue';
import Dropdown from '@/Components/Nav/DropdownMenu.vue';
import NavItem from '@/Components/Nav/NavItem.vue';
import NavLink from '@/Components/Nav/NavLink.vue';
import DropdownLink from '@/Components/Nav/DropdownLink.vue';
import Input from '@/Components/Input/InputField.vue';
import Button from '@/Components/SolidButton.vue';
import { useCan } from '@/Composables/useCan';

const appEnv = import.meta.env.VITE_APP_ENV || 'production';

const page = usePage();

const { can, canNot, canAny, canAll } = useCan();

const menuItems = computed((): Array<MenuItem> => page.props.menuItems);
const user = computed((): User => page.props.auth.user);
const csrf = computed((): string => page.props.csrf);

const mobileOpen: Ref<boolean> = ref(false);
</script>

<template>
  <nav class="bg-primary text-white fixed w-full z-50">
    <div class="mx-auto px-2 sm:px-4 lg:px-4">
      <div class="relative flex h-14 items-center justify-between">
        <div class="flex items-center flex-none justify-center lg:justify-start">
          <div class="flex space-x-4 flex-shrink-0 items-center">
            <div class="flex items-center lg:hidden" @click="mobileOpen = !mobileOpen">
              <!-- Mobile menu button-->
              <button
                type="button"
                class="inline-flex items-center justify-center rounded-md p-2 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                aria-controls="mobile-menu"
                aria-expanded="false"
              >
                <span class="sr-only">Open main menu</span>
                <svg
                  v-if="mobileOpen"
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <svg
                  v-else
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  aria-hidden="true"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                  />
                </svg>
              </button>
            </div>
            <a class="navbar-brand" :href="route('homepage')">
              <template v-if="appEnv != 'production'">
                <i class="fas fa-hammer me-2"></i>
                <span class="uppercase">{{ appEnv }}</span> |
              </template>
              S.A. Proto
            </a>
          </div>
          <div class="hidden lg:ml-6 lg:block">
            <div class="flex space-x-4">
              <template v-for="menuItem in menuItems" :key="menuItem.id">
                <template v-if="!menuItem.is_member_only || user.is_member">
                  <Dropdown v-if="menuItem.children.length > 0">
                    <template #parent>
                      {{ menuItem.menuname }}
                    </template>
                    <template #children>
                      <template v-for="child in menuItem.children" :key="child.id">
                        <DropdownLink
                          v-if="!child.is_member_only || user.is_member"
                          no-inertia
                          :href="child.parsed_url"
                        >
                          {{ child.menuname }}
                        </DropdownLink>
                      </template>
                    </template>
                  </Dropdown>
                  <NavLink v-else no-inertia :href="menuItem.parsed_url">{{ menuItem.menuname }}</NavLink>
                </template>
              </template>

              <template v-if="user">
                <Dropdown v-if="canAny(['omnomcom', 'tipcie', 'drafters'])">
                  <template #parent> OmNomCom</template>
                  <template #children>
                    <DropdownLink>Stores</DropdownLink>
                    <template v-if="can('omnomcom')">
                      <hr />
                      <DropdownLink no-inertia :href="route('omnomcom::orders::adminlist')">Orders</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::products::list')">Products</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::categories::list')">Categories</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::generateorder')"
                        >Generate Supplier Order
                      </DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::products::statistics')"
                        >Sales statistics
                      </DropdownLink>
                    </template>
                    <template v-if="can('tipcie')">
                      <hr />
                      <DropdownLink no-inertia :href="route('dinnerform::add')">Dinnerforms</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::tipcie::orderhistory')"
                        >TIPCie Order Overview
                      </DropdownLink>
                      <DropdownLink no-inertia :href="route('wallstreet::admin')">Wallstreet Drinks</DropdownLink>
                    </template>

                    <template v-if="canNot('board') && canAny(['tipcie', 'omnomcom'])">
                      <hr />
                      <DropdownLink no-inertia :href="route('passwordstore::index')">Password Store</DropdownLink>
                    </template>
                  </template>
                </Dropdown>
                <Dropdown v-if="canAny(['board', 'finadmin', 'alfred'])"
                  >>
                  <template #parent> Admin</template>
                  <template #children>
                    <template v-if="can('board')">
                      <DropdownLink no-inertia :href="route('user::admin::list')">Users</DropdownLink>
                      <DropdownLink no-inertia :href="route('tickets::list')">Tickets</DropdownLink>
                      <DropdownLink no-inertia :href="route('short_url::index')">Short URLs</DropdownLink>
                      <DropdownLink no-inertia :href="route('queries::index')">Queries</DropdownLink>

                      <hr />
                      <DropdownLink class="disabled" no-inertia :href="route('protube::admin')"
                        >ProTube Admin
                      </DropdownLink>
                      <DropdownLink no-inertia :href="route('tempadmin::index')">Temp ProTube Admin</DropdownLink>

                      <template v-if="can('sysadmin')">
                        <hr />
                        <DropdownLink no-inertia :href="route('protube::radio::index')"
                          >ProTube Radio Stations
                        </DropdownLink>
                        <DropdownLink no-inertia :href="route('protube::display::index')"
                          >ProTube Displays
                        </DropdownLink>
                        <DropdownLink no-inertia :href="route('protube::soundboard::index')"
                          >Soundboard Sounds
                        </DropdownLink>
                      </template>

                      <hr />
                      <DropdownLink no-inertia :href="route('committee::add')">Add Committee</DropdownLink>
                      <DropdownLink no-inertia :href="route('event::add')">Add Event</DropdownLink>
                      <DropdownLink no-inertia :href="route('event::category::admin')">Event Categories</DropdownLink>

                      <hr />
                      <DropdownLink no-inertia :href="route('narrowcasting::list')">Narrowcasting</DropdownLink>
                      <DropdownLink no-inertia :href="route('companies::admin')">Companies</DropdownLink>
                      <DropdownLink no-inertia :href="route('joboffers::admin')">Job offers</DropdownLink>
                      <DropdownLink no-inertia :href="route('leaderboards::admin')">Leaderboards</DropdownLink>
                      <DropdownLink no-inertia :href="route('newsletter::show')">Newsletter</DropdownLink>
                    </template>
                    <hr v-if="canAll(['board', 'finadmin'])" />
                    <template v-if="can('finadmin')">
                      <DropdownLink no-inertia :href="route('omnomcom::accounts::list')">Accounts</DropdownLink>
                      <DropdownLink no-inertia :href="route('event::financial::list')">Activities</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::withdrawal::list')">Withdrawals</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::unwithdrawable')">Unwithdrawable</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::mollie::list')">Mollie Payments</DropdownLink>
                      <DropdownLink no-inertia :href="route('omnomcom::payments::statistics')"
                        >Cash & Card Payments
                      </DropdownLink>
                    </template>
                    <template v-if="canAny(['board', 'alfred'])">
                      <hr />
                      <DropdownLink no-inertia :href="route('dmx::index')">Fixtures</DropdownLink>
                      <DropdownLink no-inertia :href="route('dmx::override::index')">Override</DropdownLink>
                    </template>
                    <template v-if="canAny(['alfred', 'sysadmin'])">
                      <hr />
                      <DropdownLink no-inertia :href="route('minisites::isalfredthere::admin')"
                        >Is Alfred There?
                      </DropdownLink>
                    </template>
                  </template>
                </Dropdown>
                <Dropdown v-if="can('board')">
                  <template #parent> Site</template>
                  <template #children>
                    <DropdownLink no-inertia :href="route('menu::list')">Menu</DropdownLink>
                    <DropdownLink no-inertia :href="route('video::admin::index')">Videos</DropdownLink>
                    <DropdownLink no-inertia :href="route('page::list')">Pages</DropdownLink>
                    <DropdownLink no-inertia :href="route('news::admin')">News</DropdownLink>
                    <DropdownLink no-inertia :href="route('email::admin')">Email</DropdownLink>
                    <DropdownLink no-inertia :href="route('achievement::list')">Achievements</DropdownLink>
                    <DropdownLink no-inertia :href="route('leaderboards::admin')">Leaderboards</DropdownLink>
                    <DropdownLink no-inertia :href="route('welcomeMessages::list')">Welcome Messages</DropdownLink>
                    <DropdownLink no-inertia :href="route('newsletter::show')">Weekly Update</DropdownLink>

                    <hr />
                    <DropdownLink no-inertia :href="route('headerimage::index')">Header Images</DropdownLink>
                    <DropdownLink no-inertia :href="route('photo::admin::index')">Photo Admin</DropdownLink>

                    <template v-if="can('sysadmin')">
                      <hr />
                      <DropdownLink no-inertia :href="route('alias::index')">Aliases</DropdownLink>
                      <DropdownLink no-inertia :href="route('announcement::index')">Announcements</DropdownLink>
                      <DropdownLink no-inertia :href="route('authorization::overview')">Authorization</DropdownLink>
                    </template>

                    <hr />
                    <DropdownLink no-inertia :href="route('passwordstore::index')">Password Store</DropdownLink>
                  </template>
                </Dropdown>
                <template v-if="canNot('board')">
                  <template v-if="can('protography')">
                    <Dropdown v-if="can('header-images')">
                      <template #parent> Site</template>
                      <template #children>
                        <DropdownLink no-inertia :href="route('headerimage::index')"> Header Images</DropdownLink>
                        <DropdownLink no-inertia :href="route('photo::admin::index')"> Photo Admin</DropdownLink>
                      </template>
                    </Dropdown>
                    <NavLink v-else no-inertia :href="route('photo::admin::index')">Photo Admin</NavLink>
                  </template>
                  <NavLink v-if="can('registermembers')" no-inertia :href="route('user::registrationhelper::list')">
                    Registration Helper
                  </NavLink>
                </template>
                <NavLink v-if="false && 'leaderboardadmin'" no-inertia :href="route('leaderboards::admin')">
                  Leaderboards Admin
                </NavLink>
              </template>
            </div>
          </div>
        </div>
        <div class="inset-y-0 right-0 flex justify-end space-x-4 items-center pr-2 lg:inset-auto lg:ml-6 lg:pr-0">
          <form method="post" class="flex-auto" :action="route('search')">
            <input type="hidden" name="_token" :value="csrf" />
            <Input name="query" place-holder="Search" class="max-w-xs" after-hover>
              <template #after>
                <button class="px-2 self-stretch"><i class="fas fa-search"></i></button>
              </template>
            </Input>
          </form>

          <Dropdown v-if="user" class="flex-none" no-hover direction="right">
            <template #parent>
              {{ user.calling_name }}
              <img class="inline h-8 w-8 mx-1 rounded-full border-2 border-white" :src="user.photo_preview" alt="" />
            </template>
            <template #children>
              <DropdownLink :href="route('user::dashboard')">Dashboard</DropdownLink>
              <DropdownLink v-if="user.is_member" no-inertia :href="route('user::profile')">My Profile</DropdownLink>
              <DropdownLink v-else no-inertia :href="route('becomeamember')">Become a member</DropdownLink>
              <DropdownLink no-inertia :href="route('protube::dashboard')">ProTube Dashboard</DropdownLink>
              <DropdownLink no-inertia :href="route('omnomcom::orders::list')">Purchase History</DropdownLink>
              <DropdownLink v-if="impersonater" no-inertia :href="route('user::quitimpersonating')"
                >Quit Impersonating
              </DropdownLink>
              <DropdownLink v-else no-inertia :href="route('login::logout')">Logout</DropdownLink>
            </template>
          </Dropdown>
          <template v-else>
            <NavItem>
              <Button no-inertia :href="route('login::register')" variant="info">Register</Button>
            </NavItem>
            <NavItem>
              <Button no-inertia :href="route('login::show')" variant="info">Login</Button>
            </NavItem>
          </template>
        </div>
      </div>
    </div>
    <div v-if="mobileOpen" id="mobile-menu" class="lg:hidden">
      <div class="space-y-1 px-2 pb-3 pt-2">
        <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
        <a href="#" class="bg-gray-900 text-white block rounded-md px-3 py-2 text-base font-medium" aria-current="page"
          >Dashboard</a
        >
        <a
          href="#"
          class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium"
          >Team</a
        >
        <a
          href="#"
          class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium"
          >Projects</a
        >
        <a
          href="#"
          class="text-gray-300 hover:bg-gray-700 hover:text-white block rounded-md px-3 py-2 text-base font-medium"
          >Calendar</a
        >
      </div>
    </div>
  </nav>
  <div class="w-100 h-14"></div>
</template>

<style scoped></style>
