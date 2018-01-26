<template lang="pug">
  Table(type='striped')
    tr(slot='head')
      th Título
      th Concluídas / Cadastradas (%)
    tr.rows(slot='rows', v-for='milestone in milestones')
      td {{ milestone.title }}
      td
        Progress(:percent='milestone.average')
          span.caption
            b {{ milestone.closed }}/{{ milestone.total }}
            |  Tarefas
          span.caption
            b {{ milestone.average }}%
            |  Concluído
</template>

<script>
  export default {
    props: [
      'milestones'
    ]
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }
</style>
