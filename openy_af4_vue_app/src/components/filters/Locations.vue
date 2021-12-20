<template>
  <div class="locations-filter-component">
    <Foldable
      v-for="(item_type, index) in filteredLocations"
      :key="id + '-location-type-' + index"
      :label="item_type.label"
      :collapse-id="id + '-toggle-' + index"
      :counter="subFiltersCount(index)"
    >
      <div v-for="item in item_type.value" :key="id + '-location-' + item.value" class="option">
        <input
          :id="id + '-location-' + item.value"
          v-model="selectedLocations"
          type="checkbox"
          :value="item.value"
        />
        <label :for="id + '-location-' + item.value">
          {{ item.label }}
        </label>
      </div>
    </Foldable>
  </div>
</template>

<script>
import Foldable from '@/components/Foldable.vue'

export default {
  name: 'LocationsFilter',
  components: {
    Foldable
  },
  props: {
    value: {
      type: Array,
      required: true
    },
    id: {
      type: String,
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
      if (!this.excludeByLocation.length) {
        return this.locations
      }
      const filteredLocations = {}
      this.locations.forEach((locationGroup, key) => {
        // Filter out excluded locations.
        if (!this.excludeByLocation.length) {
          filteredLocations[key] = locationGroup
          return
        }
        let checkIsArray = Array.isArray(locationGroup.value)
        let results = new Object()
        if (!checkIsArray) {
          let filteredLocationIndex = Object.keys(locationGroup.value).filter(key => {
            return !this.excludeByLocation.includes(locationGroup.value[key].value.toString())
          })
          filteredLocationIndex.forEach(locationIndex => {
            results[locationIndex] = locationGroup.value[locationIndex]
          })
        } else {
          results = locationGroup.value.filter(item => {
            return !this.excludeByLocation.includes(item.value.toString())
          })
        }
        filteredLocations[key] = {
          ...locationGroup,
          value: results
        }
      })
      return filteredLocations
    }
  },
  watch: {
    value() {
      this.selectedLocations = this.value
    },
    selectedLocations() {
      this.$emit('input', this.selectedLocations)
    }
  },
  methods: {
    subFiltersCount(index) {
      let result = 0
      this.selectedLocations.forEach(item => {
        if (this.locations[index].value.find(location => location.value === item)) {
          result++
        }
      })
      return result
    },
    facetCount(value) {
      let facet = this.facets.find(x => x.id === value)
      return facet ? facet.count : 0
    }
  }
}
</script>
