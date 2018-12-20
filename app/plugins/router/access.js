import { http } from 'apis'

const hasUserRouteAccess = (routeAllowedRoles, userRoles) => {
  if (!routeAllowedRoles || routeAllowedRoles === '*') {
    return true
  }

  return userRoles.some(userRole => {
    return routeAllowedRoles.some(routeAllowedRole => (
      routeAllowedRole === userRole
    ))
  })
}

const isAccountTermsPassingOrNotRequired = () => {
  if (localStorage.getItem('userSices') === 'true') {
    return Promise.resolve()
  }

  return http.get('/api/v1/terms/checker')
    .then(({ data: termsCheck }) => (
      termsCheck.termsAccepted
      || Promise.reject(new Error('Term not accepted'))
    ))
}

export const checkAccess = (to, from, next) => {
  if (to.meta.public) {
    next()
    return
  }

  if (from.meta.pushState === from.path) {
    Object.assign(from.meta, {
      pushState: null
    })

    next(from.path)
    return
  }

  const userRoles = JSON.parse(localStorage.getItem('userRoles'))
  const userIsLogged = Boolean(userRoles)

  if (!userIsLogged && process.env.PLATFORM !== 'web' && to.path !== '/login') {
    next('/login')
    return
  }

  isAccountTermsPassingOrNotRequired()
    .then(() => {
      if (hasUserRouteAccess(to.meta.allowedRoles, userRoles)) {
        next()
        return
      }

      next('/not-found')
    })
    .catch(() => {
      const termsRoute = userRoles
        .find(userRole => userRole === 'ROLE_OWNER_MASTER')
          ? '/terms'
          : '/terms-warning'

      if (termsRoute === to.path) {
        next()
        return
      }

      next(termsRoute)
    })
}

