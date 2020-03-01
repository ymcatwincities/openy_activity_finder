import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from '@/App.vue'
import router from '@/router/index.js'

Vue.config.productionTip = false
Vue.use(BootstrapVue)

// Global filters.
Vue.filter('capitalize', function(str) {
  if (!str) return ''
  str = str.toString()
  return str[0].toUpperCase() + str.slice(1)
})
Vue.filter('t', function(value, args, options) {
  return window.Drupal.t(value, args, options)
})
Vue.filter('formatPlural', function(value, singular, plural, args, options) {
  return window.Drupal.formatPlural(value, singular, plural, args, options)
})

Vue.mixin({
  methods: {
    // Global mixin to track events in Google Analytics.
    trackEvent(action, label, value = 0, category = 'Activity Finder') {
      if (window.gtag) {
        window.gtag('event', action, {
          event_category: category,
          event_label: label,
          value: value
        })
      } else if (window.ga) {
        window.ga('send', 'event', category, action, label, value)
      }
    },
    t(value, args, options) {
      return window.Drupal.t(value, args, options)
    },
    formatPlural(value, singular, plural, args, options) {
      return window.Drupal.formatPlural(value, singular, plural, args, options)
    }
  }
})

new Vue({
  router,
  components: {
    'activity-finder': App
  }
}).$mount('#activity-finder')
