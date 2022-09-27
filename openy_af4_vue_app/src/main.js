import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import App from '@/App.vue'
import router from '@/router/index.js'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faFilter,
  faCalendar,
  faMoneyBill,
  faClock,
  faChevronDown,
  faChevronUp,
  faBookmark
} from '@fortawesome/free-solid-svg-icons'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'

library.add([faFilter, faCalendar, faMoneyBill, faClock, faChevronDown, faChevronUp, faBookmark])
Vue.component('font-awesome-icon', FontAwesomeIcon)

// Listen to custom event to track events in Google Analytics.
document.addEventListener('openy_activity_finder_event', e => {
  const { action, label, value, category } = e.detail

  if (window.gtag) {
    window.gtag('event', action, {
      event_category: category,
      event_label: label,
      value: value
    })
  } else if (window.ga) {
    window.ga('send', 'event', category, action, label, value)
  }
})

Vue.config.productionTip = false
Vue.use(BootstrapVue)

// Global filters.
Vue.filter('capitalize', function(str) {
  if (!str) return ''
  str = str.toString()
  return str[0].toUpperCase() + str.slice(1)
})
Vue.filter('t', function(value, args, options = { context: 'Activity Finder' }) {
  return window.Drupal.t(value, args, options)
})
Vue.filter('formatPlural', function(
  value,
  singular,
  plural,
  args,
  options = { context: 'Activity Finder' }
) {
  return window.Drupal.formatPlural(value, singular, plural, args, options)
})

Vue.mixin({
  computed: {
    isIosMobile() {
      return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream
    }
  },
  methods: {
    trackEvent(action, label, value = 0, category = 'Activity Finder') {
      // Custom event for external code to listen to and react upon.
      const event = new CustomEvent('openy_activity_finder_event', {
        detail: {
          action,
          label,
          value,
          category
        }
      })
      document.dispatchEvent(event)
    },
    t(value, args, options = { context: 'Activity Finder' }) {
      return window.Drupal.t(value, args, options)
    },
    formatPlural(value, singular, plural, args, options = { context: 'Activity Finder' }) {
      return window.Drupal.formatPlural(value, singular, plural, args, options)
    },
    getCookie(cname) {
      const name = cname + '='
      const decodedCookie = decodeURIComponent(document.cookie)
      const ca = decodedCookie.split(';')
      for (let i = 0; i < ca.length; i++) {
        let c = ca[i]
        while (c[0] === ' ') {
          c = c.slice(1)
        }
        if (c.startsWith(name)) {
          return c.slice(name.length, c.length)
        }
      }
      return ''
    }
  }
})

new Vue({
  router,
  components: {
    'activity-finder': App
  }
}).$mount('#activity-finder')
