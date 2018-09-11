import VueInfoAddon from 'storybook-addon-vue-info'
import { configure, storiesOf } from '@storybook/vue'

const requireStories = require.context('../collection', true, /.story.js$/)

const loadStories = () => {
  requireStories.keys()
    .forEach(filename => {
      const storyName = filename.replace(/.\/|.story.js/g, '')
      const storyConfig = requireStories(filename)
      const { components, models } = storyConfig.default || storyConfig

      const story = storiesOf(storyName, module)
        .addDecorator(VueInfoAddon)

      Object
        .entries(models)
        .forEach(([modelName, template]) => {
          story.add(modelName, () => ({
            components,
            template
          }))
        })
    })
}

configure(loadStories, module)
