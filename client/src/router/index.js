import Vue from 'vue'
import Router from 'vue-router'

Vue.use(Router)

/* Layout */
import Layout from '@/views/layout/Layout'

/** note: submenu only apppear when children.length>=1
*   detail see  https://panjiachen.github.io/vue-element-admin-site/guide/essentials/router-and-nav.html
**/

/**
* hidden: true                   if `hidden:true` will not show in the sidebar(default is false)
* alwaysShow: true               if set true, will always show the root menu, whatever its child routes length
*                                if not set alwaysShow, only more than one route under the children
*                                it will becomes nested mode, otherwise not show the root menu
* redirect: noredirect           if `redirect:noredirect` will no redirct in the breadcrumb
* name:'router-name'             the name is used by <keep-alive> (must set!!!)
* meta : {
    roles: ['admin','editor']     will control the page roles (you can set multiple roles)
    title: 'title'               the name show in submenu and breadcrumb (recommend set)
    icon: 'svg-name'             the icon show in the sidebar,
    noCache: true                if true ,the page will no be cached(default is false)
  }
**/
export const constantRouterMap = [
  { path: '/login', component: () => import('@/views/login/index'), hidden: true },
  { path: '/authredirect', component: () => import('@/views/login/authredirect'), hidden: true },
  { path: '/404', component: () => import('@/views/errorPage/404'), hidden: true },
  { path: '/401', component: () => import('@/views/errorPage/401'), hidden: true },
  {
    path: '',
    component: Layout,
    redirect: 'dashboard',
    meta: {
      roles: ['admin', 'conges', 'prets', 'avances'],
      title: 'Employes',
      icon: 'chart'
    },
    children: [{
      path: 'dashboard',
      component: () => import('@/views/dashboard/index'),
      name: 'dashboard',
      meta: { title: 'Tableau de bord', icon: 'dashboard', noCache: true }
    }]
  }/* ,
  {
    path: '/employes',
    component: Layout,
    redirect: '/employes/index',
    children: [{
      path: 'index',
      component: () => import('@/views/employes/index'),
      name: 'employes',
      meta: { title: 'Employés', icon: 'documentation', noCache: true }
    },
    {
      path: 'inde',
      component: () => import('@/views/employes/index'),
      name: 'employe',
      meta: { title: 'Employés', icon: 'documentation', noCache: true }
    }]
  }, */
]

export default new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({ y: 0 }),
  routes: constantRouterMap
})

