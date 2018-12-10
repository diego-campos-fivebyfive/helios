import {
  getUser,
  setInfoLocalStorage,
  warningUserType
} from './checkers/user'

getUser()
  .then(userInfo => (
    setInfoLocalStorage(userInfo),
    userInfo,
    require('./main')
  ))
  .then(userInfo => {
    warningUserType(userInfo)
  })
