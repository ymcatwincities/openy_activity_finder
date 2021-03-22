<template>
  <div class="select-weeks-component">
    <Step
      :skip-label="'Any week (Skip)' | t"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        {{ 'When are you interested in attending camp?' | t }}
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          label="Week(s)"
          :collapsible="false"
          :counter="filtersCount"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div
                v-for="week in weeks"
                :key="week.value"
                class="option check col-6 col-xs-6 col-sm-3"
              >
                <input
                  :id="week.value"
                  v-model="selectedWeeks"
                  type="checkbox"
                  :value="week.value"
                  :disabled="isDisabled(week.value)"
                  @change="onChange(week)"
                />
                <label :id="'label-' + week.value" :for="week.value" role="button">
                  <span>
                    <span class="title">{{ week.label }}</span>
                    <span v-if="facetCount(week.value) !== null" class="results-count">
                      {{ facetCount(week.value) | formatPlural('1 Result', '@count Results') }}
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
  name: 'SelectWeeks',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    weeks: {
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
      selectedWeeks: this.value
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
      for (let key in this.weeks) {
        count += this.facetCount(this.weeks[key].value)
      }
      return count
    }
  },
  watch: {
    value() {
      this.selectedWeeks = this.value
    }
  },
  methods: {
    onChange(week) {
      this.trackEvent('selectWeeks', 'Click on week ' + week.label, week.value)
      this.$emit('input', this.selectedWeeks)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectWeeks')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectWeeks')
      this.$emit('nextStep')
    },
    facetCount(value) {
      if (this.facets.length === 0) {
        return null
      }
      let facet = this.facets.find(x => x.value === value)
      return facet && facet.count ? facet.count : 0
    },
    isDisabled(value) {
      return this.facetCount(value) === 0
    }
  }
}
</script>
