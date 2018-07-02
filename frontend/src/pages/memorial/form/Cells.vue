<template lang="pug">
  .table-wrapper
    Table.table(type='bordered')
      slot(slot='head')
        tr.caption-main
          th.col-code Código
          th.col-description Descrição
          th.col-p M
          th.col-c F
          th.col-cmv CMV
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
        tr.rows(
          v-for='component in components',
          :class='component.relation')
          td.col-code
            input(type='text', readonly, :value='component.code')
          td.col-description
            input(type='text', readonly, :value='component.description')
          td.col-p
            input(
              type='checkbox',
              :checked='parentRelationship(component)',
              v-on:change='updateRelation(component, "parent", groupName, $event)')
          td.col-c
            input(
              type='checkbox',
              :checked='childRelationship(component)',
              v-on:change='updateRelation(component, "child", groupName, $event)')
          td.col-cmv
            .cost-price
              input(
                type='text',
                v-on:blur='updateRange(component.id, $event.target.value)',
                :value='component.costPrice')
          td.col-range(
            v-for='(range, rangeKey) in component.ranges')
            .markups
              input(
                type='text',
                v-on:blur='updateMarkup(component, rangeKey, groupName, $event)',
                :value='range.markup')
              input(type='text', readonly, :value='range.price')
</template>

<script>
  export default {
    props: {
      groups: {
        required: true
      },
      getQueryParams: {
        type: Function,
        required: true
      }
    },
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
      updateRelation(component, relation, groupName, $event) {
        if (component.relation) {
          if (component.relation === relation) {
            if ($event.target.checked) {}
            else {
              this.$set(component, 'relation', null)

              if (relation === 'parent') {
                this.cleanComponentRelations(groupName)
              }
            }
          }
          else {
            if ($event.target.checked) {
              $event.target.checked = false
            }
            else {}
          }
        }
        else {
          const groupHasParent = this.groups[groupName]
            .some(component => component.relation === 'parent')

          if (relation === 'parent') {
            if (groupHasParent) {
              $event.target.checked = false
            }
            else {
              this.$set(component, 'relation', 'parent')
            }
          }
          else {
            if (groupHasParent) {
              this.$set(component, 'relation', 'child')

              const parent = this.getParentId(groupName)

              this.copyMarkups(component, parent.id)
            }
            else {
              $event.target.checked = false
            }
          }
        }
      },
      cleanComponentRelations(groupName) {
        this.groups[groupName]
          .forEach(component => {
            this.$set(component, 'relation', null)
          })
      },
      copyMarkups(component, parentId) {
        const { level } = this.getQueryParams()

        const uri = `admin/api/v1/memorial_ranges/${parentId}/copy_markups`

        this.axios.put(uri, { childId: component.id, level })
          .then(({ data }) => {
            this.$set(component, 'ranges', data)
          })
      },
      getParentId(groupName) {
        return this.groups[groupName]
          .find(component => component.relation === 'parent')
      },
      updateRange(componentId, costPrice) {
        const { level } = this.getQueryParams()

        const uri = `admin/api/v1/memorial_ranges/${componentId}/cost_price`

        this.axios.put(uri, { costPrice, level }).then(response => {
          this.$emit('updateMemorialRange', response.data)
        })
      },
      updateMarkup(component, rangeKey, groupName, $event) {
        const markupValue = $event.target.value

        const getParams = () => {
          const { level } = this.getQueryParams()

          const baseParams = {
            markup: markupValue,
            powerRange: rangeKey,
            level
          }

          if (component.relation !== 'parent') {
            return baseParams
          }

          const children = this.groups[groupName]
            .filter(component => component.relation === 'child')
            .map(child => child.id)

          return Object.assign(baseParams, {
            parent: true,
            children
          })
        }

        const uri = `admin/api/v1/memorial_ranges/${component.id}/markup`

        this.axios.put(uri, getParams())
          .then(({ data }) => {
            this.$emit('updateMemorialMarkup', data, markupValue)
          })
      },
      parentRelationship(component) {
        return component.relation === "parent"
      },
      childRelationship(component){
        return component.relation === "child"
      }
    },
    mounted() {
      this.getRanges()
    }
  }
</script>

<style lang="scss" scoped>
  $parent_background_: #fcf8e3;
  $child_background_: #dff0d8;

  .table-wrapper {
    overflow: auto;
    position: relative;

    .parent {
      background-color: $parent_background_;
    }

    .child {
      background-color: $child_background_;
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
    background-color: inherit;
  }

  th,
  td {
    &:not(.col-range):not(.col-group-name-off) {
      display: table-cell;
      position: sticky;
    }
  }

  .col-code {
    left: 0;
    min-width: 170px;

    input {
      padding: $ui-space-y/2 $ui-space-x/2;
    }
  }

  .col-description {
    left: 170px;
    min-width: 295px;

    input {
      padding: $ui-space-y/2 $ui-space-x/2;
    }
  }

  .col-p {
    left: 465px;
    min-width: 50px;
  }

  .col-c {
    left: 515px;
    min-width: 50px;
  }

  .col-cmv {
    left: 565px;
    min-width: 120px;

    .cost-price {
      position: relative;
      color: $ui-gray-regular;

      &:before {
        padding: $ui-space-y/2 $ui-space-x/4;
        position: absolute;
        content: "R$";
      }
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
        position: absolute;
        right: $ui-space-x * 2.75;
        top: $ui-space-y/2;
      }
    }
  }

  .col-range-off {
    left: 0;
  }

  .rows {
    background-color: $ui-white-regular;

    input {
      border: 1px solid $ui-gray-light;
      color: $ui-gray-regular;
      background-color: $ui-white-regular;
      padding: $ui-space-y/2 $ui-space-x;
      width: 100%;

      &:read-only {
        background-color: $ui-gray-lighter;
      }
    }
  }
</style>
