const { merge } = require('webpack-merge')

module.exports = merge(require('./webpack.config.base.cjs'), {
  mode: 'development',
  watch: true,
  devtool: 'source-map'
})
