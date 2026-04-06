export default [
    {
        path: '/domains',
        name: 'domains.index',
        component: () => import('./views/DomainIndexView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/create',
        name: 'domains.create',
        component: () => import('./views/DomainCreateView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/:id',
        name: 'domains.show',
        component: () => import('./views/DomainShowView.vue'),
        meta: { auth: true },
    },
    {
        path: '/domains/:id/edit',
        name: 'domains.edit',
        component: () => import('./views/DomainEditView.vue'),
        meta: { auth: true },
    },
]
