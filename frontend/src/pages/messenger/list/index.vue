<template lang="pug">
  form
    Table.table
      tr.rows(slot='rows', v-for='message in messages')
        td.col-checkbox
          Checkbox(:field='message.author.id')
        td.col-author {{ message.author.name }}
        td.col-content {{ message.content }}
        td.col-date {{ formatDate(message.created_at) }}
</template>

<script>
  import Checkbox from '@/theme/collection/Checkbox'

  export default {
    props: [
      'messages'
    ],
    components: {
      Checkbox
    },
    methods: {
      formatDate(value) {
        return this.$moment(value, 'YYYY-MM-DD').format('DD/MM/YYYY')
      }
    }
  }
</script>

<style lang="scss" scoped>
  .col-checkbox {
    width: 10%;
    text-align: center;
  }

  .col-author {
    width: 20%;
    text-align: center;
  }

  .col-content {
    width: 55%;
    text-align: center;
  }

  .col-date {
    width: 15%;
    text-align: center;
  }
</style>
