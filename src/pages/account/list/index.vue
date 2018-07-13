<template lang="pug">
  Table.table(type='stripped')
    tr(slot='head')
      th.col-trade Nome Fantasia
      th.col-cnpj Cnpj
      th.col-email Email
      th.col-level Nível de desconto
      th.col-status Status
      th.col-details Mais detalhes
    tr.rows(slot='rows', v-for='account in accounts')
      td.col-trade {{ account.name }}
      td.col-cnpj {{ account.cnpj }}
      td.col-email {{ account.email }}
      td.col-level
        Badge(:level='account.level')
      td.col-status {{ account.status }}
      td.col-details {{ account.details }}
</template>

<script>
  export default {
    data: () => ({
      accounts: [
        {
          name: 'carlos',
          cnpj: 12345,
          email: 'c@g.com',
          level: 'platinum',
          status: 'ativo',
          details: 'teste'
        },
        {
          name: 'joao',
          cnpj: 2345,
          email: 'c@h.com',
          level: 'partner',
          status: 'bloqueado',
          details: 'teste2'
        }
      ]
    })
  }
</script>
<style lang="scss" scoped>
  .rows {
    cursor: pointer;
  }

  .col-trade {
    min-width: 20%;
    text-align: left;
  }

  .col-cnpj {
    text-align: center;
  }

  .col-email {
    min-width: 20%;
    text-align: center;
  }

  .col-level {
    text-align: center;
  }

  .col-status {
    text-align: right;
  }

  .col-details {
    text-align: right;
  }
</style>
