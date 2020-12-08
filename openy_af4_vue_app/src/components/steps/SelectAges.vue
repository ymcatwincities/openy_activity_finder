<template>
  <div class="select-ages-component">
    <Step
      :skip-label="'All ages (Skip)' | t"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        {{ 'What ages are you searching for?' | t }}
        <strong v-if="maxAges">{{ 'Maximum of !maxAges.' | t({ '!maxAges': maxAges }) }} </strong>
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          label="Age(s)"
          :collapsible="false"
          :counter="filtersCount"
          :counter-max="maxAges"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div v-for="age in ages" :key="age.value" class="option check col-6 col-xs-6 col-sm-3">
                <input
                  :id="age.value"
                  v-model="selectedAges"
                  type="checkbox"
                  :value="age.value"
                  :disabled="isDisabled(age.value)"
                  @change="onChange(age)"
                />
                <label :id="'label-' + age.value" :for="age.value" role="button">
                  <span>
                    <span class="title">{{ age.label }}</span>
                    <span class="results-count">
                      {{ facetCount(age.value) | formatPlural('1 Result', '@count Results') }}
                    </span>
                  </span>
                </label>
                <b-tooltip v-if="isTooltipDisplay(age.value)" :target="'label-' + age.value">
                  {{ 'Please unselect any of the selected options first' | t }}
                </b-tooltip>
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
  name: 'SelectAges',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    ages: {
      type: Array,
      required: true
    },
    maxAges: {
      type: Number,
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
      selectedAges: this.value
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
      for (let key in this.ages) {
        count += this.facetCount(this.ages[key].value)
      }
      return count
    }
  },
  watch: {
    value() {
      this.selectedAges = this.value
    }
  },
  methods: {
    onChange(age) {
      this.trackEvent('selectAges', 'Click on age ' + age.label, age.value)
      this.$emit('input', this.selectedAges)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectAges')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectAges')
      this.$emit('nextStep')
    },
    facetCount(value) {
      let facet = this.facets.find(x => x.value === value)
      return facet && facet.count ? facet.count : 0
    },
    isDisabled(value) {
      return !!(
        this.facetCount(value) === 0 ||
        (this.maxAges && this.value.length >= this.maxAges && !this.value.includes(value))
      )
    },
    isTooltipDisplay(value) {
      return !!(
        this.maxAges &&
        this.value.length >= this.maxAges &&
        !this.value.includes(value) &&
        this.facetCount(value) > 0
      )
    }
  }
}
</script>
