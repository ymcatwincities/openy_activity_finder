<template>
  <div class="select-times-component">
    <Step
      :skip-label="'Any time (Skip)' | t"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        {{ 'What times are you looking to fill?' | t }}
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          :label="'Time(s)' | t"
          :collapsible="false"
          :counter="filtersCount"
          :counter-options="optionsCount"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div v-for="time in times" :key="time.value" class="option check col-xs-12 col-sm-4">
                <input
                  :id="time.value"
                  v-model="selectedTimes"
                  type="checkbox"
                  :value="time.value"
                  :disabled="isDisabled(time.value)"
                  @change="onChange(time)"
                />
                <label :for="time.value" role="button">
                  <span>
                    <span class="title">{{ time.label }}</span>
                    <span class="results-count">
                      {{ facetCount(time.value) | formatPlural('1 Result', '@count Results') }}
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
  name: 'SelectTimes',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    times: {
      type: Array,
      required: true
    },
    facets: {
      type: Array,
      required: true
    },
    firstStep: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedTimes: this.value
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
      for (let key in this.times) {
        count += this.facetCount(this.times[key].value)
      }
      return count
    }
  },
  watch: {
    value() {
      this.selectedTimes = this.value
    }
  },
  methods: {
    onChange(time) {
      this.trackEvent('selectTimes', 'Click on time ' + time.label, time.value)
      this.$emit('input', this.selectedTimes)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectTimes')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectTimes')
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
