<template lang="pug">
  a.notify-message(href='/messenger')
    Icon.notify-message-icon(name='envelope')
    label.notify-message-label(v-if='totalOfMessages')
      | {{ totalOfMessages }}
</template>

<script>
  export default {
    data: () => ({
      totalOfMessages: null
    }),
    methods: {
      unreadMessageCount() {
        const uri = '/admin/api/v1/orders/messages/unread_count'

        this.axios.get(uri).then(response => {
          this.totalOfMessages = response.data.unreadMessages
        })
      }
    },
    mounted() {
      this.unreadMessageCount()
    }
  }
</script>

<style lang="scss" scoped>
  .notify-message {
    margin-right: 0.75rem;

    .notify-message-icon {
      display: inline-block;
      z-index: 1;
    }

    .notify-message-label {
      background-color: $ui-orange-light;
      border-radius: 0.25rem;
      color: $ui-white-regular;
      font-size: 0.8rem;
      font-weight: 600;
      padding: 0.25rem;
      position: absolute;
      right: 7.75rem;
      top: 0.8rem;
      z-index: 2;
    }
  }

  a {
    color: $ui-gray-regular;
  }
</style>
