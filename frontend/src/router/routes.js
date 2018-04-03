import Account from '@/pages/account'
import Coupon from '@/pages/coupon'
import Metric from '@/pages/metric'
import NotFound from '@/pages/notfound'

export default [
  {
    path: '/account',
    name: 'Contas',
    component: Account
  },
  {
    path: '/coupon',
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
