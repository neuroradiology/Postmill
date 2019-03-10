'use strict';

const Encore = require('@symfony/webpack-encore');
const merge = require('webpack-merge');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableLessLoader()
    .enableSourceMaps(!Encore.isProduction())
    .addStyleEntry('postmill', './assets/less/postmill.css')
    .addStyleEntry('postmill-night', './assets/less/postmill-night.css')
    .addStyleEntry('core', './assets/less/core.less')
    .createSharedEntry('vendor', [
        'babel-polyfill',
        'bazinga-translator',
        'date-fns/distance_in_words',
        'date-fns/distance_in_words_to_now',
        'date-fns/is_before',
        'jquery',
        'lodash.debounce',
    ])
    .addEntry('main', './assets/js/main.js')
    .configureBabel(babelConfig => {
        babelConfig.presets.push(['es2015', { modules: false }]);
        babelConfig.plugins = ['syntax-dynamic-import'];
    })
    .enableVersioning();

module.exports = merge(Encore.getWebpackConfig(), {
    externals: {
        "fosjsrouting": "Routing",
    },
});
