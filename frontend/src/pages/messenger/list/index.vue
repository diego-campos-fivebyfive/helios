<template lang="pug">
  form
    Table.table
      tr.rows(
        slot='rows',
        v-for='message in messages',
        :class='{ "not-read": !message.isRead }')
        td.col-checkbox
          Checkbox(:field='includeCheckedState(message)')
        td.col-author {{ message.author.name }}
        td.col-content {{ message.content }}
        td.col-date {{ formatDate(message.createdAt) }}
</template>

<script>
  import Checkbox from '@/theme/collection/Checkbox'

  export default {
    props: [
      'checkedMessages',
      'messages'
    ],
    components: {
      Checkbox
    },
    methods: {
      formatDate(value) {
        return this.$moment(value, 'YYYY-MM-DD').format('DD/MM/YYYY')
      },
      includeCheckedState(message) {
        const checked = this.checkedMessages
          .find(checkedMessage => (
            checkedMessage.id === message.id
          ))

        this.$set(message, 'value', Boolean(checked))

        return message
      }
    }
  }
</script>

<style lang="scss" scoped>
  .not-read {
    font-weight: bold;
  }

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
