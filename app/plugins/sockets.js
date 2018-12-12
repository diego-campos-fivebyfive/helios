import Pusher from 'pusher-js'

const pusher = new Pusher(process.env.PUSHER_KEY, {
  cluster: process.env.PUSHER_CLUSTER,
  forceTLS: true
})

const userToken = localStorage.getItem('userToken')
const userSices = localStorage.getItem('userSices') === 'true'

const channels = {
  user: pusher.subscribe(`user-${userToken}`)
}

if (!userSices) {
  channels['integrador-terms'] = pusher.subscribe('integrador-terms')
}

const install = Vue => {
  Vue.mixin({
    mounted() {
      if (this.$options.sockets) {
        Object.entries(this.$options.sockets)
          .forEach(([name, { handler, channel }]) => {
            if (!channel || !channels[channel]) {
              throw new Error(`Channel not found: ${channel}`)
            }

            channels[channel].bind(name, handler.bind(this))
          })
      }
    }
  })
}

export default {
  install
}
