<template>
  <Foldable
    :label="'Time(s)' | t"
    :collapse-id="id + '-toggle'"
    :counter="filtersCount"
    class="times-filter-component"
  >
    <div v-for="time in times" :key="id + '-time-' + time.value" class="option">
      <input
        :id="id + '-time-' + time.value"
        v-model="selectedTimes"
        type="checkbox"
        :value="time.value"
      />
      <label :for="id + '-time-' + time.value">{{ time.label }}</label>
    </div>
  </Foldable>
</template>

<script>
import Foldable from '@/components/Foldable'

export default {
  name: 'TimesFilter',
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
    times: {
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
      selectedTimes: this.value
    }
  },
  computed: {
    filtersCount() {
      return this.selectedTimes.length
    }
  },
  watch: {
    value() {
      this.selectedTimes = this.value
    },
    selectedTimes() {
      this.$emit('input', this.selectedTimes)
    }
  },
  methods: {
    facetCount(value) {
      let facet = this.facets.find(x => x.filter === value)
      return facet ? facet.count : 0
    }
  }
}
</script>
