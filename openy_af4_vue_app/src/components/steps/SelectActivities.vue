<template>
  <div class="select-activities-component">
    <Step
      skip-label="Any activity (Skip)"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        What activities are you interested in?
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          v-for="(activity_type, index) in activities"
          :key="index"
          :label="activity_type.label"
          :collapse-id="'accordion-' + index"
          :counter="subFiltersCount(index)"
          :counter-options="optionsCount(index)"
          accordion="accordion-activities"
          :collapsed="index != firstItemWithOptions"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div
                v-for="activity in activity_type.value"
                :key="activity.value"
                class="option check col-xs-12 col-sm-3"
              >
                <input
                  :id="activity.value"
                  v-model="selectedActivities"
                  type="checkbox"
                  :value="activity.value"
                  :disabled="isDisabled(activity.value)"
                  @change="onChange(activity)"
                />
                <label :for="activity.value" role="button">
                  <span>
                    <span class="title">{{ activity.label }}</span>
                    <span class="results-count">
                      {{ facetCount(activity.value) | formatPlural('1 Result', '@count Results') }}
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
  name: 'SelectActivities',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    activities: {
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
      selectedActivities: this.value
    }
  },
  computed: {
    filtersSelected() {
      return this.value.length >= 1
    },
    firstItemWithOptions() {
      for (let key in this.activities) {
        if (this.optionsCount(key) > 0) {
          return key
        }
      }
      return 0
    }
  },
  watch: {
    value() {
      this.selectedActivities = this.value
    }
  },
  methods: {
    onChange(activity) {
      this.trackEvent('selectActivities', 'Click on activity ' + activity.label, activity.value)
      this.$emit('input', this.selectedActivities)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectActivities')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectActivities')
      this.$emit('nextStep')
    },
    facetCount(value) {
      let facet = this.facets.find(x => x.id === value)
      return facet && facet.count ? facet.count : 0
    },
    isDisabled(value) {
      return this.facetCount(value) === 0
    },
    subFiltersCount(index) {
      let result = 0
      this.value.forEach(item => {
        if (
          this.activities[index].value.find(activity => parseInt(activity.value) === parseInt(item))
        ) {
          result++
        }
      })
      return result
    },
    optionsCount(index) {
      let count = 0
      for (let key in this.activities[index].value) {
        if (this.facetCount(this.activities[index].value[key].value)) {
          count++
        }
      }
      return count
    }
  }
}
</script>
