<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title Mensagens ({{ totalOfMessages }})
        Search
      ActionBar.action-bar(
        slot='actions',
        :getMessages='getMessages',
        :pagination='pagination')
    List(
      slot='section'
      :messages='messages')
</template>

<script>
  import List from './list'
  import Search from '@/theme/collection/Search'

  export default {
    components: {
      List,
      Search
    },
    data: () => ({
      messages: [],
      pagination: {},
      totalOfMessages: ''
    }),
    methods: {
      getMessages(pageNumber = 1) {
        const uri = `admin/api/v1/orders/messages/?page=${pageNumber}`

        this.axios.get(uri).then(response => {
          this.totalOfMessages = response.data.size
          this.messages = response.data.results
          this.pagination = response.data.page
        })
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
