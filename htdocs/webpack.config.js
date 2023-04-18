const Encore = require('@symfony/webpack-encore');
require("dotenv").config(); // line to add
const BrowserSyncPlugin = require("browser-sync-webpack-plugin"); // line to add
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}
Encore
    .setOutputPath('public/build/')
    .copyFiles({
        from: './assets/img/',
        to: 'img/[path][name].[ext]'
    })
    .setPublicPath('/build')
    .addEntry('main', './assets/js/main.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .autoProvidejQuery()
    .enableSassLoader()
    .enableVersioning()
    .enableSourceMaps(!Encore.isProduction());

module.exports = Encore.getWebpackConfig();