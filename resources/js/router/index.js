import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const routes = [
    // ── Guest ────────────────────────────────────────────────────────────────
    {
        path: '/login',
        name: 'login',
        component: () => import('@/views/auth/LoginView.vue'),
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: () => import('@/views/auth/RegisterView.vue'),
        meta: { guest: true },
    },

    // ── Authenticated ─────────────────────────────────────────────────────────
    {
        path: '/',
        redirect: { name: 'dashboard' },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: () => import('@/views/DashboardView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains',
        name: 'domains.index',
        component: () => import('@/views/domains/DomainIndexView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/create',
        name: 'domains.create',
        component: () => import('@/views/domains/DomainCreateView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/:id',
        name: 'domains.show',
        component: () => import('@/views/domains/DomainShowView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/:id/edit',
        name: 'domains.edit',
        component: () => import('@/views/domains/DomainEditView.vue'),
        meta: { auth: true },
    },

    // ── 404 ──────────────────────────────────────────────────────────────────
    {
        path: '/:pathMatch(.*)*',
        name: 'not-found',
        component: () => import('@/views/NotFoundView.vue'),
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior: () => ({ top: 0 }),
})

router.beforeEach(async (to) => {
    const auth = useAuthStore()

    // Resolve current user once on first navigation
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
