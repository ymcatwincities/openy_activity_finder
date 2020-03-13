<template>
  <div class="sort-radios-component">
    <div v-for="(option, key) in sortOptions" :key="key" class="option">
      <input :id="key" v-model="selectedSort" type="radio" :value="key" @change="onChange" />
      <label :for="key">{{ option }}</label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SortRadios',
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
.sort-radios-component {
  .option {
    display: flex;
    height: 50px;

    input[type='radio'] {
      display: none;

      + label {
        cursor: pointer;
        display: flex;
        position: relative;
        margin: auto 0;
        padding-left: 50px;
        color: $af-black;
        font-weight: normal;
        font-size: 12px;
        line-height: 18px;

        &:before {
          content: '';
          display: block;
          position: absolute;
          left: 0;
          height: 20px;
          width: 20px;
          margin: auto 15px;
          border: 2px solid $af-dark-gray;
          border-radius: 50%;
          align-self: center;
        }
      }

      &:checked + label {
        &:after {
          content: '';
          display: block;
          position: absolute;
          top: 4px;
          left: 20px;
          width: 10px;
          height: 10px;
          background-color: $af-blue;
          border-radius: 50%;
        }
      }
    }
  }
}
</style>
