<template>
  <Foldable
    :label="'Week(s)' | t"
    :collapse-id="id + '-toggle'"
    :counter="filtersCount"
    class="weeks-filter-component"
  >
    <div v-for="week in weeks" :key="id + '-week-' + week.value" class="option">
      <input
        :id="id + '-week-' + week.value"
        v-model="selectedWeeks"
        type="checkbox"
        :value="week.value"
      />
      <label :for="id + '-week-' + week.value">{{ week.label }}</label>
    </div>
  </Foldable>
</template>

<script>
import Foldable from '@/components/Foldable'

export default {
  name: 'WeeksFilter',
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
    weeks: {
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
      selectedWeeks: this.value
    }
  },
  computed: {
    filtersCount() {
      return this.selectedWeeks.length
    }
  },
  watch: {
    value() {
      this.selectedWeeks = this.value
    },
    selectedWeeks() {
      this.$emit('input', this.selectedWeeks)
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
