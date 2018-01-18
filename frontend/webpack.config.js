module.exports = {
  entry: './src/main.js',
  output: {
    filename: 'bundle.js'
  },
  resolve: {
    alias: {
      vue: 'vue/dist/vue.js'
    }
  },
  module: {
    rules: [
      {
        test: /\.vue$$/,
        loader: 'vue-loader'
      }
    ]
  }
}
