<template>
  <div class="activities-filter-component">
    <Foldable
      v-for="(item_type, index) in activities"
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
    }
  },
  data() {
    return {
      selectedActivities: this.multiple ? this.value : this.value.length ? this.value[0] : null
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
