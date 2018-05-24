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
      slot(slot='rows', v-for='(components, groupName) in groups')
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
            .costPrice
              input(
                type='text',
                v-on:blur='updateRange(component.id, $event.target.value)',
                :value='component.costPrice')
          td.col-range(v-for='(range, key) in component.ranges')
            .markups
              input(
                type='text',
                v-on:blur='updateMarkup(component, key, $event.target.value)',
                :value='range.markup')
              input(type='text', readonly, :value='range.price')
</template>

<script>
  export default {
    props: [
      'groups',
      'getQueryParams'
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
      },
      updateRange(componentId, costPrice) {
        const { level } = this.getQueryParams()

        const uri = `admin/api/v1/memorial_ranges/${componentId}/cost_price`

        this.axios.put(uri, { costPrice, level }).then(response => {
          this.$emit('updateMemorialRange', response.data)
        })
      },
      updateMarkup(component, powerRange, markup) {
        const { level } = this.getQueryParams()

        const uri = `admin/api/v1/memorial_ranges/${component.id}/markup`

        this.axios.put(uri, { markup, powerRange, level }).then(response => {
          this.$emit('updateMemorialMarkup', response.data, markup)
        })
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

    input {
      padding: $ui-space-y/2 $ui-space-x/2;
    }
  }

  .col-description {
    left: 200px;
    min-width: 200px;

    input {
      padding: $ui-space-y/2 $ui-space-x/2;
    }
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

    .costPrice {
      position: relative;
      color: $ui-gray-regular;
    }

    .costPrice:before {
      padding: $ui-space-y/2 $ui-space-x/4;
      position: absolute;
      content: "R$";
    }

    input {
      padding: $ui-space-y/2 $ui-space-x;
    }

    span {
      position: absolute;
      left: $ui-space-y/2.5;
      top: $ui-space-y/2;
    }
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

    .markups {
      position: relative;
      color: $ui-gray-regular;

      &:before {
        content: "%";
        left: $ui-space-x * 3;
        position: absolute;
        top: $ui-space-y/2;
      }

      &:after {
        content: "R$";
        left: - $ui-space-x * 4;
        position: absolute;
        top: $ui-space-y/2;
      }
    }
  }

  .col-range-off {
    left: 0;
  }

  .rows {
    input {
      border: 1px solid $ui-gray-light;
      color: $ui-gray-regular;
      padding: $ui-space-y/2 $ui-space-x;
      width: 100%;

      &:read-only {
        background-color: $ui-gray-lighter;
      }
    }
  }
</style>
