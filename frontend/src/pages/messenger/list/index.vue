<template lang="pug">
  form
    Table.table
      tr.rows(slot='rows', v-for='message in messages')
        td.col-checkbox
          Checkbox(:field='message')
        td.col-author {{ message.author }}
        td.col-content {{ message.content }}
        td.col-date {{ message.date }}
</template>

<script>
  import Checkbox from '@/theme/collection/Checkbox'

  export default {
    components: {
      Checkbox
    },
    data: () => ({
      messages: [
        {
          author: 'Anna Smith',
          content: 'Lorem ipsum dolor noretek imit set.',
          date: '6.10 AM'
        },
        {
          author: 'Jack Nowak',
          content: 'Aldus PageMaker including versions of Lorem Ipsum.',
          date: '8.22 PM'
        },
        {
          author: 'Sandra Derick',
          content: 'Contrary to popular belief.',
          date: 'May 28'
        },
        {
          author: 'Damien Ritz',
          content: 'Oor Lorem Ipsum is that it has a more-or-less normal.',
          date: 'Jun 11'
        }
      ]
    })
  }
</script>

<style lang="scss" scoped>
  .col-checkbox {
    margin-bottom: $ui-space-y/4;
    min-width: 10%;
    text-align: center;
  }

  .col-author {
    min-width: 30%;
    text-align: left;
  }

  .col-content {
    min-width: 40%;
    text-align: center;
  }

  .col-date {
    min-width: 20%;
    text-align: center;
  }
</style>
