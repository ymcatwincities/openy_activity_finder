<template>
  <div class="sort-select-component">
    <select v-model="selectedSort" @change="onChange">
      <option v-for="option in sortOptions" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'SortSelect',
  props: {
    value: {
      type: String,
      required: true
    },
    sortOptions: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedSort: this.value
    }
  },
  watch: {
    value() {
      this.selectedSort = this.value
    }
  },
  methods: {
    onChange() {
      this.$emit('input', this.selectedSort)
    }
  }
}
</script>

<style lang="scss">
.sort-select-component {
  select {
    height: 40px;
  }
}
</style>