export const asyncRouterMap = [
  {
    path: '/employee',
    component: Layout,
    redirect: 'noredirect',
    name: 'employes',
    meta: {
      roles: ['admin'],
      title: 'Employes',
      icon: 'chart'
    },
    children: [
      {
        path: 'new',
        component: () => import('@/views/employee/new/index'),
        name: 'new-employee',
        meta: { title: 'Entrée', noCache: true }
      }, /* ,      {
        path: 'out',
        component: () => import('@/views/employee/out/index'),
        name: 'out-employee',
        meta: { title: "Sortie", noCache: true }
      } */
      {
        path: 'ending-contracts',
        component: () => import('@/views/employee/ending-contracts/index'),
        name: 'contract-ending-employee',
        meta: { title: 'en fin de contrat', noCache: true }
      },
      {
        path: 'list',
        component: () => import('@/views/employee/list/index'),
        name: 'list-employee',
        meta: { title: 'Liste', noCache: true }
      }/* ,
      {
        path: 'options-employee',
        component: () => import('@/views/employee/options/index'),
        name: 'options',
        meta: { title: 'Options', noCache: true }
      } */
    ]
  },
  {
    path: '/leave',
    component: Layout,
    redirect: 'noredirect',
    name: 'conges',
    meta: {
      roles: ['admin', 'conges'],
      title: 'Conges',
      icon: 'chart'
    },
    children: [
      {
        path: 'new',
        component: () => import('@/views/leave/new/index'),
        name: 'new-leave',
        meta: { title: 'Prise de congé', noCache: true }
      },
      {
        path: 'list',
        component: () => import('@/views/leave/list/index'),
        name: 'leaves-list',
        meta: { title: 'Liste des congés', noCache: true }
      },
      {
        path: 'remaining-leaves',
        component: () => import('@/views/leave/remaining/index'),
        name: 'remaining-leaves',
        meta: { title: 'Congés restants', noCache: true }
      }
    ]
  },
  {
    path: '/advance',
    component: Layout,
    redirect: 'noredirect',
    name: 'avances',
    meta: {
      roles: ['admin', 'avances'],
      title: 'Avances',
      icon: 'chart'
    },
    children: [
      {
        path: 'new',
        component: () => import('@/views/advance/new/index'),
        name: 'new-leave',
        meta: { title: 'Prise d\'avance', noCache: true }
      },
      {
        path: 'list',
        component: () => import('@/views/advance/list/index'),
        name: 'leaves-list',
        meta: { title: 'Liste des avances', noCache: true }
      }
    ]
  },
  {
    path: '/loan',
    component: Layout,
    redirect: 'noredirect',
    name: 'loan',
    meta: {
      roles: ['admin', 'prets'],
      title: 'Prets',
      icon: 'chart'
    },
    children: [
      {
        path: 'new',
        component: () => import('@/views/loan/new/index'),
        name: 'new-loan',
        meta: { title: 'Prise de pret', noCache: true }
      },
      {
        path: 'list',
        component: () => import('@/views/loan/list/index'),
        name: 'leaves-list',
        meta: { title: 'Liste des prets', noCache: true }
      }
    ]
  },
  {
    path: '/payroll',
    component: Layout,
    redirect: 'noredirect',
    name: 'payroll',
    meta: {
      roles: ['admin'],
      title: 'Fiche de paie',
      icon: 'chart'
    },
    children: [
      {
        path: 'new',
        component: () => import('@/views/payroll/index'),
        name: 'new-loan',
        meta: { title: 'Generer', noCache: true }
      },
      {
        path: 'groups',
        component: () => import('@/views/payroll/groups'),
        name: 'payroll-groups',
        meta: { title: 'Groupes de paie', noCache: true }
      }
    ]
  },
  /* {
    path: '/tab',
    component: Layout,
    children: [{
      path: 'index',
      component: () => import('@/views/tab/index'),
      name: 'tab',
      meta: { title: 'tab', icon: 'tab' }
    }]
  }, */
  {
    path: '/options',
    component: Layout,
    meta: {
      roles: ['admin'],
    },
    children: [{
      path: 'index',
      component: () => import('@/views/options/index'),
      name: 'options',
      meta: { title: 'Options', icon: 'tab' }
    }]
  },
  {
    path: '/users',
    component: Layout,
    meta: {
      roles: ['admin'],
    },
    children: [{
      path: 'index',
      component: () => import('@/views/users/index'),
      name: 'users',
      meta: { title: 'Utilisateurs', icon: 'tab' }
    }]
  },
  {
    path: '/error-log',
    component: Layout,
    redirect: 'noredirect',
    hidden: true,
    children: [{ path: 'log', component: () => import('@/views/errorLog/index'), name: 'errorLog', meta: { title: 'errorLog', icon: 'bug' }}]
  },
  {
    path: '/aide',
    component: Layout,
    hidden: true,
    children: [{
      path: 'index',
      component: () => import('@/views/aide/index'),
      name: 'aide',
      meta: { title: 'Aide', icon: 'tab' }
    }]
  },
  {
    path: '/profile',
    component: Layout,
    hidden: true,
    children: [{
      path: 'index',
      component: () => import('@/views/profile/index'),
      name: 'profile',
      meta: { title: 'Aide', icon: 'tab' }
    }]
  },

  { path: '*', redirect: '/404', hidden: true }
]
