import { storiesOf } from '@storybook/vue'
import VueInfoAddon from 'storybook-addon-vue-info'
import Banner from '../collection/Banner'

storiesOf('Banner', module)
  .addDecorator(VueInfoAddon)
  .add('Info', () => ({
    components: { Banner },
    template:
    `<Banner
      icon='arrow-down'
      message='Lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et'
      title='Danger banner!'
      type='danger'>
    </Banner>`
  }))
