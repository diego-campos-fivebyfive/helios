<template lang="pug">
  span.label-count(v-show="showAmount()") {{ amount }}
</template>

<script>
  export default {
    data: () => ({
      amount: 0
    }),
    mounted() {
      this.axios.get('/twig/tasks/count')
        .then(({ data }) => {
          this.amount = data.count
        })
    },
    methods: {
      showAmount() {
        return this.amount
      }
    }
  }
</script>

<style lang="scss" scoped>
  .label-count {
    animation-name: fadeInOpacity;
    animation-iteration-count: 1;
    animation-timing-function: ease-in;
    animation-duration: 1s;
    background: $ui-blue-light;
    border-radius: $ui-corner/2;
    color: $ui-white-regular;
    font-size: 0.8em;
    float: right;
    margin-right: $ui-space-y;
    opacity: 1;
    padding: $ui-space-y/6 $ui-space-y/2;
  }

  @keyframes fadeInOpacity {
    0% {
      opacity: 0;
    }
    100% {
      opacity: 1;
    }
  }
</style>
