<template>
  <Foldable
    :label="'Day(s) & Time(s)' | t"
    :collapse-id="id + '-toggle'"
    :counter="filtersCount"
    class="days-times-filter-component"
  >
    <FoldableInput
      v-for="(day, index) in daysTimes"
      :key="id + '-day-' + index"
      :label="day.search_value"
      :collapse-id="id + '-toggle-' + index"
      :counter="subFiltersCount(day.value)"
      :class="{
        checked: includesAll(day.value),
        semichecked: includesSome(day.value) && !includesAll(day.value)
      }"
      @input-click="onInputClick(day.value)"
    >
      <div v-for="time in day.value" :key="id + '-time-' + time.value" class="option">
        <input
          :id="id + '-time-' + time.value"
          v-model="selectedDaysTimes"
          type="checkbox"
          :value="time.value"
        />
        <label :for="id + '-time-' + time.value">
          <span>
            {{ time.label }} <span v-if="time.description">({{ time.description }})</span>
          </span>
        </label>
      </div>
    </FoldableInput>
  </Foldable>
</template>

<script>
import Foldable from '@/components/Foldable'
import FoldableInput from '@/components/FoldableInput'

export default {
  name: 'DaysTimesFilter',
  components: {
    Foldable,
    FoldableInput
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
    daysTimes: {
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
      selectedDaysTimes: this.value
    }
  },
  computed: {
    filtersCount() {
      return this.selectedDaysTimes.length
    }
  },
  watch: {
    value() {
      this.selectedDaysTimes = this.value
    },
    selectedDaysTimes() {
      this.$emit('input', this.selectedDaysTimes)
    }
  },
  methods: {
    facetCount(value) {
      let facet = this.facets.find(x => x.filter === value)
      return facet ? facet.count : 0
    },
    subFiltersCount(times) {
      let count = 0
      for (let time of times) {
        if (this.selectedDaysTimes.includes(time.value)) {
          count++
        }
      }
      return count
    },
    onInputClick(times) {
      if (this.includesAll(times)) {
        const values = times.map(x => x.value)
        this.selectedDaysTimes = this.selectedDaysTimes.filter(x => !values.includes(x))
      } else {
        const values = times.map(x => x.value).filter(x => !this.selectedDaysTimes.includes(x))
        // We need to work with new array here.
        this.selectedDaysTimes = this.selectedDaysTimes.slice()
        this.selectedDaysTimes.push(...values)
      }
    },
    includesAll(times) {
      return times.every(time => this.selectedDaysTimes.includes(time.value))
    },
    includesSome(times) {
      return times.some(time => this.selectedDaysTimes.includes(time.value))
    }
  }
}
</script>
