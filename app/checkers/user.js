import { http } from 'apis'

export const getUser = () => http.get('/api/v1/user')
  .then(({ data: userInfo }) => userInfo )

export const setInfoLocalStorage = userInfo => {
  Object
    .entries(userInfo)
    .forEach(([itemName, itemValue]) => {
      localStorage.setItem(itemName, (Object(itemValue) === itemValue)
        ? JSON.stringify(itemValue)
        : itemValue
      )
    })
}

export const warningUserType = () => userInfo => {
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
}
