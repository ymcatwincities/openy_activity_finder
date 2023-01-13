<template>
  <div class="select-activities-component">
    <Step
      :skip-label="'Any activity (Skip)' | t"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        {{ 'What activities are you interested in?' | t }}
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          v-for="(activity_type, index) in filteredActivities"
          :key="index"
          :label="activity_type.label"
          :collapse-id="'accordion-' + index"
          :counter="subFiltersCount(index)"
          :counter-options="optionsCount(index)"
          accordion="accordion-activities"
          :collapsible="Object.keys(filteredActivities).length !== 1"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div
                v-for="activity in activity_type.value"
                :key="activity.value"
                class="option col-12 col-xs-12 col-sm-3"
                :class="{ check: multiple, radio: !multiple }"
              >
                <input
                  :id="activity.value"
                  v-model="selectedActivities"
                  :type="multiple ? 'checkbox' : 'radio'"
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
    },
    firstStep: {
      type: Boolean,
      default: false
    },
    multiple: {
      type: Boolean,
      default: true
    },
    limitByCategory: {
      type: Array,
      required: true
    },
    excludeByCategory: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedActivities: this.multiple ? this.value : this.value.length ? this.value[0] : null
    }
  },
  computed: {
    filteredActivities() {
      if (!this.firstStep && !this.excludeByCategory.length && !this.limitByCategory.length) {
        return this.activities
      }

      const filteredActivities = {}
      this.activities.forEach((activityGroup, key) => {
        // Filter out activity groups with empty facets if it is the first step.
        if (this.firstStep && !this.optionsCount(key)) {
          return
        }
        
        // Filter out excluded or limited categories.
        if (!this.excludeByCategory.length || !this.limitByCategory.length) {
          filteredActivities[key] = activityGroup
          return
        }

        const filteredValue = activityGroup.value.filter(item => {
          let r = false
          // Items must pass both tests, so we intentionally do not ELSE these.
          // If there are excludes, the item must NOT be excluded.
          if (this.excludeByCategory.length) {
            r = !this.excludeByCategory.includes(item.value.toString())
          }
          // If there are limits, ONLY items in the limit list are included.
          if (this.limitByCategory.length) {
            r = this.limitByCategory.includes(item.value.toString())
          }
          return r
        })
        // If there are no filtered values then the activityGroup is empty,
        // and we should not add it to the filteredActivities array.
        if (!filteredValue.length) {
          return
        }

        filteredActivities[key] = {
          ...activityGroup,
          value: filteredValue
        }
      })
      return filteredActivities
    },
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
      this.selectedActivities = this.multiple
        ? this.value
        : this.value.length
        ? this.value[0]
        : null
    }
  },
  methods: {
    onChange(activity) {
      this.trackEvent('selectActivities', 'Click on activity ' + activity.label, activity.value)
      this.$emit('input', this.multiple ? this.selectedActivities : [this.selectedActivities])
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
          this.activities[index].value.find(activity => String(activity.value) === String(item))
        ) {
          result++
        }
      })
      return result
    },
    optionsCount(index) {
      let count = 0
      for (let key in this.activities[index].value) {
        count += this.facetCount(this.activities[index].value[key].value)
      }
      return count
    }
  }
}
</script>
