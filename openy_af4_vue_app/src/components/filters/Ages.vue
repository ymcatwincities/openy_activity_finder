<template>
  <Foldable
    label="Age(s)"
    :collapse-id="collapseId"
    :counter="filtersCount"
    :counter-max="maxAges"
    class="ages-filter-component"
  >
    <div class="row">
      <div v-for="age in ages" :key="age.value" class="age-option col-xs-4">
        <input
          :id="'age' + age.value"
          v-model="selectedAges"
          type="checkbox"
          :value="age.value"
          :disabled="isDisabled(age.value)"
          @change="onChange"
        />
        <label :id="'label-' + age.value" :for="'age' + age.value">{{ age.label }}</label>
        <b-tooltip v-if="isDisabled(age.value)" :target="'label-' + age.value">
          {{ 'Please unselect any of the selected options first' | t }}
        </b-tooltip>
      </div>
    </div>
  </Foldable>
</template>

<script>
import Foldable from '@/components/Foldable'

export default {
  name: 'AgesFilter',
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
    ages: {
      type: Array,
      required: true
    },
    facets: {
      type: Array,
      required: true
    },
    maxAges: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      selectedAges: this.value
    }
  },
  computed: {
    filtersCount() {
      return this.selectedAges.length
    }
  },
  watch: {
    value() {
      this.selectedAges = this.value
    }
  },
  methods: {
    onChange() {
      this.$emit('input', this.selectedAges)
    },
    facetCount(value) {
      let facet = this.facets.find(x => x.value === value)
      return facet ? facet.count : 0
    },
    isDisabled(value) {
      return !!(this.maxAges && this.value.length >= this.maxAges && !this.value.includes(value))
    }
  }
}
</script>

<style lang="scss">
.ages-filter-component {
  .foldable-content {
    padding: 10px 10px 5px;

    .row {
      margin-left: -3px !important;
      margin-right: -3px !important;
    }

    .age-option {
      padding-left: 3px;
      padding-right: 3px;

      input[type='checkbox'] {
        display: none;

        + label {
          cursor: pointer;
          display: block;
          font-family: 'Cachet', Verdana, Geneva, sans-serif;
          height: 50px;
          line-height: 50px;
          text-align: center;
          color: $af-blue;
          background: $white;
          border: 2px solid $af-blue;
          border-radius: 5px;
        }

        &:checked + label {
          color: white;
          background: $af-blue;
        }

        &:disabled + label {
          background-color: $af-light-gray;
          border-color: $af-dark-gray;
          cursor: default;
          color: $af-dark-gray;
        }
      }
    }
  }
}
</style>
