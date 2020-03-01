<template>
  <div class="locations-filter-component">
    <Foldable
      v-for="(item_type, index) in locations"
      :key="'location-type-' + index"
      :label="item_type.label"
      :collapse-id="collapseId + '-' + index"
      :counter="subFiltersCount(index)"
    >
      <div v-for="item in item_type.value" :key="item.value" class="option">
        <input
          :id="'location-' + item.value"
          v-model="selectedLocations"
          type="checkbox"
          :value="item.value"
          @change="onChange"
        />
        <label :for="'location-' + item.value">
          {{ item.label }}
        </label>
      </div>
    </Foldable>
  </div>
</template>

<script>
import Foldable from '@/components/Foldable'

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
    collapseId: {
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
    }
  },
  data() {
    return {
      selectedLocations: this.value
    }
  },
  watch: {
    value() {
      this.selectedLocations = this.value
    }
  },
  methods: {
    onChange() {
      this.$emit('input', this.selectedLocations)
    },
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
