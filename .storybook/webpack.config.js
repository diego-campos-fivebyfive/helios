const path = require('path');
const { storyLoader } = require('vue-storybook')

// Export a function. Accept the base config as the only param.
module.exports = (storybookBaseConfig, configType) => {
  console.log(storybookBaseConfig.module.rules)
  storybookBaseConfig.module.rules[1].options = {
    loaders: {
      'story': storyLoader
    }
  }
  return storybookBaseConfig;
};
