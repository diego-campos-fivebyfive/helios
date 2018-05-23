<template lang="pug">
  .col-components
    span.components-title Componentes
    label(v-for='(component, family) in components')
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
    data: () => ({
      components: {
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
    }),
    methods: {
      updateFamiliesQuery(familyName, event) {
        const familiesQuery = (event.target.checked)
          ? this.families.concat(familyName)
          : this.families
            .filter(eachFamilyName => (eachFamilyName !== familyName))

        this.$emit('updateFamiliesQuery', familiesQuery)
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
