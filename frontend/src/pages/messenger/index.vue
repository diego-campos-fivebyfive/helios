<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title Mensagens ({{ totalOfMessages }})
        Search
      ActionBar.action-bar(
        slot='actions',
        :getMessages='getMessages',
        :clearCheckedMessages='clearCheckedMessages',
        :incrementCheckedMessages='incrementCheckedMessages',
        :messages='messages',
        :pagination='pagination')
    List(
      slot='section',
      v-bind='{ checkedMessages, messages }')
</template>

<script>
  import List from './list'
  import ActionBar from './ActionBar'

  export default {
    components: {
      List,
      ActionBar
    },
    data: () => ({
      checkedMessages: [],
      messages: [],
      pagination: {},
      totalOfMessages: ''
    }),
    methods: {
      getMessages(pageNumber = 1) {
        this.incrementCheckedMessages()

        const uri = `admin/api/v1/orders/messages/?page=${pageNumber}`

        this.axios.get(uri).then(response => {
          this.totalOfMessages = response.data.size
          this.messages = response.data.results
          this.pagination = response.data.page
        })
      },
      incrementCheckedMessages() {
        return new Promise(resolve => {
          this.checkedMessages = this.messages
            .filter(message => message.value)
            .concat(this.checkedMessages)

          resolve(this.checkedMessages)
        })
      },
      clearCheckedMessages() {
        this.checkedMessages = []

        this.messages.forEach(message => (
          this.$set(message, 'value', false)
        ))

        this.getMessages(this.pagination.current)
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
