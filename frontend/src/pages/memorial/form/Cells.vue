<template lang="pug">
  .table-wrapper
    Table.table(type='bordered')
      slot(slot='head')
        tr.caption-main
          th.col-code Código
          th.col-description Descrição
          th.col-m M
          th.col-f F
          th.col-cmv CMV Aplicado
          th.col-range(v-for='range in ranges')
            | {{ range }}
        tr.caption-ranges
          th.col-range-off(colspan='5')
          th.col-range(v-for='range in ranges')
            span Markup (%)
            span Preço (R$)
      slot(
        slot='rows', v-for='(components, groupName) in groups')
        tr.group-name
          th.col-group-name(colspan='5')
            Icon(:name='getIcon(groupName)')
            | {{ getGroupName(groupName) }}
          th.col-group-name-off(:colspan='rangesCount')
        tr.rows(v-for='component in components')
          td.col-code
            input(type='text', readonly, :value='component.code')
          td.col-description
            input(type='text', readonly, :value='component.description')
          td.col-m
            input(type='checkbox')
          td.col-f
            input(type='checkbox')
          td.col-cmv
            input(type='text', :value='component.costPrice')
          td.col-range(v-for='range in component.ranges')
            input(type='text', :value='`${range.markup}%`')
            input(type='text', readonly, :value='`R$ ${range.price}`')
</template>

<script>
  export default {
    props: [
      'groups'
    ],
    data: () => ({
      ranges: [],
      rangesCount: null
    }),
    methods: {
      getRanges() {
        const uri = 'admin/api/v1/memorials/power_ranges'

        this.axios.get(uri).then(({ data }) => {
          this.ranges = data
          this.rangesCount = Object.keys(data).length
        })
      },
      getIcon(name) {
        const icons = {
          module: 'th',
          inverter: 'exchange',
          stringBox: 'plug',
          structure: 'sitemap',
          variety: 'wrench'
        }

        return icons[name]
      },
      getGroupName(name) {
        const names = {
          module: 'módulos',
          inverter: 'inversores',
          stringBox: 'string box',
          structure: 'estrutura',
          variety: 'variedades'
        }

        return names[name]
      }
    },
    mounted() {
      this.getRanges()
    }
  }
</script>

<style lang="scss" scoped>
  .table-wrapper {
    overflow: auto;
    position: relative;
  }

  .rows {
    input {
      border: 1px solid $ui-gray-light;
      color: $ui-gray-regular;
      padding: 8px 10px;
      width: 100%;
    }

    input:read-only {
      background-color: $ui-gray-lighter;
    }
  }

  .group-name {
    background-color: $ui-gray-lighter;
    text-align: left;
    text-transform: uppercase;

    svg {
      float: left;
      margin-right: $ui-space-y/2;
    }
  }

  th {
    background-color: $ui-gray-lighter;
  }

  td {
    background-color: $ui-white-regular;
  }

  th,
  td {
    &:not(.col-range):not(.col-group-name-off) {
      display: table-cell;
      position: sticky;
      z-index: 5;
    }
  }

  .col-code {
    left: 0;
    min-width: 200px;
  }

  .col-description {
    left: 200px;
    min-width: 200px;
  }

  .col-m {
    left: 400px;
    min-width: 75px;
  }

  .col-f {
    left: 475px;
    min-width: 75px;
  }

  .col-cmv {
    left: 550px;
    min-width: 135px;
  }

  .col-group-name {
    left: 0;
  }

  .col-range {
    min-width: 200px;
    width: 100%;

    span {
      display: inline-block;
      width: 50%;
    }

    input {
      display: inline-block;
      max-width: 50%;
      width: 100%;

      &:first-of-type {
        text-align: right;
      }
    }
  }

  .col-range-off {
    left: 0;
  }
</style>
