'use strict'

const path = require('path')
const webpack = require('webpack')
const HtmlWebpackPlugin = require('html-webpack-plugin')
const ExtractTextPlugin = require('extract-text-webpack-plugin')
const OptimizeCSSPlugin = require('optimize-css-assets-webpack-plugin')
const UglifyJsPlugin = require('uglifyjs-webpack-plugin')
const babelConfig = require('./.babel.config.js')

const config = {
  entry: './app/main.js',
  output: {
    path: path.resolve(__dirname, `../../../backend/web/app/${process.env.CLIENT}`),
    publicPath: `/app/${process.env.CLIENT}/`,
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
            css: ExtractTextPlugin.extract({
              use: [
                {
                  loader: 'css-loader',
                  options: { sourceMap: true, minimize: true }
                },
                {
                  loader: 'postcss-loader',
                  options: { sourceMap: true }
                }
              ],
              fallback: 'vue-style-loader'
            }),
            postcss: ExtractTextPlugin.extract({
              use: [
                {
                  loader: 'css-loader',
                  options: { sourceMap: true, minimize: true }
                },
                {
                  loader: 'postcss-loader',
                  options: { sourceMap: true }
                }
              ],
              fallback: 'vue-style-loader'
            }),
            scss: ExtractTextPlugin.extract({
              use: [
                {
                  loader: 'css-loader',
                  options: {
                    minimize: true,
                    sourceMap: true,
                    resources: path.resolve(__dirname, './assets/style/main.scss')
                  }
                },
                {
                  loader: 'postcss-loader',
                  options: {
                    sourceMap: true,
                    resources: path.resolve(__dirname, './assets/style/main.scss')
                  }
                },
                {
                  loader: 'sass-loader',
                  options: {
                    sourceMap: true,
                    resources: path.resolve(__dirname, './assets/style/main.scss')
                  }
                },
                {
                  loader: 'sass-resources-loader',
                  options: {
                    sourceMap: true,
                    resources: path.resolve(__dirname, './assets/style/main.scss')
                  }
                }
              ],
              fallback: 'vue-style-loader'
            })
          }
        }
      },
      {
        test: /\.js$/,
        loader: 'babel-loader',
        exclude: /node_modules/,
        include: [
          path.resolve(__dirname, 'node_modules/vue-awesome'),
          path.resolve(__dirname, './../../src'),
          path.resolve(__dirname, './')
        ],
        options: {
          babelrc: false,
          ...babelConfig
        }
      },
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: { sourceMap: true, minimize: true }
            },
            {
              loader: 'postcss-loader',
              options: { sourceMap: true }
            }
          ],
          fallback: 'vue-style-loader'
        })
      },
      {
        test: /\.postcss$/,
        use: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: { sourceMap: true, minimize: true }
            },
            {
              loader: 'postcss-loader',
              options: { sourceMap: true }
            }
          ],
          fallback: 'vue-style-loader'
        })
      },
      {
        test: /\.scss$/,
        use: ExtractTextPlugin.extract({
          use: [
            {
              loader: 'css-loader',
              options: { sourceMap: true, minimize: true }
            },
            {
              loader: 'postcss-loader',
              options: { sourceMap: true }
            },
            {
              loader: 'sass-loader',
              options: { sourceMap: true }
            }
          ],
          fallback: 'vue-style-loader'
        })
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
      'vue$': 'vue/dist/vue.min.js',
      'styles': path.resolve(__dirname, './assets/style'),
      'helios': path.resolve(__dirname, './'),
      'theme': path.resolve(__dirname, './'),
      'locale': path.resolve(__dirname, './../../locale'),
      'apis': path.resolve(__dirname, './app/apis')
    },
    extensions: ['*', '.js', '.vue', '.json']
  },
  devServer: {
    historyApiFallback: true,
    inline: true
  },
  plugins: [
    new webpack.DefinePlugin({
      'process.env': {
        'CLIENT': JSON.stringify(process.env.CLIENT),
        'PLATFORM': JSON.stringify(process.env.PLATFORM),
        'NODE_ENV': JSON.stringify(process.env.CES_AMBIENCE),
        'API_URL': JSON.stringify(process.env.CES_SICES_URI),
        'SOCKET_URL': JSON.stringify((process.env.CES_AMBIENCE === 'development')
          ? `${process.env.CES_SICES_SOCKET_HOST}:${process.env.CES_SICES_SOCKET_PORT}`
          : `${process.env.CES_SICES_SOCKET_HOST}`),
      }
    }),
    new UglifyJsPlugin({
      uglifyOptions: {
        compress: {
          warnings: false
        }
      },
      sourceMap: true,
      parallel: true
    }),
    new ExtractTextPlugin({
      filename: '[name].css',
      allChunks: true
    }),
    new OptimizeCSSPlugin({
      assetNameRegExp: /\.css$/g,
      cssProcessor: require('cssnano'),
      cssProcessorOptions: { discardComments: { removeAll: true } },
      canPrint: true
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
