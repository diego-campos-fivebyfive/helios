import Account from '@/pages/account'
import AccountForm from '@/pages/account/form'
import Coupon from '@/pages/coupon'
import Memorial from '@/pages/memorial'
import MemorialConfig from '@/pages/memorial/form'
import Messenger from '@/pages/messenger'
import Metric from '@/pages/metric'
import NotFound from '@/pages/notfound'
import Terms from '@/pages/terms'
import TermsOfUse from '@/pages/terms-of-use'

const RouterView = {
  template: '<router-view></router-view>'
}

const FrameView = {
  template: '<div :style="frameWrapper"><iframe :style="frame" :src="getRoute"></iframe></div>',
  data: () => ({
    frame: {
      border: 0,
      height: '100%',
      width: '100%'
    },
    frameWrapper: {
      height: '100%',
      overflow: 'hidden'
    }
  }),
  computed: {
    getRoutePath() {
      const currentPath = this.$route.path
      const homePath = '/dashboard'
      return (currentPath === '/') ? homePath : currentPath
    },
    getRoute() {
      const twigBaseUri = `${process.env.API_URL}/twig`
      const routePath = this.getRoutePath
      return `${twigBaseUri}${routePath}`
    }
  }
}

export const routes = [
  {
    path: '/',
    name: 'Dashboard',
    component: FrameView
  },
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
    name: 'Cupons',
    component: Coupon
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: FrameView
  },
  {
    path: '/memorial',
    name: 'Memoriais',
    component: RouterView,
    children: [
      {
        path: '',
        component: Memorial
      },
      {
        path: ':id/config',
        component: MemorialConfig,
        meta: {
          sidebar: 'collapse'
        }
      }
    ]
  },
  {
    path: '/messenger',
    name: 'Mensagens',
    component: Messenger
  },
  {
    path: '/metrics',
    name: 'Métricas',
    component: Metric
  },
  {
    path: '/terms-of-use',
    name: 'Termos de Uso',
    component: TermsOfUse
  },
  {
    path: '/terms',
    name: 'Termos de Uso',
    component: Terms
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
