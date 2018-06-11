<template lang="pug">
  .collection-action-bar
    Button.collection-action-bar-left(
      v-for='button in buttons.left',
      :action='button.click',
      :key='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      class='default-bordered')
      Icon(:name='button.icon')
    Button.collection-action-bar-right(
      v-for='button in buttons.right',
      :action='button.click',
      :key='button.icon',
      :label='button.label || ""',
      :pos='button.position',
      class='default-bordered')
      Icon(:name='button.icon')
    label.collection-action-bar-text
      | Página {{ pagination.current }} de {{ pagination.total }}
</template>

<script>
  export default {
    props: [
      'getMessages',
      'clearCheckedMessages',
      'incrementCheckedMessages',
      'messages',
      'pagination'
    ],
    data() {
      const {
        refreshPage,
        markIsRead,
        filterUnread,
        nextPage,
        prevPage
      } = this

      return {
        buttons: {
          left: [{
            icon: 'refresh',
            position: 'single',
            label: 'atualizar',
            click: () => refreshPage()
          }, {
            icon: 'eye',
            position: 'single',
            click: () => markIsRead()
          }, {
            icon: 'envelope',
            position: 'single',
            click: () => filterUnread()
          }],
          right: [{
            icon: 'arrow-right',
            position: 'last',
            click: () => nextPage()
          }, {
            icon: 'arrow-left',
            position: 'first',
            click: () => prevPage()
          }]
        },
        unreadMessages: false
      }
    },
    methods: {
      refreshPage() {
        this.unreadMessages = false
        this.clearCheckedMessages()
      },
      markIsRead() {
        this.incrementCheckedMessages()
          .then(messages => {
            const messagesIds = messages
              .filter(message => !message.isRead)
              .map(message => message.id)

            if (messagesIds.length > 0) {
              const data = { messagesIds }

              const uri = 'admin/api/v1/orders/messages/mark_as_read'

              this.axios.post(uri, data)
                .then(() => {
                  this.clearCheckedMessages()
                })
                .catch(() => 'Não foi possível marcar mensagens como lidas')
            }
          })
      },
      filterUnread() {
        this.unreadMessages = true
        this.$emit('updateList')
      },
      nextPage() {
        if (this.pagination.links.next) {
          const pageNumber = this.pagination.current + 1
          this.getMessages(pageNumber)
        }
      },
      prevPage() {
        if (this.pagination.links.prev) {
          const pageNumber = this.pagination.current - 1
          this.getMessages(pageNumber)
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-action-bar {
    width: 100%;

    .collection-action-bar-left {
      float: left;
      margin-right: 0.25rem;
    }

    .collection-action-bar-text {
      float: right;
      font-weight: 600;
      padding: 0.75rem;
      vertical-align: middle;
    }

    .collection-action-bar-right {
      float: right;
    }
  }
</style>
