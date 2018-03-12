'use strict'

const HtmlWebpackPlugin = require('html-webpack-plugin')
const path = require('path')
const webpack = require('webpack')
const axios = require('axios')

const host = 'http://localhost:8000'
const sessid = process.env.SICES_PHPSESSID

;(() => {
  const testURL = `${host}/api/v1/coupon`

  if (!sessid) {
    console.error('ERROR: PHPSESSID env variable not exported')
    process.exit(1)
  }

  axios.get(testURL, {
    headers: {
      Cookie: `PHPSESSID=${sessid};`
    }
  }).then(response => {
    if (typeof response.data !== 'object') {
      console.error('ERROR: Session has been expired, please, export a new PHPSESSID')
      process.exit(1)
    }
  }).catch(error => {
    console.error(`ERROR: Could not connect to server, error message: ${error.message}`)
    process.exit(1)
  })
})()

module.exports = {
  entry: './src/main.js',
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
              loader: 'babel-loader'
            },
            scss: {
              loader: 'vue-style-loader!css-loader!sass-loader!sass-resources-loader',
              options: {
                resources: path.resolve(__dirname, './src/assets/style/main.scss')
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
          path.resolve('node_modules/vue-awesome')
        ]
      },
      {
        test: /\.css$/,
        use: [
          'vue-style-loader',
          'css-loader'
        ],
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
      '@': path.resolve(__dirname, './src'),
      'vue$': 'vue/dist/vue.esm.js',
      'styles': path.resolve(__dirname, './src/assets/style/')
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
        'AMBIENCE': JSON.stringify('development'),
        'API_URL': JSON.stringify(host),
        'PHPSESSID': JSON.stringify(sessid)
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
