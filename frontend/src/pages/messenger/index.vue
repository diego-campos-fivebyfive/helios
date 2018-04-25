<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title Mensagens ({{ totalOfMessages }})
        Search
      ActionBar.action-bar(
        slot='actions',
        :getMessages='getMessages',
        :messages='messages',
        :pagination='pagination')
    List(
      slot='section',
      v-bind='{ checkedMessages, messages }')
</template>

<script>
  import List from './list'

  export default {
    components: {
      List
    },
    data: () => ({
      checkedMessages: [],
      messages: [],
      pagination: {},
      totalOfMessages: ''
    }),
    methods: {
      getMessages(pageNumber = 1) {
        this.checkedMessages = this.messages
          .filter(message => message.value)
          .map(message => message.id)
          .concat(this.checkedMessages)

        const uri = `admin/api/v1/orders/messages/?page=${pageNumber}`

        this.axios.get(uri).then(response => {
          this.totalOfMessages = response.data.size
          this.messages = response.data.results
          this.pagination = response.data.page
        })
      },
      checkedMessagesClear() {
        this.checkedMessages = []
      }
    },
    mounted() {
      this.getMessages()
    }
  }
</script>

<style lang="scss" scoped>
  .action-bar {
    margin-top: 1.5rem;
  }
</style>
