<template lang="pug">
  Field(:field='field')
    input.field(
      v-on:input='requestOptions($event.target.value)',
      :value='selected.text')
    .options(v-if='showOptions()')
      ul
        li(
          v-on:click='updateOption($event.target.value)',
          v-for='option in options',
          :value='option.value')
          | {{ option.text }}
</template>

<script>
  import Field from 'theme/collection/Field'

  export default {
    components: {
      Field
    },
    props: {
      disabled: {
        type: Boolean,
        required: false,
        default: false
      },
      field: {
        type: Object,
        required: true
      },
      options: {
        type: Array,
        required: true
      },
      selected: {
        type: Object,
        required: false,
        default: () => ({
          text: ''
        })
      }
    },
    data: () => ({
      showingOptions: false
    }),
    methods: {
      requestOptions(param) {
        this.$emit('request', param)
        this.showingOptions = true
      },
      showOptions() {
        return this.showingOptions
      },
      updateOption(selectedOption) {
        this.showingOptions = false

        const currentOption = this.options.find(eachOption => (
          eachOption.value === selectedOption
        ))

        this.$emit('update', currentOption)
      }
    }
  }
</script>

<style lang="scss" scoped>
  .options {
    position: relative;
    width: 100%;

    ul {
      background-color: $ui-white-regular;
      border: 1px solid $ui-gray-light;
      border-radius: 1px;
      border-top: 0;
      left: 0;
      list-style: none;
      width: 100%;
      top: 0;
    }

    li {
      padding: $ui-space-y/3 $ui-space-x/2;
      width: 100%;

      &:hover {
        background-color: $ui-blue-light;
        color: $ui-white-regular;
        cursor: pointer;
      }
    }
  }
</style>
