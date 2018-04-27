<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title Mensagens ({{ totalOfMessages }})
        Search(
          :getMessages='getMessages',
          :incrementParams='incrementParams')
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
      paramsTerms: [],
      messages: [],
      pagination: {},
      totalOfMessages: ''
    }),
    methods: {
      getMessages(pageNumber = 1) {
        this.incrementCheckedMessages()

        const uri = `admin/api/v1/orders/messages/?page=${pageNumber}`

        const data = {
          params: { searchTerm: this.paramsTerms }
        }

        this.axios.get(uri, data).then(response => {
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
      incrementParams(params) {
        this.paramsTerms = params
      },
      clearCheckedMessages() {
        this.checkedMessages = []
        this.paramsTerms = []

        this.messages.forEach(message => (
          this.$set(message, 'value', false)
        ))

        this.getMessages()
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
