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
        required: false
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
      },
      '$route.query.searchTerm': {
        handler() {
          if (this.$route.query.searchTerm) {
            return
          }

          this.removeQueryParam('searchTerm')
        },
        sync: true
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
      },
      removeQueryParam(paramToRemove) {
        if (!paramToRemove) {
          throw new Error('You must provide a params to remove')
        }

        this.$router.replace({ query: {
          ...Object.entries(this.$route.query)
            .reduce((acc, [queryName, queryValue]) => {
              if (queryName !== paramToRemove) {
                acc[queryName] = queryValue
              }

              return acc
            }, {})
        } })
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
      border: 1px solid $ui-blue-light;
      color: inherit;
      padding: 0.5rem;
      transition:
        border-color 0.15s ease-in-out 0s,
        box-shadow 0.15s ease-in-out 0s;
    }

    .collection-search-input::placeholder {
      color: $ui-text-main;
    }

    .collection-search-input:-ms-input-placeholder {
      color: $ui-text-main;
    }

    .collection-search-input::-ms-input-placeholder {
      color: $ui-text-main;
    }

    .collection-search-button {
      display: inline-block;
      vertical-align: middle;
    }
  }
</style>
