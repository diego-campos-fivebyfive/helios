<template lang="pug">
  Page.page(sidebar='none')
    header
      img.logo(src='@/assets/media/logo-ui.png', alt='Sices Solar - Logo')
    section.section
      h1.title
        | Oops.
        span.sub Tente novamente mais tarde.
      a.link(href='/') Voltar
    footer
      p.description
        | A plataforma pensada exclusivamente em quem trabalha com Energia Solar
        | Fotovoltaica
      span Sices Solar &reg;{{ getYear }}
</template>

<script>
  export default {
    computed: {
      getYear: () => new Date().getFullYear()
    }
  }
</script>

<style lang="scss" scoped>
  $notfound-logo-x: 150px;
  $notfound-description-x: 300px;

  .page {
    background-color: $ui-white-regular;
    color: $ui-text-main;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: $ui-space-y $ui-space-x;
    text-align: center;
  }

  .section {
    display: block;
    padding: $ui-space-y 0;
  }

  .logo {
    max-width: $notfound-logo-x;
  }

  .title {
    font-size: 2rem;
    font-weight: 100;
    padding: $ui-space-y 0;

    .sub {
      display: block;
      font-size: 1.25rem;
      font-weight: 600;
      padding-top: $ui-space-y/2;
    }
  }

  .link {
    color: $ui-blue-dark;
    font-size: 1.25rem;
    font-weight: 600;
  }

  .description {
    display: block;
    line-height: 1.5;
    margin: $ui-space-y auto;
    max-width: $notfound-description-x;
    width: 100%;
  }
</style>
