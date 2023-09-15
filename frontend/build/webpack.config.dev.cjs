const { merge } = require('webpack-merge')

module.exports = merge(require('./webpack.config.base.cjs'), {
  mode: 'development',
  output: {
    path: '/backend/public',
    filename: 'js/[name].js'
  },
  watch: true,
  devtool: 'source-map'
})
