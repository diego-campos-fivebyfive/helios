import Account from '@/pages/account'
import AccountForm from '@/pages/account/form'
import Coupon from '@/pages/coupon'
import Metric from '@/pages/metric'
import NotFound from '@/pages/notfound'

const RouterView = {
  template: '<router-view></router-view>'
}

export default [
  {
    path: '/account',
    name: 'Contas',
    component: RouterView,
    children: [
      {
        path: '',
        component: Account
      },
      {
        path: 'create',
        component: AccountForm
      }
    ]
  },
  {
    path: '/coupon',
    redirect: 'not-found',
    name: 'Cupons',
    component: Coupon
  },
  {
    path: '/metrics',
    name: 'Métricas',
    component: Metric
  },
  {
    path: '*',
    name: 'not-found',
    component: NotFound,
    meta: {
      sidebar: 'none',
      mainbar: 'none'
    }
  }
]
