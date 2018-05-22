<template lang="pug">
  .col-components
    span.components-title Componentes
    label(v-for='(component, family) in getComponents()')
      input(
        type='checkbox',
        v-on:change='updateFamiliesQuery(family, $event)')
      Icon(:name='component.icon')
      | {{ component.label }}
</template>

<script>
  export default {
    props: [
      'families'
    ],
    methods: {
      getComponents() {
        const components = {
          module: {
            icon: 'th',
            label: 'Módulos'
          },
          inverter: {
            icon: 'exchange',
            label: 'Inversores'
          },
          stringBox: {
            icon: 'plug',
            label: 'String Box'
          },
          structure: {
            icon: 'sitemap',
            label: 'Estrutura'
          },
          variety: {
            icon: 'wrench',
            label: 'Variedades'
          }
        }

        return components
      },
      updateFamiliesQuery(familyName, event) {
        if (event.target.checked) {
          this.$emit('updateFamiliesQuery', this.families.concat(familyName))

        } else {
          this.$emit('updateFamiliesQuery', this.families
            .filter(eachFamilyName => (
              eachFamilyName !== familyName
            )))
        }
      }
    }
  }
</script>

<style lang="scss" scoped>
  .col-components {
    text-align: left;

    label {
      display: inline-block;
      padding: $ui-space-y/1.25 $ui-space-x/3;
    }

    svg {
      margin: 0 $ui-space-x/6;
    }

    .components-title {
      display: block;
      font-weight: 600;
    }
  }
</style>
