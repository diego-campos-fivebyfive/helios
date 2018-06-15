<template lang="pug">
  .copy-modal
    Confirm.copy-confirm(ref='confirm', v-on:removeItem='copyMemorial')
      div(slot='content')
        Icon.icon(name='question-circle-o', scale='4')
        h2.title Confirma a cópia de configurações? {{ level }} » {{ target }}
        h5.sub-title Os dados presentes em {{ level }} serão perdidos.
    Modal(ref='modal')
      h1.copy-modal-title(slot='header')
        Icon.icon(name='copy', scale='2')
        | Copiar configurações de markup
        h4.sub
          | Atenção! Ao efetuar a cópia, todos os
          | registros do nível atual são substituídos.
      .copy-modal-content(slot='section')
        span.description Selecione o nível de desconto a ser copiado
        select.levels(
          v-on:change='updateLevelcopy($event.target.value)')
          option(
            v-for='option in options',
            :value='option.value')
            | {{ option.text }}
        Button.copy(
          class='primary-common',
          label='Copiar',
          pos='last',
          :action='() => $refs.confirm.show(target)')
          Icon(name='copy')
</template>

<script>
  import Select from '@/theme/collection/Select'

  export default {
    props: {
      level: {
        type: Array,
        required: true
      }
    },
    components: {
      Select
    },
    data: () => ({
      options: [],
      target: ''
    }),
    methods: {
      copyMemorial(target) {
        this.$refs.confirm.hide()

        const { id } = this.$route.params
        const uri = `admin/api/v1/memorials/${id}/copy_level`

        this.axios.put(uri, { source: this.level, target })
      },
      show() {
        this.axios.get('admin/api/v1/memorials/levels')
          .then(response => {
            delete response.data[this.level]

            this.options = Object.entries(response.data)
              .map(([key, value]) => ({
                value: key,
                text: value
              }))

            this.target = this.options[0].value
          })

        this.$refs.modal.show()
      },
      updateLevelcopy(value) {
        this.target = value
      }
    }
  }
</script>

<style lang="scss" scoped>
  .copy-modal {
    z-index: 200;
  }

  .copy-confirm {
    z-index: 205;
  }

  .copy-modal-title {
    font-size: 2.25rem;
    font-weight: 600;
    text-align: center;

    .sub {
      font-size: 1rem;
      font-weight: 600;
      padding: $ui-space-x/2 $ui-space-y/2;
    }
  }

  .icon {
    margin-right: 0.5rem;
  }

  .copy-modal-content {
    align-items: baseline;
    display: flex;
    position: relative;
    margin: 0 $ui-space-y * 9.5;

    .description {
      border: 1px solid $ui-gray-light;
      font-weight: 600;
      height: 2.75rem;
      padding: $ui-space-y / 1.75;
    }

    .levels {
      border: 1px solid $ui-gray-light;
      color: $ui-text-main;
      height: 2.75rem;
      padding: $ui-space-y / 18 0;
    }

    .copy {
      height: 2.75rem;
      padding: $ui-space-y / 1.75;
    }
  }
</style>
