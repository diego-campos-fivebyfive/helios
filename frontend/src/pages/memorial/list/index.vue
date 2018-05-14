<template lang="pug">
  Table.table(type='bordered')
    tr(slot='head')
      th.col-name Nome
      th.col-creation
        Icon(name='calendar')
        |  Criação
      th.col-publication
        Icon(name='calendar')
        |  Publicação
      th.col-expiration
        Icon(name='calendar')
        |  Expiração
      th.col-status Status
      th.col-action
    tr.rows(slot='rows', v-for='memorial in memorials')
      td.col-name {{ memorial.name }}
      td.col-creation {{ formatDate(memorial.createdAt) }}
      td.col-publication {{ formatDate(memorial.expiredAt)}}
      td.col-expiration {{ formatDate(memorial.publishedAt)}}
      td.col-status
        label(:class='formatStatus(memorial.status)')
          | {{ formatStatus(memorial.status) }}
      td.col-action
        Button(
          label='Operações',
          type='primary-common',
          icon='arrow-down',
          pos='single')
</template>

<script>
  export default {
    props: [
      'memorials'
    ],
    data: () => ({
      status: {
        0: 'pendente',
        1: 'publicado',
        2: 'expirado'
      }
    }),
    methods: {
      formatDate(date) {
        if (!date) {
          return ''
        }

        const moment = this.$moment(date, 'YYYY-MM-DD')

        return moment.format('DD/MM/YYYY')
      },
      formatStatus(statusCode) {
        return this.status[statusCode]
      }
    }
  }
</script>

<style lang="scss" scoped>
  %svg {
    svg {
      vertical-align: middle;
    }
  }

  .col-name {
    width: 25%;
  }

  .col-creation {
    text-align: center;
    width: 15%;

    @extend %svg;
  }

  .col-publication {
    text-align: center;
    width: 15%;

    @extend %svg;
  }

  .col-expiration {
    text-align: center;
    width: 15%;

    @extend %svg;
  }

  .col-status {
    text-align: center;
    width: 15%;

    label {
      border-radius: $ui-corner;
      color: $ui-white-regular;
      padding: $ui-space-x/8 $ui-space-y/2;

      &.pendente {
        background-color: $ui-gray-regular;
      }

      &.publicado {
        background-color: $ui-blue-dark;
      }

      &.expirado {
        background-color: $ui-red-lighter;
      }
    }
  }

  .col-action {
    text-align: center;
    width: 15%;
  }
</style>
