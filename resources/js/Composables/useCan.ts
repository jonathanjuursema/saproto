import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import type { Ref } from 'vue';
import type { Page, PageProps } from '@inertiajs/core';

export function useCan() {
  const page: Page<PageProps> = usePage();
  const userPermissions: Ref<Record<string, boolean>> = computed(() => page.props.auth.permissions);

  const can = (permission: string): boolean => {
    return Boolean(userPermissions.value[permission]);
  };

  const canAny = (permissions: Array<string>): boolean => {
    return permissions.some((permission: string) => {
      return userPermissions.value[permission];
    });
  };

  const canAll = (permissions: Array<string>): boolean => {
    return permissions.every((permission: string) => {
      return userPermissions.value[permission];
    });
  };

  const canNot = (permission: string): boolean => {
    return !can(permission);
  };

  return { can, canNot, canAny, canAll };
}
