import Vue from 'vue'
import VueSocket from 'vue-socket.io'
import io from 'socket.io-client'

import { pushNotification } from 'apis/notification'

const socketPath = (process.env.NODE_ENV === 'development')
  ? '/socket.io'
  : '/socket/socket.io'

const room = `/${process.env.CLIENT}`

const socket = io(`${process.env.SOCKET_URL}${room}`, {
  path: socketPath,
  query: {
    token: localStorage.getItem('userToken')
  }
})

/* eslint-disable no-console */
export const connect = () => {
  if (process.env.NODE_ENV !== 'production') {
    console.log('socket connected')
  }
}
/* eslint-enable no-console */

Vue.use(VueSocket, socket)

export default {
  pushNotification
}
