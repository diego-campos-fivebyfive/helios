<template lang="pug">
  Table.table(type='bordered')
    slot(slot='head')
      tr
        th Código
        th Descrição
        th M
        th F
        th CMV Aplicado
        th(v-for='range in ranges')
          | {{ range }}
      tr
        th(colspan='5')
        th.range(v-for='range in ranges')
          span Markup (%)
          span Preço (R$)
    slot(
      slot='rows', v-for='(components, groupName) in groups')
      tr.group-name
        th(:colspan='5 + rangesCount')
          Icon(:name='getIcon(groupName)')
          | {{ getGroupName(groupName) }}
      tr.rows(v-for='component in components')
        td
          input(type='text', readonly, :value='component.code')
        td
          input(type='text', readonly, :value='component.description')
        td
          input(type='checkbox')
        td
          input(type='checkbox')
        td
          input(type='text', :value='component.costPrice')
        td.range(v-for='range in component.ranges')
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
          string_box: 'plug',
          structure: 'sitemap',
          variety: 'wrench'
        }

        return icons[name]
      },
      getGroupName(name) {
        const names = {
          module: 'módulos',
          inverter: 'inversores',
          string_box: 'string box',
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

  .range {
    input {
      width: 50%;

      &:first-of-type {
        text-align: right;
      }
    }

    span {
      width: 50%;
      display: inline-block;
    }
  }

  .group-name {
    background-color: $ui-gray-lighter;
    text-transform: uppercase;
    text-align: left;

    svg {
      float: left;
      margin: 0 $ui-space-y/2;
    }
  }
</style>
