import $locale from 'locale'

const sicesRoutes = [
  {
    path: '/',
    title: 'Dashboard'
  },
  {
    path: '/admin/twig/account',
    title: $locale.twigRoutes.account
  },
  {
    path: '/admin/twig/account/create',
    title: $locale.twigRoutes.account
  },
  {
    path: '/admin/twig/account/:id/show',
    title: $locale.twigRoutes.account
  },
  {
    path: '/admin/twig/account/:id/update',
    title: $locale.twigRoutes.account
  },
  {
    path: '/admin/twig/account/:id',
    title: $locale.twigRoutes.account
  },
  {
    path: '/admin/twig/memorials',
    title: $locale.twigRoutes.memorial
  },
  {
    path: '/admin/twig/memorials/:id/update',
    title: $locale.twigRoutes.memorial
  },
  {
    path: '/admin/twig/memorials/:id/config',
    title: $locale.twigRoutes.memorial,
    sidebar: 'collapse'
  },
  {
    path: '/admin/twig/memorials/create',
    title: $locale.twigRoutes.memorial
  },
  {
    path: '/admin/twig/kit',
    title: $locale.twigRoutes.express
  },
  {
    path: '/admin/twig/stock',
    title: $locale.twigRoutes.stock,
    sidebar: 'collapse'
  },
  {
    path: '/twig/financial-simulator',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/admin/twig/financial-simulator/create',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/admin/twig/financial-simulator/:id/update',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/twig/financial-simulator/:id/simulate',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/admin/twig/orders',
    title: $locale.twigRoutes.order
  },
  {
    path: '/admin/twig/orders/:id/export',
    title: $locale.twigRoutes.order
  },
  {
    path: '/twig/orders/:id/file/:type',
    title: $locale.twigRoutes.archive
  },
  {
    path: '/admin/twig/users',
    title: $locale.twigRoutes.userSices
  },
  {
    path: '/admin/twig/users/update/:id',
    title: $locale.twigRoutes.userSices
  },
  {
    path: '/admin/twig/users/create',
    title: $locale.twigRoutes.userSices
  },
  {
    path: '/admin/twig/users/:id',
    title: $locale.twigRoutes.userSices
  },
  {
    path: '/admin/twig/payment-methods',
    title: $locale.twigRoutes.paymentMethods
  },
  {
    path: '/admin/twig/insurance',
    title: $locale.twigRoutes.insurance
  },
  {
    path: '/admin/twig/media',
    title: $locale.twigRoutes.mediaManagement
  },
  {
    path: '/admin/twig/settings',
    title: $locale.twigRoutes.parameters
  },
  {
    path: '/twig/contact/:context',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/:token/show',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/create',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/:token/update',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/dashboard',
    title: 'Dashboard'
  },
  {
    path: '/twig/member',
    title: $locale.twigRoutes.user
  },
  {
    path: '/twig/ranking',
    title: $locale.twigRoutes.ranking,
    sidebar: 'collapse'
  },
  {
    path: '/twig/reports',
    title: $locale.twigRoutes.reports
  },
  {
    path: '/admin/twig/kit/create',
    title: $locale.twigRoutes.express
  },
  {
    path: '/admin/twig/kit/:id/update',
    title: $locale.twigRoutes.express
  },
  {
    path: '/twig/orders/:id/show',
    title: $locale.twigRoutes.myOrders
  },
  {
    path: '/twig/member/timezone',
    title: $locale.twigRoutes.timezone
  },
  {
    path: '/twig/member/profile',
    title: $locale.twigRoutes.userSetting
  },
  {
    path: '/twig/cart/show',
    title: $locale.twigRoutes.cart
  },
  {
    path: '/twig/purchase/list_cart_pool',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/cart_pool/:id',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/cart_pool_info/:id',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/payment_feedback',
    title: $locale.twigRoutes.bought
  },
  {
    path: '/twig/component/mlpe',
    title: $locale.twigRoutes.mlpes
  },
  {
    path: '/twig/component/mlpe/:id/update',
    title: $locale.twigRoutes.mlpes
  },
  {
    path: '/twig/component/mlpe/create',
    title: $locale.twigRoutes.mlpes
  },
  {
    path: '/twig/component/module/:id/update',
    title: $locale.twigRoutes.modules
  },
  {
    path: '/twig/component/module/create',
    title: $locale.twigRoutes.modules
  },
  {
    path: '/twig/component/inverter',
    title: $locale.twigRoutes.inverter
  },
  {
    path: '/twig/component/inverter/:id/update',
    title: $locale.twigRoutes.inverter
  },
  {
    path: '/twig/component/inverter/create',
    title: $locale.twigRoutes.inverter
  },
  {
    path: '/twig/component/structure',
    title: $locale.twigRoutes.mountingSystems
  },
  {
    path: '/twig/component/structure/:id/update',
    title: $locale.twigRoutes.mountingSystems
  },
  {
    path: '/twig/component/structure/create',
    title: $locale.twigRoutes.mountingSystems
  },
  {
    path: '/twig/component/stringbox',
    title: $locale.twigRoutes.stringBoxes
  },
  {
    path: '/twig/component/stringbox/:id/update',
    title: $locale.twigRoutes.stringBoxes
  },
  {
    path: '/twig/component/stringbox/create',
    title: $locale.twigRoutes.stringBoxes
  },
  {
    path: '/twig/component/variety',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/component/variety/:id/update',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/component/variety/create',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/component/maker',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/component/maker/update/:id',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/component/maker/create',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/project',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/:id/manage',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/generator/',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/generator/:id',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/create',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/:id/update',
    title: $locale.twigRoutes.project
  },
  {
    path: '/twig/project/financial/:id',
    title: $locale.twigRoutes.analysis
  },
  {
    path: '/twig/proposal/:id/editor',
    title: $locale.twigRoutes.proposal
  },
  {
    path: '/twig/tasks/m',
    title: $locale.twigRoutes.tasks
  },
  {
    path: '/twig/tasks/m/calendar',
    title: $locale.twigRoutes.tasks
  }
]

