<template>
  <div class="no-results-component">
    <div class="row">
      <div
        class="col-12 col-xs-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4 m-auto text-center"
      >
        <h2>{{ 'Oh no!' | t }}</h2>
        <p>{{ "We're sorry, but no results meet your search criteria." | t }}</p>
        <p v-if="selectedDimensions >= 2">
          <strong>{{ 'What criteria is most important to you?' | t }}</strong>
        </p>
        <div v-if="selectedDimensions >= 2" class="actions">
          <button
            v-if="selectedAges.length"
            type="button"
            class="btn btn-lg"
            @click="onChoice('selectedAges')"
          >
            {{ 'Age' | t }}
          </button>
          <button
            v-if="selectedDays.length"
            type="button"
            class="btn btn-lg"
            @click="onChoice('selectedDays')"
          >
            {{ 'Day' | t }}
          </button>
          <button
            v-if="selectedDaysTimes.length"
            type="button"
            class="btn btn-lg"
            @click="onChoice('selectedDaysTimes')"
          >
            {{ 'Day & time' | t }}
          </button>
          <button
            v-if="selectedLocations.length"
            type="button"
            class="btn btn-lg"
            @click="onChoice('selectedLocations')"
          >
            {{ 'Location' | t }}
          </button>
          <button
            v-if="selectedActivities.length"
            type="button"
            class="btn btn-lg"
            @click="onChoice('selectedActivities')"
          >
            {{ 'Activity' | t }}
          </button>
        </div>
        <div v-else class="actions">
          <button type="button" class="btn btn-lg" @click="onChoice()">
            {{ 'Clear Filters' | t }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'NoResults',
  props: {
    selectedAges: {
      type: Array,
      required: true
    },
    selectedDays: {
      type: Array,
      required: true
    },
    selectedTimes: {
      type: Array,
      required: true
    },
    selectedDaysTimes: {
      type: Array,
      required: true
    },
    selectedLocations: {
      type: Array,
      required: true
    },
    selectedActivities: {
      type: Array,
      required: true
    }
  },
  computed: {
    selectedDimensions() {
      return (
        (this.selectedAges.length ? 1 : 0) +
        (this.selectedDaysTimes.length ? 1 : 0) +
        (this.selectedTimes.length ? 1 : 0) +
        (this.selectedLocations.length ? 1 : 0) +
        (this.selectedActivities.length ? 1 : 0)
      )
    }
  },
  methods: {
    onChoice(choice) {
      this.$emit('noResultsChoice', choice)
    }
  }
}
</script>

<style lang="scss">
.no-results-component {
  .actions {
    display: flex;
    flex-direction: column;
    margin: 20px 0;

    .btn {
      font-weight: bolder;
      line-height: 50px;
      padding: 0 10px;
      border-radius: 5px;
      background-color: $af-violet;
      margin-bottom: 20px;

      &:last-child {
        margin-bottom: 0;
      }
    }
  }
}
</style>
