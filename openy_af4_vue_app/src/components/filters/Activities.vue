<template>
  <div class="activities-filter-component">
    <Foldable
      v-for="(item_type, index) in filteredActivities"
      :key="id + '-activity-type-' + index"
      :label="item_type.label"
      :collapse-id="id + '-toggle-' + index"
      :counter="subFiltersCount(index)"
    >
      <div
        v-for="item in item_type.value"
        :key="id + '-activity-' + item.value"
        class="option"
        :class="{ check: multiple, radio: !multiple }"
      >
        <input
          :id="id + '-activity-' + item.value"
          v-model="selectedActivities"
          :type="multiple ? 'checkbox' : 'radio'"
          :value="item.value"
        />
        <label :for="id + '-activity-' + item.value">
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
      if (!this.excludeByCategory.length && !this.limitByCategory.length) {
        return this.activities
      }

      const filteredActivities = {}
      this.activities.forEach((activityGroup, key) => {
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
    }
  },
  watch: {
    value() {
      this.selectedActivities = this.multiple
        ? this.value
        : this.value.length
        ? this.value[0]
        : null
    },
    selectedActivities() {
      this.$emit('input', this.multiple ? this.selectedActivities : [this.selectedActivities])
    }
  },
  methods: {
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
    facetCount(value) {
      let facet = this.facets.find(x => x.id === value)
      return facet ? facet.count : 0
    }
  }
}
</script>
