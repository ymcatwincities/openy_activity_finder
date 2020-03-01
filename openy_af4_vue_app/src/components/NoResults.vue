<template>
  <div class="no-results-component">
    <div class="row">
      <div class="col-sx-12 col-sm-6 col-sm-offset-3 col-lg-4 col-lg-offset-4 text-center">
        <h2>{{ 'Oh no!' | t }}</h2>
        <p>{{ "We're sorry, but no results meet your search criteria." | t }}</p>
        <p>
          <strong>{{ 'What criteria is most important to you?' | t }}</strong>
        </p>
        <div class="actions">
          <button
            v-if="selectedAges.length"
            type="button"
            class="btn btn-lg"
            @click="handleChoice('selectedAges')"
          >
            {{ 'Age' | t }}
          </button>
          <button
            v-if="selectedDaysTimes.length"
            type="button"
            class="btn btn-lg"
            @click="handleChoice('selectedDaysTimes')"
          >
            {{ 'Day & time' | t }}
          </button>
          <button
            v-if="selectedLocations.length"
            type="button"
            class="btn btn-lg"
            @click="handleChoice('selectedLocations')"
          >
            {{ 'Location' | t }}
          </button>
          <button
            v-if="selectedActivities.length"
            type="button"
            class="btn btn-lg"
            @click="handleChoice('selectedActivities')"
          >
            {{ 'Activity' | t }}
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
  methods: {
    handleChoice(choice) {
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