const integradorRoutes = [
  {
    path: '/',
    title: 'Dashboard'
  },
  {
    path: '/twig/dashboard',
    title: 'Dashboard'
  },
  {
    path: '/twig/cart/checkout',
    title: $locale.twigRoutes.checkout
  },
  {
    path: '/twig/cart/show',
    title: $locale.twigRoutes.cart
  },
  {
    path: '/twig/component/maker',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/component/maker/create',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/component/maker/update/:id',
    title: $locale.twigRoutes.manufacturers
  },
  {
    path: '/twig/component/mlpe',
    title: $locale.twigRoutes.mlpes
  },
  {
    path: '/twig/component/module',
    title: $locale.twigRoutes.modules
  },
  {
    path: '/twig/component/module/:id/update',
    title: $locale.twigRoutes.modules
  },
  {
    path: '/twig/component/module/create',
    title: $locale.twigRoutes.modules
  },
  {
    path: '/twig/component/inverter',
    title: $locale.twigRoutes.inverters
  },
  {
    path: '/twig/component/inverter/:id/update',
    title: $locale.twigRoutes.inverters
  },
  {
    path: '/twig/component/inverter/create',
    title: $locale.twigRoutes.inverters
  },
  {
    path: '/twig/component/stringbox',
    title: $locale.twigRoutes.stringBox
  },
  {
    path: '/twig/component/stringbox/:id/update',
    title: $locale.twigRoutes.stringBox
  },
  {
    path: '/twig/component/stringbox/create',
    title: $locale.twigRoutes.stringBox
  },
  {
    path: '/twig/component/structure',
    title: $locale.twigRoutes.structures
  },
  {
    path: '/twig/component/structure/:id/update',
    title: $locale.twigRoutes.structures
  },
  {
    path: '/twig/component/structure/create',
    title: $locale.twigRoutes.structures
  },
  {
    path: '/twig/component/variety',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/component/variety/:id/update',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/component/variety/create',
    title: $locale.twigRoutes.varieties
  },
  {
    path: '/twig/contact/:context',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/:token/show',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/:token/update',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/contact/:context/create',
    title: $locale.twigRoutes.contact
  },
  {
    path: '/twig/item',
    title: $locale.twigRoutes.myItems
  },
  {
    path: '/twig/kit',
    title: $locale.twigRoutes.kits
  },
  {
    path: '/twig/member',
    title: $locale.twigRoutes.users
  },
  {
    path: '/twig/member/business',
    title: $locale.twigRoutes.myBusiness
  },
  {
    path: '/twig/member/profile',
    title: $locale.twigRoutes.myData
  },
  {
    path: '/twig/member/timezone',
    title: $locale.twigRoutes.timezone
  },
  {
    path: '/twig/financial-simulator',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/twig/financial-simulator/:id/simulate',
    title: $locale.twigRoutes.loanCalculator
  },
  {
    path: '/twig/orders',
    title: $locale.twigRoutes.myOrders
  },
  {
    path: '/twig/orders/:id/show',
    title: $locale.twigRoutes.myOrders
  },
  {
    path: '/twig/price',
    title: $locale.twigRoutes.salesTotal
  },
  {
    path: '/twig/project',
    title: $locale.twigRoutes.projects
  },
  {
    path: '/twig/project/:id/manage',
    title: $locale.twigRoutes.projects
  },
  {
    path: '/twig/project/:id/update',
    title: $locale.twigRoutes.projects
  },
  {
    path: '/twig/project/create',
    title: $locale.twigRoutes.projects
  },
  {
    path: '/twig/project/financial/:id',
    title: $locale.twigRoutes.analysis
  },
  {
    path: '/twig/project/generator/:id',
    title: $locale.twigRoutes.projects
  },
  {
    path: '/twig/purchase/cart_pool/:id',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/cart_pool_info/:id',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/list_cart_pool',
    title: $locale.twigRoutes.transactions
  },
  {
    path: '/twig/purchase/payment_feedback',
    title: $locale.twigRoutes.bought
  },
  {
    path: '/twig/ranking',
    title: $locale.twigRoutes.loyaltyProgram,
    sidebar: 'collapse'
  },
  {
    path: '/settings/twig/nasa',
    title: $locale.twigRoutes.weather,
    sidebar: 'collapse'
  },
  {
    path: '/settings/twig/categories/contact_category/',
    title: $locale.twigRoutes.categories
  },
  {
    path: '/settings/twig/categories/sale_stage/',
    title: $locale.twigRoutes.saleStage
  },
  {
    path: '/twig/tasks/m',
    title: $locale.twigRoutes.tasks
  },
  {
    path: '/twig/tasks/m/calendar',
    title: $locale.twigRoutes.tasks
  },
  {
    path: '/twig/project/generator/',
    title: $locale.twigRoutes.proposal
  },
  {
    path: '/twig/proposal/:id/editor',
    title: $locale.twigRoutes.proposal,
    sidebar: 'collapse'
  }
]

export default process.env.CLIENT === 'sices'
  ? sicesRoutes
  : integradorRoutes
