<template lang="pug">
  Panel.panel
    div(slot='header')
      slot(name='heading')
        h1.title
          | Mensagens ({{ messages }})
        Search
      slot(name='actions')
        ActionBar.action-bar
    List(
      slot='section')
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
      messages: 11
    })
  }
</script>

<style lang="scss" scoped>
  .action-bar {
    margin-top: 1.5rem;
  }
</style>
