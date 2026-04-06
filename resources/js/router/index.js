import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/modules/auth/store'
import authRoutes from '@/modules/auth/routes'
import dashboardRoutes from '@/modules/dashboard/routes'
import domainRoutes from '@/modules/domain/routes'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...authRoutes,
        ...dashboardRoutes,
        ...domainRoutes,
        {
            path: '/:pathMatch(.*)*',
            name: 'not-found',
            component: () => import('@/shared/views/NotFoundView.vue'),
        },
    ],
    scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
    const auth = useAuthStore()

    if (!auth.initialized) {
        await auth.fetchUser()
    }

    if (to.meta.auth && !auth.isAuthenticated) {
        return { name: 'login', query: { redirect: to.fullPath } }
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return { name: 'dashboard' }
    }
})

export default router
