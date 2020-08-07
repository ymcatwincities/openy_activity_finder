<template>
  <div>
    <a v-if="visible == 1" class="pdf-component" href="#" @click.stop.prevent="downloadPdf">
      Download Weekly PDF <span class="fa fa-file" />
    </a>
  </div>
</template>

<script>
export default {
  name: 'Pdf',
  props: {
    data: {
      type: Object,
      required: true
    },
    activities: {
      type: Array,
      required: true
    },
    initialActivities: {
      type: Array,
      required: true
    },
    initialLocations: {
      type: Array,
      required: true
    },
    locations: {
      type: Array,
      required: true
    },
    visible: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      selectedActivities: this.initialActivities,
      selectedLocations: this.initialLocations
    }
  },
  watch: {
    initialLocations() {
      this.selectedLocations = this.initialLocations
    },
    selectedLocations() {
      this.filterChange({ filter: 'selectedLocations', value: this.selectedLocations })
    },
    initialActivities() {
      this.selectedActivities = this.initialActivities
    },
    selectedActivities() {
      this.filterChange({ filter: 'selectedActivities', value: this.selectedActivities })
    }
  },
  methods: {
    downloadPdf() {
      let pdfActivities = []
      for (const program of this.activities) {
        for (const subprogram of program.value) {
          for (const selctedSubprogram of this.selectedActivities) {
            if (selctedSubprogram == subprogram.value) {
              pdfActivities.push(program.label + ' ' + subprogram.label)
            }
          }
        }
      }

      let locationsLabels = []
      for (const type of this.locations) {
        if (type.length == 0) {
          continue
        }
        for (const location of type.value) {
          for (const selectedLocation of this.selectedLocations) {
            if (selectedLocation == location.value) {
              locationsLabels.push(location.label)
            }
          }
        }
      }
      if (pdfActivities.length == 0) {
        for (const program of this.activities) {
          for (const subprogram of program.value) {
            pdfActivities.push(program.label + ' ' + subprogram.label)
          }
        }
      }
      const locationsLabelsString = locationsLabels.join(',')
      const pdfActivitiesString = pdfActivities.join(',')
      let routeData = this.$router.resolve({
        path: '/schedules/get-pdf',
        query: {
          locations: locationsLabelsString,
          categories: pdfActivitiesString,
          limit: pdfActivitiesString,
          ['hide-programs']: 1,
          ['hide-instructor']: 1
        }
      })
      window.open(routeData.href, '_blank')
    }
  }
}
</script>
