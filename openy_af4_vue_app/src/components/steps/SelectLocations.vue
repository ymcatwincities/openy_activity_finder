<template>
  <div class="select-locations-component">
    <Step
      :skip-label="'All locations (Skip)' | t"
      :filters-selected="filtersSelected"
      @skip="onSkip"
      @next="onNext"
    >
      <template v-slot:title>
        {{ 'Do you have any location preferences?' | t }}
      </template>
      <template v-slot:default="{ handleSticky }">
        <Fieldset
          v-for="(location_type, index) in filteredLocations"
          :key="index"
          :label="location_type.label"
          :collapse-id="'accordion-' + index"
          :counter="subFiltersCount(index)"
          :counter-options="optionsCount(index)"
          accordion="accordion-locations"
          :collapsed="true"
          :handle-sticky="handleSticky"
        >
          <div class="options">
            <div class="row">
              <div
                v-for="location in location_type.value"
                :key="location.value"
                class="option check col-12 col-xs-12 col-sm-4"
              >
                <input
                  :id="location.value"
                  v-model="selectedLocations"
                  type="checkbox"
                  :value="location.value"
                  :disabled="isDisabled(location.value)"
                  @change="onChange(location)"
                />
                <label :for="location.value" role="button">
                  <span>
                    <span class="title">{{ location.label }}</span>
                    <span class="results-count">
                      {{ facetCount(location.value) | formatPlural('1 Result', '@count Results') }}
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
  name: 'SelectLocations',
  components: {
    Fieldset,
    Step
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    locations: {
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
    homeBranchId: {
      type: String,
      default: null
    },
    excludeByLocation: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedLocations: this.value
    }
  },
  computed: {
    filteredLocations() {
      if (!this.firstStep && !this.excludeByLocation.length) {
        return this.locations
      }

      const filteredLocations = {}
      for (let key in this.locations) {
        if (this.optionsCount(key) > 0) {
          filteredLocations[key] = this.locations[key]
        }
      }
      if (!this.excludeByLocation.length) {
        return this.locations
      }
      this.locations.forEach((locationGroup, key) => {
        // Filter out excluded locations.
        if (!this.excludeByLocation.length) {
          filteredLocations[key] = locationGroup
          return
        }
        const filteredValue = locationGroup.value.filter(item => {
          return !this.excludeByLocation.includes(item.value.toString())
        })
        if (!filteredValue.length) {
          return
        }

        filteredLocations[key] = {
          ...locationGroup,
          value: filteredValue
        }
      })
      return filteredLocations
    },
    filtersSelected() {
      return this.value.length >= 1
    }
  },
  watch: {
    value() {
      this.selectedLocations = this.value
    }
  },
  mounted() {
    if (!this.homeBranchId) {
      return
    }
    if (this.selectedLocations.length) {
      return
    }
    if (!this.facetCount(this.homeBranchId)) {
      return
    }
    this.selectedLocations.push(this.homeBranchId)
  },
  methods: {
    onChange(location) {
      this.trackEvent('selectLocations', 'Click on location ' + location.label, location.value)
      this.$emit('input', this.selectedLocations)
    },
    onSkip() {
      this.trackEvent('skip', 'Click on selectLocations')
      this.$emit('input', [])
      this.$emit('nextStep')
    },
    onNext() {
      this.trackEvent('next', 'Click on selectLocations')
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
        if (this.locations[index].value.find(location => location.value === item)) {
          result++
        }
      })
      return result
    },
    optionsCount(index) {
      let count = 0
      for (let key in this.locations[index].value) {
        count += this.facetCount(this.locations[index].value[key].value)
      }
      return count
    }
  }
}
</script>
