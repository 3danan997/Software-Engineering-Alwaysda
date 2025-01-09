module.exports = {
  pwa: {
    // configure the workbox plugin
    workboxPluginMode: 'InjectManifest',
    workboxOptions: {
      swSrc: "public/firebase-messaging-sw.js"
    }
  },
  optimization: {
    nodeEnv: 'production',
    minimize: true
  },
  css: {
    loaderOptions: {
      sass: {
        additionalData: `@import "~@/assets/css/variables.scss"`
      },
      scss: {
        additionalData: `@import "~@/assets/css/variables.scss";`
      },
    }
  },
  transpileDependencies: [
    'vuetify',
  ]
}
