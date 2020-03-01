<template>
  <Modal id="activity-finder-filters" v-model="visible" title="Filters" narrow>
    <template>
      <div class="filters-modal-content">
        <div class="row">
          <div class="col-xs-12">
            <div class="content">
              <p>{{ 'The following filters have been applied' | t }}:</p>
              <ul>
                <li v-for="(item, index) in filterList" :key="index">
                  {{ item }}
                </li>
              </ul>
              <div class="text-center">
                <button type="button" class="btn btn-lg view-results" @click="viewResults()">
                  {{ 'View results' | t }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </template>
  </Modal>
</template>

<script>
import Modal from '@/components/modals/Modal.vue'
import {
  activitiesList,
  agesList,
  locationsList,
  daysList,
  timesList,
  daysTimesList
} from '@/helpers/filters'

export default {
  name: 'FiltersModal',
  components: {
    Modal
  },
  props: {
    value: {
      type: Boolean,
      default: false
    },
    ages: {
      type: Array,
      required: true
    },
    selectedAges: {
      type: Array,
      required: true
    },
    days: {
      type: Array,
      required: true
    },
    selectedDays: {
      type: Array,
      required: true
    },
    times: {
      type: Array,
      required: true
    },
    selectedTimes: {
      type: Array,
      required: true
    },
    daysTimes: {
      type: Array,
      required: true
    },
    selectedDaysTimes: {
      type: Array,
      required: true
    },
    activities: {
      type: Array,
      required: true
    },
    selectedActivities: {
      type: Array,
      required: true
    },
    locations: {
      type: Array,
      required: true
    },
    selectedLocations: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      visible: this.value
    }
  },
  computed: {
    filterList() {
      return [
        ...activitiesList(this.selectedActivities, this.activities),
        ...agesList(this.selectedAges, this.ages),
        ...locationsList(this.selectedLocations, this.locations),
        ...daysList(this.selectedDays, this.days),
        ...timesList(this.selectedTimes, this.times),
        ...daysTimesList(this.selectedDaysTimes, this.daysTimes)
      ]
    }
  },
  watch: {
    value() {
      this.visible = this.value
    },
    visible() {
      this.$emit('input', this.visible)
    }
  },
  methods: {
    viewResults() {
      this.visible = false
      this.$emit('viewResults')
    }
  }
}
</script>

<style lang="scss">
.filters-modal-content {
  .content {
    padding: 20px 10px;

    .view-results {
      border-radius: 5px;
      background-color: $af-violet;
    }
  }
}
</style>
