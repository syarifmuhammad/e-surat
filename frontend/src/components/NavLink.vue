<script setup>
import { RouterLink, useRoute, useRouter } from 'vue-router';
import { Icon } from '@iconify/vue';
import { useUserStore } from '@/stores/user';

const route = useRoute()
const router = useRouter()

const props = defineProps({
    title: String,
    name: String,
    icon: String,
    child: Array,
})

function check_route_can_access(name) {
    if (router.hasRoute(props.name)) {
        const theroute = router.resolve({ name: props.name })
        const can_accessed = theroute.meta.can_accessed.some(roles => roles === '*' || roles === useUserStore().user.roles)
        if (!can_accessed) {
            return false
        }
    }
    return true
}

</script>
<template>
    <template v-if="props.child.length < 1">
        <li>
            <RouterLink :to="{ name: props.name }" v-if="check_route_can_access(props.name)"
                :class="route.name === props.name ? 'bg-primary-400/20 text-primary-400 font-semibold' : 'hover:text-primary-400'"
                class="hs-accordion-toggle flex items-center gap-x-3.5 py-2 px-2.5 text-base text-black rounded-md">
                <Icon class="min-w-[24px] min-h-[24px]" :icon="props.icon" />
                {{ props.title }}
            </RouterLink>
        </li>
    </template>
    <template v-else>
        <li class="hs-accordion" v-if="props.child.some(c => check_route_can_access(c.name))" :class="{
            'active': props.child.some(c => c.name == route.name)
        }" :id="`${props.name}-accordion`">
            <span :class="props.child.some(c => c.name == route.name) ? 'text-primary-400' : 'hover:text-primary-400'"
                class="hs-accordion-toggle flex items-center gap-x-3.5 py-2 px-2.5 text-base text-black rounded-md cursor-pointer">
                <Icon class="min-w-[24px] min-h-[24px]" :icon="props.icon" />
                {{ props.title }}
                <Icon class="hs-accordion-active:block text-xl ml-auto hidden" icon="tabler:chevron-up" />
                <Icon class="hs-accordion-active:hidden text-xl ml-auto block" icon="tabler:chevron-down" />
            </span>
            <div :id="`${props.accordionName}-accordion-child`"
                class="hs-accordion-content w-full overflow-hidden transition-[height] duration-300" :class="{
                    'hidden': !props.child.some(c => c.name == route.name)
                }">
                <ul class="hs-accordion-group pl-3 pt-2">
                    <template v-for="child in props.child">
                        <li v-if="check_route_can_access(child.name)" class="rounded-md my-2"
                            :class="child.name == route.name ? 'bg-primary-400/20 text-primary-400 font-semibold' : 'hover:text-primary-400'">
                            <RouterLink :to="{ name: child.name }"
                                :class="child.disabled ? 'pointer-events-none cursor-default' : ''"
                                class="flex items-center gap-x-3.5 py-2 px-2.5 text-sm">
                                <Icon class="min-w-[24px] min-h-[24px]" :icon="child.icon" />
                                {{ child.title }}
                                <span v-if="child.disabled"
                                    class="inline-flex items-center py-1 px-2 rounded-full text-xs font-medium bg-red-100 text-red-800">disabled
                                </span>
                            </RouterLink>
                        </li>
                    </template>
                </ul>
            </div>
        </li>
    </template>
</template>