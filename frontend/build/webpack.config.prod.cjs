const { merge } = require('webpack-merge')
const path = require('path')

module.exports = merge(require('./webpack.config.base.cjs'), {
  mode: 'production',
  output: {
    path: path.resolve(__dirname, '..', 'dist'),
    filename: 'js/[name].js'
  }
})
