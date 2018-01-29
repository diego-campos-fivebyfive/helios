<template lang="pug">
  Panel.panel
    div(slot='header')
      h2.title Progresso de tarefas técnicas
        span.sub Github - Milestones
    div(slot='section')
      Content(:milestones='milestones')
</template>

<script>
  import Content from '@/components/metric/Content'

  export default {
    components: {
      Content
    },
    data: () => ({
      milestones: [],
      modal: {
        milestone: '',
        issues: []
      }
    }),
    mounted() {
      this.axios.get(`admin/metrics/api/v1/milestones`).then(response => {
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
