<template>
  <div class="select-days-component">
    <Step
      skip-label="Any day (Skip)"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        What days are you looking to fill?
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          label="Day(s)"
          :collapsible="false"
          :counter="filtersCount"
          :counter-options="optionsCount"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div
                v-for="day in days"
                :key="day.search_value"
                class="option check col-xs-6 col-sm-3"
              >
                <input
                  :id="day.search_value"
                  v-model="selectedDays"
                  type="checkbox"
                  :value="day.value"
                  :disabled="isDisabled(day.search_value)"
                  @change="onChange(day)"
                />
                <label :for="day.search_value" role="button">
                  <span>
                    <span class="title">{{ day.search_value | capitalize }}</span>
                    <span class="results-count">
                      {{
                        facetCount(day.search_value) | formatPlural('1 Result', '@count Results')
                      }}
                    </span>
                  </span>
                </label>
              </div>
            </div>
          </div>
        </Fieldset>
      </template>
    </Step>
  </div>
</template>

<script>
import Fieldset from '@/components/Fieldset.vue'
import Step from '@/components/steps/Step.vue'

export default {
  name: 'SelectDays',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    days: {
      type: Array,
      required: true
    },
    facets: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedDays: this.value
    }
  },
  computed: {
    filtersSelected() {
      return this.value.length >= 1
    },
    filtersCount() {
      return this.value.length
    },
    optionsCount() {
      let count = 0
      for (let key in this.days) {
        if (this.facetCount(this.days[key].search_value)) {
          count++
        }
      }
      return count
    }
  },
  watch: {
    value() {
      this.selectedDays = this.value
    }
  },
  methods: {
    onChange(day) {
      this.trackEvent('selectDays', 'Click on day ' + day.search_value, day.value)
      this.$emit('input', this.selectedDays)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectDays')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectDays')
      this.$emit('nextStep')
    },
    facetCount(value) {
      let facet = this.facets.find(x => x.filter === value)
      return facet && facet.count ? facet.count : 0
    },
    isDisabled(value) {
      return this.facetCount(value) === 0
    }
  }
}
</script>