'use strict'

const path = require('path')
const webpack = require('webpack')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const babelConfig = require('./.babel.config.js')
const clientPath = require('./client-path')

const config = {
  entry: [
    './cordova',
    './app/main.js'
  ],
  output: {
    path: path.resolve(__dirname, 'dist'),
    publicPath: '/',
    filename: '[name].js'
  },
  module: {
    rules: [
      {
        test: /\.vue$$/,
        loader: 'vue-loader',
        options: {
          loaders: {
            js: {
              loader: 'babel-loader',
              options: {
                babelrc: false,
                ...babelConfig
              }
            },
            scss: {
              loader: 'vue-style-loader!css-loader!sass-loader!sass-resources-loader',
              options: {
                resources: path.resolve(__dirname, './assets/style/main.scss')
              }
            }
          }
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        include: [
          path.resolve(__dirname, 'node_modules/vue-awesome'),
          path.resolve(__dirname, './'),
          path.resolve(clientPath.repo('/src'))
        ],
        options: {
          babelrc: false,
          ...babelConfig
        }
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
    ]
  },
  resolve: {
    alias: {
      '@': path.resolve(clientPath.repo('/src')),
      'vue$': 'vue/dist/vue.esm.js',
      'styles': path.resolve(__dirname, './assets/style'),
      'helios': path.resolve(__dirname, './'),
      'theme': path.resolve(__dirname, './'),
      'locale': path.resolve(clientPath.repo('/locale')),
      'apis': path.resolve(__dirname, './app/apis')
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devServer: {
    historyApiFallback: true,
    inline: true,
    watchOptions: {
      aggregateTimeout: 300,
      poll: 1000
    }
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'PUSHER_KEY': JSON.stringify(process.env.CES_SICES_PUSHER_KEY),
        'PUSHER_CLUSTER': JSON.stringify(process.env.CES_SICES_PUSHER_CLUSTER),
        'CLIENT': JSON.stringify(process.env.CLIENT),
        'PLATFORM': JSON.stringify(process.env.PLATFORM),
        'NODE_ENV': JSON.stringify(process.env.CES_AMBIENCE),
        'API_URL': JSON.stringify(process.env.CES_SICES_URI)
      }
    }),
    new HtmlWebpackPlugin({
      filename: 'index.html',
      template: 'index.html',
      inject: true,
      hash: true,
      minify: {
        removeComments: true,
        collapseWhitespace: true,
        removeAttributeQuotes: true
      }
    }),
    new webpack.HashedModuleIdsPlugin(),
    new webpack.optimize.ModuleConcatenationPlugin(),
    new webpack.optimize.CommonsChunkPlugin({
      name: 'vendor',
      minChunks (module) {
        return (
          module.resource &&
          /\.js$/.test(module.resource) &&
          module.resource.indexOf(
            path.join(__dirname, '../node_modules')
          ) === 0
        )
      }
    }),
    new webpack.optimize.CommonsChunkPlugin({
      name: 'manifest',
      minChunks: Infinity
    }),
    new webpack.optimize.CommonsChunkPlugin({
      name: 'app',
      async: 'vendor-async',
      children: true,
      minChunks: 3
    })
  ]
}

webpack(config, (err, stats) => {
  if (err) throw err

  process.stdout.write(stats.toString({
    colors: true,
    modules: false,
    children: false,
    chunks: false,
    chunkModules: false
  }) + '\n\n')

  if (stats.hasErrors()) {
    console.log('Build failed with erros')
    process.exit(1)
  }
})

module.exports = config;
