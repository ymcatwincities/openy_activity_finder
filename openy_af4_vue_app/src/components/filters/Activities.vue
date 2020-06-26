<template>
  <div class="activities-filter-component">
    <Foldable
      v-for="(item_type, index) in filteredActivities"
      :key="id + '-activity-type-' + index"
      :label="item_type.label"
      :collapse-id="id + '-toggle-' + index"
      :counter="subFiltersCount(index)"
    >
      <div v-for="item in item_type.value" :key="id + '-activity-' + item.value" class="option">
        <input
          :id="id + '-activity-' + item.value"
          v-model="selectedActivities"
          type="checkbox"
          :value="item.value"
          @change="onChange()"
        />
        <label :for="id + '-activity-' + item.value">{{ item.label }}</label>
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
    id: {
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
  computed: {
    filteredActivities() {
      const filteredActivities = {}
      for (let key in this.activities) {
        if (this.isNotEmptyProgramm(key)) {
          filteredActivities[key] = this.activities[key]
        }
      }
      return filteredActivities
    }
  },
  watch: {
    value() {
      this.selectedActivities = this.value
    }
  },
  methods: {
    onChange() {
      this.$emit('input', this.selectedActivities)
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
    facetCount(value) {
      let facet = this.facets.find(x => x.id === value)
      return facet ? facet.count : 0
    },
    isNotEmptyProgramm(index) {
      for (let key in this.activities[index].value) {
        if (this.facetsFind(this.activities[index].value[key].value)) {
          return true
        }
      }
      return false
    },
    facetsFind(value) {
      return this.facets.find(x => x.id === value)
    }
  }
}
</script>
