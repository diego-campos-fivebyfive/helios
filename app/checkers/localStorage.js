import { http } from 'apis'

const setUserInfoInLocalStorage = userInfo => {
  Object
    .entries(userInfo)
    .forEach(([itemName, itemValue]) => {
      localStorage.setItem(itemName, (Object(itemValue) === itemValue)
        ? JSON.stringify(itemValue)
        : itemValue
      )
    })
}

const userToken = localStorage.getItem('userToken')

if (process.env.NODE_ENV === 'development' || !userToken) {
  http.get('/api/v1/user')
    .then(({ data: userInfo }) => (
      setUserInfoInLocalStorage(userInfo),
      userInfo
    ))
    .then(userInfo => {
      if (userInfo.userToken !== userToken) {
        location.reload()
        return
      }

      const userType = localStorage.getItem('userSices') === 'true'
        ? 'sices'
        : 'integrador'

      if (userType !== process.env.CLIENT) {
        throw new Error(`
          Error: ${userType} user logged in web-${process.env.CLIENT} client.
        `)
      }
    })
}
