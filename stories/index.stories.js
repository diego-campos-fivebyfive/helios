import { storiesOf } from '@storybook/vue'

import BannerDanger from './BannerDanger.story'
import BannerInfo from './BannerInfo.story'

storiesOf('Banner', module)
  .add('Danger', () => ({
      components: { BannerDanger },
      template: '<BannerDanger/>',
  }))
  .add('Info', () => ({
    components: { BannerInfo },
    template: '<BannerInfo/>',
  }))
