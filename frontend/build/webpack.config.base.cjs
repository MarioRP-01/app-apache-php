const { SRC, DIST } = require('./paths.cjs')
const MiniCssExtractPlugin = require('mini-css-extract-plugin')
const autoprefixer = require('autoprefixer')
// const devMode = process.env.NODE_ENV !== 'production'

const plugins = [
  new MiniCssExtractPlugin({
    filename: 'css/[name].css',
    chunkFilename: 'css/[id].css'
  })
]

const jsRules = {
  test: /\.m?js$/,
  exclude: /(node_modules)/,
  use: {
    loader: 'swc-loader'
  }
}

const cssRules = {
  test: /\.(s[ac]|c)ss$/i,
  use: [
    // Replace style-loader | Creates `style` nodes from JS strings
    MiniCssExtractPlugin.loader,
    // Translates CSS into CommonJS
    'css-loader',
    // PostCSS
    {
      // Loader for webpack to process CSS with PostCSS
      loader: 'postcss-loader',
      options: {
        postcssOptions: {
          plugins: [
            autoprefixer
          ]
        }
      }
    },
    // Resolve relative url() paths
    'resolve-url-loader',
    // Compiles Sass to CSS
    'sass-loader'
  ]
}

const iconsRules = {
  mimetype: 'image/svg+xml',
  scheme: 'data',
  type: 'asset/resource',
  generator: {
    filename: 'img/[hash].svg'
  }
}

module.exports = {
  context: SRC,
  entry: {
    main: { import: './main.js' },
    index: { import: './pages/index.js' }
  },
  output: {
    path: DIST,
    filename: 'js/[name].js'
  },
  module: {
    rules: [
      iconsRules,
      cssRules,
      jsRules
    ]
  },
  plugins
}
