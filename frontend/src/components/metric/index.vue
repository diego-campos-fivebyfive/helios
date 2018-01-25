<template lang="pug">
  Panel
    div(slot='header')
      h2.title Progresso de tarefas técnicas
        span.sub Github - Milestones
    table(slot='section')
      thead
        tr
          th Título
          th Concluídas / Cadastradas (%)
      thead
      tbody
        tr.milestone(
          v-for='milestone in milestones')
          td {{ milestone.title }}
          td
            Progress(:percent='milestone.average')
            span
              b {{ milestone.closed }}
              |/
              b {{ milestone.total }}
              | Tarefas
            span
              b {{ milestone.average }}
              |% Concluído
</template>

<script>
  const uri = 'http://localhost:8000/admin/metrics/api/v1'

  export default {
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

</style lang="scss" scoped>
  /* Metrics Style */
</style>
