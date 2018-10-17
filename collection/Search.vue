<template lang="pug">
  form.collection-search(
    v-on:keypress.enter.prevent='')
    input.collection-search-input(
      v-model='searchTerm',
      :placeholder='placeholder',
      v-on:keyup.enter='updateRoute')
    Button.collection-search-button(
      :action='updateRoute',
      class='primary-common',
      label='Pesquisar',
      pos='last')
</template>

<script>
  import Button from 'theme/collection/Button'

  export default {
    components: {
      Button
    },
    props: {
      search: {
        type: String,
        required: false
      },
      placeholder: {
        type: String,
        required: false,
        default: 'Pesquisar'
      }
    },
    data: () => ({
      searchTerm: ''
    }),
    mounted() {
      this.searchTerm = this.search
    },
    watch: {
      search() {
        this.searchTerm = this.search
      }
    },
    methods: {
      updateRoute() {
        this.$router.push({
          query: {
            ...this.$route.query,
            searchTerm: this.searchTerm
          }
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  .collection-search {
    height: 2.25rem;
    position: relative;

    .collection-search-input {
      background-color: $ui-white-regular;
      background-image: none;
      border: 1px solid $ui-gray-light;
      color: inherit;
      padding: 0.5rem;
      transition:
        border-color 0.15s ease-in-out 0s,
        box-shadow 0.15s ease-in-out 0s;
    }

    .collection-search-input::placeholder {
      color: $ui-gray-regular;
    }

    .collection-search-input:-ms-input-placeholder {
      color: $ui-gray-regular;
    }

    .collection-search-input::-ms-input-placeholder {
      color: $ui-gray-regular;
    }

    .collection-search-button {
      display: inline-block;
      vertical-align: middle;
    }
  }

  @media screen and (max-width: $small-device) {
    .collection-search {
      display: flex;
    }

    .collection-search-input {
      flex: 1;
    }
  }
</style>
