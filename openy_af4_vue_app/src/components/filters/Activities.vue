<template>
  <div class="activities-filter-component">
    <Foldable
      v-for="(item_type, index) in activities"
      :key="'activity-type-' + index"
      :label="item_type.label"
      :collapse-id="collapseId + '-' + index"
      :counter="subFiltersCount(index)"
    >
      <div v-for="item in item_type.value" :key="item.value" class="option">
        <input
          :id="'activity-' + item.value"
          v-model="selectedActivities"
          type="checkbox"
          :value="item.value"
        />
        <label :for="'activity-' + item.value">
          {{ item.label }}
        </label>
      </div>
    </Foldable>
  </div>
</template>

<script>
import Foldable from '@/components/Foldable'

export default {
  name: 'ActivitiesFilter',
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
  watch: {
    value() {
      this.selectedActivities = this.value
    },
    selectedActivities() {
      this.$emit('input', this.selectedActivities)
    }
  },
  methods: {
    subFiltersCount(index) {
      let result = 0
      this.selectedActivities.forEach(item => {
        if (
          this.activities[index].value.find(activity => parseInt(activity.value) === parseInt(item))
        ) {
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
