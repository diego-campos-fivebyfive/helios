'use strict'

const HtmlWebpackPlugin = require('html-webpack-plugin')
const path = require('path')
const webpack = require('webpack')
const babelConfig = require('./.babel.config.js')

module.exports = {
  entry: './app/dev-main.js',
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
          path.resolve(__dirname, './../../src')
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
      '@': path.resolve(__dirname, './../../src'),
      'vue$': 'vue/dist/vue.esm.js',
      'styles': path.resolve(__dirname, './assets/style'),
      'helios': path.resolve(__dirname, './'),
      'theme': path.resolve(__dirname, './'),
      'locale': path.resolve(__dirname, './../../locale'),
      'apis': path.resolve(__dirname, './app/apis')
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devtool: 'inline-cheap-module-source-map',
  devServer: {
    historyApiFallback: true,
    inline: true,
    watchOptions: {
      aggregateTimeout: 300,
      poll: 1000
    }
  },
  plugins: [
    new webpack.LoaderOptionsPlugin({
      debug: true
    }),
    new webpack.DefinePlugin({
      'process.env': {
        'PUSHER_KEY': JSON.stringify(process.env.CES_SICES_PUSHER_KEY),
        'PUSHER_CLUSTER': JSON.stringify(process.env.CES_SICES_PUSHER_CLUSTER),
        'CLIENT': JSON.stringify(process.env.CLIENT),
        'PLATFORM': JSON.stringify(process.env.PLATFORM),
        'NODE_ENV': JSON.stringify(process.env.CES_AMBIENCE),
        'API_URL': JSON.stringify(process.env.CES_SICES_URI),
        'SOCKET_URL': JSON.stringify((process.env.CES_AMBIENCE === 'development')
          ? `${process.env.CES_SICES_SOCKET_HOST}:${process.env.CES_SICES_SOCKET_PORT}`
          : `${process.env.CES_SICES_SOCKET_HOST}`)
      }
    }),
    new HtmlWebpackPlugin({
      filename: 'index.html',
      template: 'index.html',
      inject: true,
      hash: true
    })
  ]
}
