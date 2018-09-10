const path = require('path')
const { storyLoader } = require('vue-storybook')

// Export a function. Accept the base config as the only param.
module.exports = (storybookBaseConfig, configType) => {
  storybookBaseConfig.module.rules[1].options = {
    loaders: {
      story: storyLoader,
      js: {
        loader: 'babel-loader'
      },
      scss: {
        loader: 'vue-style-loader!css-loader!sass-loader!sass-resources-loader',
        options: {
          resources: path.resolve(__dirname, '../assets/style/main.scss')
        }
      }
    }
  }

  storybookBaseConfig.module.rules.push(
    {
      test: /\.js$/,
      loader: 'babel-loader',
      exclude: /node_modules/,
      include: [
        path.resolve(__dirname, 'node_modules/vue-awesome'),
        path.resolve(__dirname, 'node_modules/helios'),
        path.resolve(__dirname, 'src')
      ]
    },
    {
      test: /\.css$/,
      use: [
        'vue-style-loader',
        'css-loader'
      ]
    },
    {
      test: /\.scss$/,
      use: [
        'vue-style-loader',
        'css-loader',
        'sass-loader'
      ]
    },
    {
      test: /\.(png|jpe?g|gif|svg)(\?.*)?$/,
      loader: 'url-loader',
      options: {
        limit: 10000,
        name: 'static/[name].[hash:7].[ext]'
      }
    },
    {
      test: /\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/,
      loader: 'url-loader',
      options: {
        limit: 10000,
        name: 'static/[name].[hash:7].[ext]'
      }
    },
    {
      test: /\.(woff2?|eot|ttf|otf)(\?.*)?$/,
      loader: 'url-loader',
      options: {
        limit: 10000,
        name: 'static/[name].[hash:7].[ext]'
      }
    }
  )

  storybookBaseConfig.resolve.alias.theme = path.resolve(__dirname, '../')

  console.log(storybookBaseConfig.module.rules[1].options)
  return storybookBaseConfig;
};
