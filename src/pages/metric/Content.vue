<template lang="pug">
  div
    Modal(ref='modal')
      h1.title(slot='header')
        | {{ metric.milestone }}
        span.sub Tarefas relacionadas ao milestone
      ul.list(slot='section')
        li(v-for='issue in metric.issues') {{ issue.title }}
    Table.table(type='stripped')
      tr(slot='head')
        th.col-title Título
        th.col-milestone Concluídas / Cadastradas (%)
      tr.rows(
        slot='rows',
        v-for='milestone in milestones',
        v-on:click='getIssues(milestone)')
        td.col-title {{ milestone.title }}
        td.col-milestone
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
    props: {
      milestones: {
        type: Array,
        required: true
      }
    },
    data: () => ({
      metric: {
        title: '',
        list: []
      }
    }),
    methods: {
      getIssues(milestone) {
        const uri = `admin/api/v1/metrics/milestones/${milestone.id}/issues`
        this.axios.get(uri).then(response => {
          this.metric = {
            milestone: milestone.title,
            issues: response.data
          }

          this.$refs.modal.show()
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-title {
    min-width: 40%;
    text-align: left;
  }

  .col-milestone {
    text-align: right;
  }

  .title {
    text-align: left;
  }
</style>
