import FrameView from 'helios/template/frameview'

import twigRoutes from './twig'
import externalRoutes from './external'

import { vueRoutes } from './loader'

const serializeTwigRoutes = () =>
  twigRoutes.map(route => ({
    path: route.path.split('twig/').join(''),
    component: Object.assign({}, FrameView),
    meta: Object.assign(route, {
      absolutePath: route.path
    })
  }))

const serializeExternalRoutes = () =>
  externalRoutes.map(link => ({
    path: (link.charAt(0) === '/') ? link : `/${link}`,
    beforeEnter() {
      const newWindow = process.env.PLATFORM !== 'web'
        ? 'location=yes'
        : null

        window.open(link, '_system', newWindow)
    }
  }))

export const routes = [
  ...serializeTwigRoutes(),
  ...serializeExternalRoutes(),
  ...vueRoutes
]
