module.exports = {
  presets: [
    ['es2015', {
      targets: {
        browsers: ['> 1%', 'last 2 versions', 'not ie <= 8']
      }
    }]
  ],
  plugins: [
    ['transform-object-rest-spread', { useBuiltIns: true }]
  ]
}
