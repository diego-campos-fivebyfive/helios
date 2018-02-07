<template lang="pug">
  div
    Modal(:open='modal.open', v-on:close='modal.open = false')
      h1.title(slot='header')
        | {{ modal.milestone }}
        span.sub Tarefas relacionadas ao milestone
      ul.list(slot='section')
        li(v-for='issue in modal.issues') {{ issue.title }}
    Table.table(type='striped')
      tr(slot='head')
        th Título
        th Concluídas / Cadastradas (%)
      tr.rows(
        slot='rows',
        v-for='milestone in milestones',
        v-on:click='getIssues(milestone)')
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
    ],
    data: () => ({
      modal: {
        open: false,
        title: '',
        list: []
      }
    }),
    methods: {
      getIssues(milestone) {
        const uri = `admin/metrics/api/v1/milestones/${milestone.id}/issues`
        this.axios.get(uri).then(response => {
          this.modal = {
            open: true,
            milestone: milestone.title,
            issues: response.data
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .table {
    td,
    th {
      &:nth-child(1) {
        text-align: left;
        width: 40%;
      }

      &:nth-child(2) {
        text-align: right;
      }
    }
  }
</style>
