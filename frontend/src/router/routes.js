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

      if (!absolutePath) {
        const twigBaseUri = `${process.env.API_URL}/twig`
        const routePath = this.getRoutePath
        return `${twigBaseUri}${routePath}`
      }

      const viewsEntryPoint = 'twig'
      const viewsEntryPointIndex = absolutePath
        .split('/')
        .filter(segment => segment)
        .findIndex(segment => segment === viewsEntryPoint)

      const currentPathSegments = this.$route.path
        .split('/')
        .filter(segment => segment)

      return currentPathSegments.reduce((acc, segment, segmentIndex) => (
        (segmentIndex === viewsEntryPointIndex)
          ? `${acc}/${viewsEntryPoint}/${segment}`
          : `${acc}/${segment}`
        ), process.env.API_URL)
    }
  }
}

const externalLinks = {
  utils: 'https://suporte.plataformasicessolar.com.br/faq/links-uteis'
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
    component: Account
  },
  {
    path: '/account/create',
    name: 'Cadastro de Conta',
    component: AccountForm
  },
  {
    path: '/admin/account/',
    name: 'Contas',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/account'
    }
  },
  {
    path: '/admin/account/create',
    name: 'Contas',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/account/create'
    }
  },
  {
    path: '/admin/account/:id/update',
    name: 'Contas',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/account/:id/update'
    }
  },
  {
    path: '/admin/account/:id',
    name: 'Contas',
    component: FrameView,
    meta: {
      absolutePath: 'admin/twig/account/:id'
    }
  },
  {
    path: '/admin/memorials',
    name: 'Memoriais',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/memorials'
    }
  },
  {
    path: '/admin/memorials/:id/update',
    name: 'Memoriais',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/memorials/:id/update'
    }
  },
  {
    path: '/admin/memorials/:id/config',
    name: 'Memoriais',
    component: FrameView,
    meta: {
      absolutePath: 'admin/twig/memorials/:id/config'
    }
  },
  {
    path: '/admin/memorials/create',
    name: 'Memoriais',
    component: FrameView,
    meta: {
      absolutePath: 'admin/twig/memorials/create'
    }
  },
  {
    path: '/admin/kit',
    name: 'Sices express',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/kit'
    }
  },
  {
    path: '/admin/stock',
    name: 'Gestão de estoque',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/stock',
      sidebar: 'collapse'
    }
  },
  {
    path: '/admin/orders',
    name: 'Orçamentos',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/orders'
    }
  },
  {
    path: '/admin/orders/:id/export',
    name: 'Orçamentos',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/orders/:id/export'
    }
  },
  {
    path: '/admin/users',
    name: 'Usuários Sices',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/users'
    }
  },
  {
    path: '/admin/users/update/:id',
    name: 'Usuários Sices',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/users/update/:id'
    }
  },
  {
    path: '/admin/users/create',
    name: 'Usuários Sices',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/users/create'
    }
  },
  {
    path: '/admin/users/:id',
    name: 'Usuários Sices',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/users/:id'
    }
  },
  {
    path: '/admin/payment-methods',
    name: 'Condições de pagamento',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/payment-methods'
    }
  },
  {
    path: '/admin/insurance',
    name: 'Seguros',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/insurance'
    }
  },
  {
    path: '/admin/settings',
    name: 'Parâmetros da Plataforma',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/settings'
    }
  },
  {
    path: '/contact/:context',
    name: 'Contato',
    component: FrameView
  },
  {
    path: '/contact/:context',
    name: 'Contato',
    component: FrameView
  },
  {
    path: '/contact/:context/:token/show',
    name: 'Contato',
    component: FrameView
  },
  {
    path: '/contact/:context/create',
    name: 'Contato',
    component: FrameView
  },
  {
    path: '/contact/:context/:token/update',
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
    component: FrameView,
    meta: {
      sidebar: 'collapse'
    }
  },
  {
    path: '/kit',
    name: 'Sices express',
    component: FrameView
  },
  {
    path: '/admin/kit/create',
    name: 'Sices express',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/kit/create'
    }
  },
  {
    path: '/admin/kit/:id/update',
    name: 'Sices express',
    component: FrameView,
    meta: {
      absolutePath: '/admin/twig/kit/:id/update'
    }
  },
  {
    path: '/orders',
    name: 'Meus Pedidos',
    component: FrameView
  },
  {
    path: '/orders/:id/show',
    name: 'Meus Pedidos',
    component: FrameView
  },
  {
    path: '/member/timezone',
    name: 'Fuso Horário',
    component: FrameView
  },
  {
    path: '/member/profile',
    name: 'Meus Dados',
    component: FrameView
  },
  {
    path: '/member/business',
    name: 'Meu Negócio',
    component: FrameView
  },
  {
    path: '/cart/show',
    name: 'Carrinho',
    component: FrameView
  },
  {
    path: '/cart/checkout',
    name: 'Checkout',
    component: FrameView
  },
  {
    path: '/purchase/list_cart_pool',
    name: 'Histórico de Transações',
    component: FrameView
  },
  {
    path: '/purchase/cart_pool/:id',
    name: 'Histórico de Transações',
    component: FrameView
  },
  {
    path: '/purchase/payment_feedback',
    name: 'Compra Efetuada',
    component: FrameView
  },
  {
    path: '/settings/categories/contact_category/',
    name: 'Categorias',
    component: FrameView,
    meta: {
      absolutePath: '/settings/twig/categories/contact_category/'
    }
  },
  {
    path: '/settings/categories/sale_stage/',
    name: 'Etapas de Venda',
    component: FrameView,
    meta: {
      absolutePath: '/settings/twig/categories/sale_stage/'
    }
  },
  {
    path: '/memorial',
    name: 'Memoriais',
    component: Memorial
  },
  {
    path: '/memorial/:id/config',
    name: 'Gestão de Memorial',
    component: MemorialConfig,
    meta: {
      sidebar: 'collapse'
    }
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
    path: '/components/module/:id/update',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/components/module/create',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/components/inverter/:id/update',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/components/inverter/create',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/structure/:id/update',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/structure/create',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/stringbox/:id/update',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/stringbox/create',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/variety/:id/update',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/variety/create',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/maker',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/maker/update/:id',
    name: 'Módulos',
    component: FrameView
  },
  {
    path: '/maker/create',
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
    path: '/project/generator',
    name: 'Projetos',
    component: FrameView,
    meta: {
      absolutePath: '/twig/project/generator/'
    }
  },
  {
    path: '/project/generator/:id',
    name: 'Projetos',
    component: FrameView
  },
  {
    path: '/project/create',
    name: 'Projetos',
    component: FrameView
  },
  {
    path: '/project/:id/update',
    name: 'Projetos',
    component: FrameView
  },
  {
    path: '/tasks/m',
    name: 'Tarefas',
    component: FrameView
  },
  {
    path: '/tasks/m/calendar',
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
    name: 'Termos de Uso ',
    component: Terms
  },
  {
    path: `/${externalLinks.utils}`,
    beforeEnter() {
      window.location = externalLinks.utils
    }
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
