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

  const uri = 'http://localhost:8000/admin/metrics/api/v1'

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
      this.milestones = JSON.parse('[{"id":2,"title":"Seguro","open":0,"closed":1,"total":1,"average":100},{"id":1,"title":"Finame","open":1,"closed":1,"total":2,"average":50}]')
    }
  }
</script>

<style lang="scss" scoped>
  .panel {
    max-width: calc(50% - #{$ui-space-x});
  }
</style>
