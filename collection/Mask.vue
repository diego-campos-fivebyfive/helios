<template lang="pug">
  Field(
    ref='field',
    :field='field')
    input.field(
      :value='field.value',
      v-mask='getMask(field.type)',
      :placeholder='placeholder',
      v-on:blur='$emit("validate", field)',
      v-on:input='$set(field, "value", $event.target.value)')
</template>

<script>
  import Field from 'theme/collection/Field'
  import { mask } from 'vue-the-mask'

  export default {
    name: 'Mask',
    components: {
      Field
    },
    props: {
      field: {
        type: Object,
        required: true
      }
    },
    data: () => ({
      placeholder: ''
    }),
    directives: { mask },
    mounted() {
      this.setInputPlaceholder()
    },
    methods: {
      getMask(type) {
        const masks = {
          phone: ['(##) ####-####', '(##) #####-####'],
          cnpj: '##.###.###/####-##',
          postcode: '#####-###'
        }

        return masks[type]
      },
      setInputPlaceholder() {
        this.placeholder = this.$refs.field.getPlaceholder()
      }
    }
  }
</script>

<style lang="scss" scoped>
  /* Mask Style */
</style>
