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
      td.col-creation {{ memorial.createdAt }}
      td.col-publication {{ memorial.expiredAt }}
      td.col-expiration {{ memorial.publishedAt }}
      td.col-status
        label(:class='memorial.class')
          | {{ memorial.status }}
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
    ]
  }
</script>

<style lang="scss" scoped>
  .table {
    svg {
      vertical-align: middle;
    }

    .col-name {
      width: 25%;
    }

    .col-creation {
      text-align: center;
      width: 15%;
    }

    .col-publication {
      text-align: center;
      width: 15%;
    }

    .col-expiration {
      text-align: center;
      width: 15%;
    }

    .col-status {
      text-align: center;
      width: 15%;

      label {
        border-radius: $ui-corner;
        color: $ui-white-regular;
        padding: $ui-space-x/8 $ui-space-y/2;

        &.pending {
          background-color: $ui-gray-regular;
        }

        &.published {
          background-color: $ui-blue-dark;
        }

        &.expired {
          background-color: $ui-red-lighter;
        }
      }
    }

    .col-action {
      text-align: center;
      width: 15%;
    }
  }
</style>
