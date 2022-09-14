<template>
  <div class="sort-select-component">
    <select v-model="selectedSort" @change="onChange">
      <option v-for="(option, key) in sortOptions" :key="key" :value="key">
        {{ option }}
      </option>
    </select>
    <font-awesome-icon icon="chevron-down" />
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
      type: Object,
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
  position: relative;
  display: flex;
  align-items: center;

  select {
    height: 40px;
    border: 1px solid $af-dark-gray;
    border-radius: 5px;
    color: $af-black;
    min-width: 260px;
    width: auto;
    padding: 0 10px;
    appearance: none;
    z-index: 1;
    background: transparent;
    outline: none;
    font-size: 12px;
    line-height: 18px;

    &::-ms-expand {
      display: none;
    }

    option {
      padding: 10px;
    }
  }

  .fa,
  .svg-inline--fa {
    position: absolute;
    right: 0;
    font-size: 12px;
    margin: 0 14px;
  }
}
</style>
