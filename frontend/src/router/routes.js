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
      const { absolutePath } = this.$route.meta

      if (absolutePath) {
        return `${process.env.API_URL}${absolutePath}`
      }

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
    path: '/contact/:id',
    name: 'Contato',
    component: FrameView
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
    path: '/structure',
    name: 'Estruturas',
    component: FrameView
  },
  {
    path: '/stringbox',
    name: 'String Box',
    component: FrameView
  },
  {
    path: '/components/inverter',
    name: 'Inversores',
    component: FrameView
  },
  {
    path: '/variety',
    name: 'Variedades',
    component: FrameView
  },
  {
    path: '/settings/nasa',
    name: 'Dados Climáticos',
    component: FrameView,
    meta: {
      absolutePath: '/settings/twig/nasa'
    }
  },
  {
    path: '/item',
    name: 'Meus Itens',
    component: FrameView
  },
  {
    path: '/member',
    name: 'Usuários',
    component: FrameView
  },
  {
    path: '/ranking',
    name: 'Pontuações',
    component: FrameView
  },
  {
    path: '/kit',
    name: 'Kits Fixos',
    component: FrameView
  },
  {
    path: '/orders',
    name: 'Meus Pedidos',
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
    path: '/components/module',
    name: 'Módulos',
    component: FrameView
  },
  {
      path: '/price',
      name: 'Preço de Venda',
      component: FrameView
  },
  {
    path: '/project',
    name: 'Projetos',
    component: FrameView
  },
  {
    path: '/tasks/m',
    name: 'Tarefas',
    component: FrameView
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
