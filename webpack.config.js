const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {
  const isProduction = argv.mode === 'production';
  
  return {
    entry: {
      viewer: './js/viewer.js'
    },
    output: {
      path: path.resolve(__dirname, 'js/dist'),
      filename: '[name].bundle.js',
      clean: true
    },
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          use: {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env']
            }
          }
        },
        {
          test: /\.css$/,
          use: [
            MiniCssExtractPlugin.loader,
            'css-loader'
          ]
        }
      ]
    },
    plugins: [
      new MiniCssExtractPlugin({
        filename: '../css/[name].bundle.css'
      })
    ],
    resolve: {
      extensions: ['.js', '.json']
    },
    devtool: isProduction ? false : 'source-map',
    optimization: {
      minimize: isProduction
    }
  };
}; 