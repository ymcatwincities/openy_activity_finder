<template>
  <div class="wizard-bar-component">
    <div class="container">
      <div class="row">
        <div class="col-12 col-xs-12">
          <div class="controls">
            <span class="filters">
              <a :class="{ 'no-filters': !filtersCount }" role="button" @click="showFiltersModal()">
                <font-awesome-icon icon="filter" />
                {{ filtersCount | formatPlural('1 Filter', '@count Filters') }}
              </a>
            </span>
            <span class="start-over">
              <a class="start-over" role="button" @click="startOver()">{{ 'Start Over' | t }}</a>
            </span>
          </div>
        </div>
      </div>
      <FiltersModal
        v-model="filtersModal.visible"
        :ages="ages"
        :selected-ages="selectedAges"
        :days="days"
        :selected-days="selectedDays"
        :times="times"
        :selected-times="selectedTimes"
        :days-times="daysTimes"
        :selected-days-times="selectedDaysTimes"
        :weeks="weeks"
        :selected-weeks="selectedWeeks"
        :locations="locations"
        :selected-locations="selectedLocations"
        :activities="activities"
        :selected-activities="selectedActivities"
        @viewResults="viewResults"
      />
    </div>
  </div>
</template>

<script>
import FiltersModal from '@/components/modals/Filters.vue'

export default {
  name: 'WizardBar',
  components: {
    FiltersModal
  },
  props: {
    ages: {
      type: Array,
      required: true
    },
    activities: {
      type: Array,
      required: true
    },
    days: {
      type: Array,
      required: true
    },
    times: {
      type: Array,
      required: true
    },
    daysTimes: {
      type: Array,
      required: true
    },
    weeks: {
      type: Array,
      required: true
    },
    locations: {
      type: Array,
      required: true
    },
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
    selectedWeeks: {
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
  data() {
    return {
      filtersModal: {
        visible: false
      }
    }
  },
  computed: {
    filtersCount() {
      return (
        this.selectedAges.length +
        this.selectedDays.length +
        this.selectedTimes.length +
        this.selectedDaysTimes.length +
        this.selectedWeeks.length +
        this.selectedLocations.length +
        this.selectedActivities.length
      )
    }
  },
  methods: {
    startOver() {
      this.$emit('startOver')
    },
    viewResults() {
      this.$emit('viewResults')
    },
    showFiltersModal() {
      this.filtersModal.visible = true
    }
  }
}
</script>

<style lang="scss">
.wizard-bar-component {
  background-color: $af-light-gray;

  .controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;

    .filters {
      font-size: 12px;
      line-height: 18px;
      .fa-filter {
        margin-right: 8px;
      }
      .no-filters {
        color: $af-dark-gray;
      }
    }

    .start-over {
      font-size: 12px;
      line-height: 18px;
    }
  }
}
</style>
