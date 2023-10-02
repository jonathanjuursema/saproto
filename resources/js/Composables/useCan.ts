import { usePage } from '@inertiajs/vue3';
import { computed, Ref } from 'vue';

export function useCan() {
  const page: Page<PageProps> = usePage();
  const userPermissions: Ref<Array<boolean>> = computed(() => page.props.auth.permissions);

  const can = (permission: string): boolean => {
    return Boolean(userPermissions.value[permission]);
  };

  const canAny = (permissions: Array<string>): boolean => {
    return permissions.some((permission) => {
      return userPermissions.value[permission];
    });
  };

  const canAll = (permissions: Array<string>): boolean => {
    return permissions.every((permission) => {
      return userPermissions.value[permission];
    });
  };

  const canNot = (permission: string): boolean => {
    return !can(permission);
  };

  return { can, canNot, canAny, canAll };
}
