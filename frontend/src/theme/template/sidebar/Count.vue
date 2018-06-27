<template lang="pug">
  span.label-count.fade-in(v-show="amount") {{ amount }}
</template>

<script>
  export default {
    data: () => ({
      amount: 0
    }),
    mounted() {
      this.axios.get('/tasks/count')
        .then(({ data }) => {
          this.amount = data.count
        })
    }
  }
</script>

<style lang="scss" scoped>
  .label-count {
    background: $ui-blue-light;
    border-radius: $ui-corner/2;
    color: $ui-white-regular;
    font-size: 0.8em;
    float: right;
    margin-right: $ui-space-y/2;
    padding: $ui-space-y/6 $ui-space-y/2;
    animation-name: fadeInOpacity;
    animation-iteration-count: 1;
    animation-timing-function: ease-in;
    animation-duration: 1s;
    opacity: 1;
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
