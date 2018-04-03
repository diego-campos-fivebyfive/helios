<template lang="pug">
  Panel.panel
    h2.title(slot='header')
      | Progresso de tarefas técnicas
      span.sub Github - Milestones
    Content(slot='section', :milestones='milestones')
</template>

<script>
  import Content from './Content'

  export default {
    components: {
      Content
    },
    data: () => ({
      milestones: []
    }),
    mounted() {
      this.axios.get('admin/api/v1/metrics/milestones').then(response => {
        this.milestones = response.data
      })
    }
  }
</script>

<style lang="scss" scoped>
  .panel {
    max-width: calc(50% - #{$ui-space-x});
  }
</style>
