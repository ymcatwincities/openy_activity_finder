<template>
  <div id="activity-finder-app">
    <WizardBar
      v-if="wizardSteps.includes(step)"
      :ages="ages"
      :days="days"
      :times="times"
      :days-times="daysTimes"
      :weeks="weeks"
      :locations="locations"
      :activities="activities"
      :selected-ages="selectedAges"
      :selected-days="selectedDays"
      :selected-times="selectedTimes"
      :selected-days-times="selectedDaysTimes"
      :selected-weeks="selectedWeeks"
      :selected-locations="selectedLocations"
      :selected-activities="selectedActivities"
      @startOver="startOver()"
      @viewResults="viewResults()"
    />
    <ResultsBar
      v-if="step === 'results'"
      :class="resultsBarClasses"
      :disable-search-box="disableSearchBox"
    >
      <template v-if="!disableSearchBox" v-slot:search="{ hideModal }">
        <SearchForm v-model="searchKeywords" @input="hideModal" />
      </template>
      <template v-slot:filter="{ hideModal }">
        <Filters
          :data="data"
          :ages="ages"
          :days="days"
          :days-times="daysTimes"
          :weeks="weeks"
          :locations="locations"
          :activities="activities"
          :initial-ages="selectedAges"
          :initial-days="selectedDays"
          :initial-days-times="selectedDaysTimes"
          :initial-weeks="selectedWeeks"
          :initial-locations="selectedLocations"
          :initial-activities="selectedActivities"
          :max-ages="maxAges"
          :legacy-mode="legacyMode"
          :weeks-filter="weeksFilter"
          :filters-section-config="filtersSectionConfig"
          :daxko="daxko"
          :bs-version="bsVersion"
          :exclude-by-category="excludeByCategory"
          :exclude-by-location="excludeByLocation"
          @filterChange="onFilterChange($event, hideModal)"
          @clearFilters="clearFilters(hideModal)"
        />
      </template>
      <template v-slot:sort="{ hideModal }">
        <SortRadios v-model="selectedSort" :sort-options="sortOptions" @input="hideModal" />
      </template>
    </ResultsBar>
    <Loading v-if="step !== 'results' && isLoadingData" />
    <SelectPath
      v-else-if="step === 'selectPath'"
      v-model="selectedPath"
      :label="label"
      :label-display="labelDisplay"
      :paths="paths"
      :background-image="backgroundImage"
      :bs-version="bsVersion"
      @nextStep="nextStep('selectPath')"
    >
      <template v-if="!disableSearchBox" v-slot:search>
        <SearchForm v-model="searchKeywords" />
      </template>
      <template v-if="showHomeBranchBlock" v-slot:home-branch>
        <p>
          <a role="button" class="homebranch-link" @click.stop.prevent="viewHomeBranchResults">
            <strong>{{ 'View all programs for Home Branch' | t }}</strong>
          </a>
        </p>
        <p v-if="homeBranchResultsCount" class="homebranch-results-count">
          {{ homeBranchResultsCount | formatPlural('1 Result', '@count Results') }}
        </p>
      </template>
    </SelectPath>
    <SelectAges
      v-else-if="step === 'selectAges'"
      v-model="selectedAges"
      :ages="ages"
      :max-ages="maxAges"
      :facets="data.facets.static_age_filter ? data.facets.static_age_filter : []"
      :first-step="selectedPath === 'selectAges'"
      @nextStep="nextStep('selectAges')"
    />
    <SelectDays
      v-else-if="step === 'selectDays'"
      v-model="selectedDays"
      :days="days"
      :facets="data.facets.days_of_week"
      :first-step="selectedPath === 'selectDays'"
      @nextStep="nextStep('selectDays')"
    />
    <SelectTimes
      v-else-if="step === 'selectTimes'"
      v-model="selectedTimes"
      :times="times"
      :facets="data.facets.af_parts_of_day"
      :first-step="selectedPath === 'selectTimes'"
      @nextStep="nextStep('selectTimes')"
    />
    <SelectDaysTimes
      v-else-if="step === 'selectDaysTimes'"
      v-model="selectedDaysTimes"
      :days-times="daysTimes"
      :facets="data.facets.af_weekdays_parts_of_day"
      :first-step="selectedPath === 'selectDaysTimes'"
      @nextStep="nextStep('selectDaysTimes')"
    />
    <SelectWeeks
      v-else-if="step === 'selectWeeks'"
      v-model="selectedWeeks"
      :weeks="weeks"
      :facets="data.facets.static_weeks_filter"
      :first-step="selectedPath === 'selectWeeks'"
      @nextStep="nextStep('selectWeeks')"
    />
    <SelectLocations
      v-else-if="step === 'selectLocations'"
      v-model="selectedLocations"
      :locations="locations"
      :facets="data.facets.locations"
      :first-step="selectedPath === 'selectLocations'"
      :home-branch-id="homeBranchId"
      :exclude-by-location="excludeByLocation"
      @nextStep="nextStep('selectLocations')"
    />
    <SelectActivities
      v-else-if="step === 'selectActivities'"
      v-model="selectedActivities"
      :activities="activities"
      :facets="data.facets.field_activity_category"
      :first-step="selectedPath === 'selectActivities'"
      :multiple="!daxko"
      :limit-by-category="limitByCategory"
      :exclude-by-category="excludeByCategory"
      @nextStep="nextStep('selectActivities')"
    />
    <Results
      v-else-if="step === 'results'"
      :data="data"
      :ages="ages"
      :selected-ages="selectedAges"
      :is-loading-data="isLoadingData"
      :cart-items="cartItems"
      :legacy-mode="legacyMode"
      :disable-spots-available="disableSpotsAvailable"
      :request-more-info="daxko"
      :bs-version="bsVersion"
      @startOver="startOver()"
      @addItem="addItem($event)"
      @removeItem="removeItem($event)"
      @removeItems="removeItems"
    >
      <template v-if="!disableSearchBox" v-slot:search>
        <SearchForm v-model="searchKeywords" />
      </template>
      <template v-slot:filters>
        <Filters
          id="desktop-filters"
          :data="data"
          :ages="ages"
          :days="days"
          :days-times="daysTimes"
          :weeks="weeks"
          :locations="locations"
          :activities="activities"
          :initial-ages="selectedAges"
          :initial-days="selectedDays"
          :initial-days-times="selectedDaysTimes"
          :initial-weeks="selectedWeeks"
          :initial-locations="selectedLocations"
          :initial-activities="selectedActivities"
          :max-ages="maxAges"
          :legacy-mode="legacyMode"
          :weeks-filter="weeksFilter"
          :filters-section-config="filtersSectionConfig"
          :daxko="daxko"
          :bs-version="bsVersion"
          :exclude-by-category="excludeByCategory"
          :exclude-by-location="excludeByLocation"
          filters-mode="instant"
          @filterChange="onFilterChange($event)"
          @clearFilters="clearFilters"
        />
      </template>
      <template v-slot:sort>
        <SortSelect v-model="selectedSort" :sort-options="sortOptions" />
      </template>
      <template v-slot:pager>
        <DaxkoPager v-if="daxko" v-model="selectedPage" :daxko-pages="daxkoPages" />
        <Pager v-else v-model="selectedPage" :total-pages="data.pager_info.total_pages" />
      </template>
      <template v-slot:no-results>
        <NoResults
          :selected-ages="selectedAges"
          :selected-days="selectedDays"
          :selected-times="selectedTimes"
          :selected-days-times="selectedDaysTimes"
          :selected-locations="selectedLocations"
          :selected-activities="selectedActivities"
          :search-keywords="searchKeywords"
          @noResultsChoice="noResultsChoice($event)"
          @clearKeywords="clearKeywords($event)"
        />
      </template>
    </Results>
  </div>
</template>

<script>
import client from '@/client/index.js'
import SelectPath from '@/components/steps/SelectPath.vue'
import SelectAges from '@/components/steps/SelectAges.vue'
import SelectDays from '@/components/steps/SelectDays.vue'
import SelectTimes from '@/components/steps/SelectTimes.vue'
import SelectDaysTimes from '@/components/steps/SelectDaysTimes.vue'
import SelectWeeks from '@/components/steps/SelectWeeks.vue'
import SelectLocations from '@/components/steps/SelectLocations.vue'
import SelectActivities from '@/components/steps/SelectActivities.vue'
import Results from '@/components/Results.vue'
import Loading from '@/components/Loading.vue'
import ResultsBar from '@/components/ResultsBar.vue'
import WizardBar from '@/components/WizardBar.vue'
import Pager from '@/components/filters/Pager.vue'
import DaxkoPager from '@/components/filters/DaxkoPager.vue'
import Filters from '@/components/filters/Filters.vue'
import SortRadios from '@/components/filters/SortRadios.vue'
import SortSelect from '@/components/filters/SortSelect.vue'
import SearchForm from '@/components/filters/SearchForm.vue'
import NoResults from '@/components/NoResults.vue'

export default {
  name: 'ActivityFinder',
  components: {
    SelectPath,
    SelectAges,
    SelectDays,
    SelectTimes,
    SelectDaysTimes,
    SelectWeeks,
    SelectLocations,
    SelectActivities,
    Results,
    Loading,
    ResultsBar,
    WizardBar,
    Pager,
    DaxkoPager,
    Filters,
    SortRadios,
    SortSelect,
    SearchForm,
    NoResults
  },
  props: {
    backendService: {
      type: String,
      required: true
    },
    label: {
      type: String,
      required: true
    },
    labelDisplay: {
      type: Boolean,
      required: true
    },
    ages: {
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
    categories: {
      type: Array,
      required: true
    },
    categoriesType: {
      type: String,
      required: true
    },
    activities: {
      type: Array,
      required: true
    },
    locations: {
      type: Array,
      required: true
    },
    sortOptions: {
      type: Object,
      required: true
    },
    defaultSortOption: {
      type: String,
      required: true
    },
    relevanceSortOption: {
      type: String,
      required: true
    },
    limitByCategory: {
      type: Array,
      required: true
    },
    excludeByCategory: {
      type: Array,
      required: true
    },
    legacyMode: {
      type: Boolean,
      required: true
    },
    excludeByLocation: {
      type: Array,
      required: true
    },
    weeksFilter: {
      type: Boolean,
      required: true
    },
    hideHomeBranchBlock: {
      type: Boolean,
      required: true
    },
    disableSearchBox: {
      type: Boolean,
      required: true
    },
    disableSpotsAvailable: {
      type: Boolean,
      required: true
    },
    backgroundImage: {
      type: Object,
      required: true
    },
    filtersSectionConfig: {
      type: Object,
      required: true
    },
    bsVersion: {
      type: Number,
      required: true
    }
  },
  data() {
    let data = {
      // Flag to show if the data request is in progress.
      isLoadingData: false,
      // Controls if we can load data now - we can load only in between the steps or on results step.
      canLoadData: true,
      // Search parameters of the last request casted to string primitive.
      lastRequestParamsString: '',
      // Indicates if we are working with Daxko backend.
      daxko: false,
      daxkoPages: [],
      data: {},
      steps: [
        'selectPath',
        'selectAges',
        'selectActivities',
        'selectDaysTimes',
        'selectLocations',
        'results'
      ],
      completedSteps: [],
      wizardSteps: [
        'selectAges',
        'selectActivities',
        'selectTimes',
        'selectDays',
        'selectDaysTimes',
        'selectWeeks',
        'selectLocations'
      ],
      paths: [
        {
          id: 'selectAges',
          name: this.t('Age'),
          icon: 'fa-birthday-cake'
        },
        {
          id: 'selectDaysTimes',
          name: this.t('Day & Time'),
          icon: 'fa-calendar'
        },
        {
          id: 'selectLocations',
          name: this.t('Location'),
          icon: 'fa-map-marker'
        },
        {
          id: 'selectActivities',
          name: this.t('Activity'),
          icon: 'fa-heartbeat'
        }
      ],
      selectedPath: '',
      maxAges: 2,
      defaults: {
        step: 'selectPath',
        selectedAges: [],
        selectedDays: [],
        selectedTimes: [],
        selectedDaysTimes: [],
        selectedWeeks: [],
        selectedLocations: [],
        selectedActivities: [],
        selectedPage: 1,
        selectedSort: this.defaultSortOption,
        searchKeywords: ''
      },
      cartItems: [],
      cartItemsKey: 'activity_finder.cartItems',
      cartItemsTimeout: 5 * 24 * 3600 * 1000,
      clearFiltersSkip: ['step', 'selectedSort', 'searchKeywords'],
      // The Home Branch ID from cookie.
      homeBranchId: this.getHomeBranchId(),
      // Results count for the Home Branch.
      homeBranchResultsCount: null
    }

    // Identify daxko backend.
    if (this.backendService === 'openy_daxko2.openy_activity_finder_backend') {
      data.daxko = true
    }

    // Legacy mode tweaks.
    if (this.legacyMode) {
      data.steps = [
        'selectPath',
        'selectAges',
        'selectActivities',
        'selectDays',
        'selectLocations',
        'results'
      ]
      data.paths[1] = {
        id: 'selectDays',
        name: this.t('Day'),
        icon: 'fa-calendar'
      }
      data.maxAges = 0
    }

    // Replace days/times by Weeks selection step.
    if (this.weeksFilter) {
      data.steps = [
        'selectPath',
        'selectAges',
        'selectActivities',
        'selectWeeks',
        'selectLocations',
        'results'
      ]
      data.paths[1] = {
        id: 'selectWeeks',
        name: this.t('Week'),
        icon: 'fa-calendar'
      }
    }

    // Initialize reactive properties for every default value.
    for (let key in data.defaults) {
      data[key] = Array.isArray(data.defaults[key])
        ? data.defaults[key].slice()
        : data.defaults[key]
    }

    return data
  },
  computed: {
    // Search parameters for data request.
    searchParams() {
      // We shouldn't filter by the selected values on the corresponding step
      // to not limit the available options.
      let params = {
        ages: this.step === 'selectAges' ? '' : this.selectedAges.join(','),
        days: this.step === 'selectDays' ? '' : this.selectedDays.join(','),
        times: this.step === 'selectTimes' ? '' : this.selectedTimes.join(','),
        daystimes: this.step === 'selectDaysTimes' ? '' : this.selectedDaysTimes.join(','),
        weeks: this.step === 'selectWeeks' ? '' : this.selectedWeeks.join(','),
        locations: this.step === 'selectLocations' ? '' : this.selectedLocations.join(','),
        categories: this.step === 'selectActivities' ? '' : this.selectedActivities.join(','),
        page: this.selectedPage,
        sort: this.selectedSort,
        keywords: this.searchKeywords,
        exclude: this.excludeByCategory.join(','),
        excludeloc: this.excludeByLocation.join(',')
      }

      if (this.daxko && this.selectedPage > 1 && this.daxkoPages[this.selectedPage]) {
        params.next = encodeURIComponent(this.daxkoPages[this.selectedPage])
      }

      return params
    },
    // Search parameters casted to string primitive.
    searchParamsString() {
      return Object.values(this.searchParams).join('_')
    },
    // Reset page parameters casted to string primitive.
    resetPageString() {
      return [
        ...this.selectedAges,
        ...this.selectedDays,
        ...this.selectedTimes,
        ...this.selectedDaysTimes,
        ...this.selectedWeeks,
        ...this.selectedLocations,
        ...this.selectedActivities,
        this.selectedSort,
        this.searchKeywords
      ].join('_')
    },
    // Parameters to update the url casted to string primitive.
    updateUrlString() {
      return [
        this.step,
        ...this.selectedAges,
        ...this.selectedDays,
        ...this.selectedTimes,
        ...this.selectedDaysTimes,
        ...this.selectedWeeks,
        ...this.selectedLocations,
        ...this.selectedActivities,
        this.selectedPage,
        this.selectedSort,
        this.searchKeywords
      ].join('_')
    },
    // Controls if the search parameters were changed since last data request.
    shouldUpdateData() {
      return this.lastRequestParamsString !== this.searchParamsString
    },
    showHomeBranchBlock() {
      return !this.hideHomeBranchBlock && this.homeBranchId
    },
    resultsBarClasses() {
      return this.bsVersion === 4 ? 'd-lg-none' : 'hidden-md hidden-lg'
    }
  },
  watch: {
    searchParamsString() {
      // If data is empty then the page is just loading and we shouldn't react to changes yet.
      if (Object.keys(this.data).length === 0) {
        return
      }

      if (this.step === 'results') {
        this.canLoadData = true
      }
    },
    resetPageString() {
      // If data is empty then the page is just loading and we shouldn't react to changes yet.
      if (Object.keys(this.data).length === 0) {
        return
      }

      this.selectedPage = this.defaults.selectedPage
      this.daxkoPages = []
    },
    updateUrlString() {
      this.updateUrl()
    },
    step(val, oldVal) {
      // Check if we returned to previously completed step - using browser back button - then we
      // need to remove this step from completed steps.
      // The else part tracks the completed step if the first (default) step was already completed
      // or if we have just completed this default step.
      if (this.completedSteps.includes(val)) {
        this.completedSteps = this.completedSteps.filter(x => x !== val)
      } else if (
        this.completedSteps.includes(this.defaults.step) ||
        oldVal === this.defaults.step
      ) {
        this.completedSteps.push(oldVal)
      }

      this.canLoadData = true

      // Scroll to view.
      document.getElementById('activity-finder-app').scrollIntoView(true)
    },
    selectedPage() {
      // Scroll to top.
      window.scrollTo({ top: 0, behavior: 'smooth' })
    },
    canLoadData() {
      this.loadData()
    },
    shouldUpdateData() {
      this.loadData()
    },
    $route: {
      handler() {
        this.getDataFromUrl()
      },
      immediate: true
    },
    cartItems() {
      localStorage.setItem(this.cartItemsKey, JSON.stringify(this.cartItems))
    },
    searchKeywords(val) {
      if (val) {
        this.step = 'results'
      }
      this.selectedSort = val ? this.relevanceSortOption : this.defaultSortOption
    }
  },
  created() {
    this.loadData()
    this.getHomeBranchResultsCount()
  },
  mounted() {
    if (localStorage.getItem(this.cartItemsKey)) {
      try {
        this.cartItems = JSON.parse(localStorage.getItem(this.cartItemsKey)).filter(
          item => item.timestamp > new Date().getTime() - this.cartItemsTimeout
        )

        // Update cart items info.
        const itemIds = this.cartItems.map(item => item.item.nid)
        if (itemIds.length) {
          client('session_data')
            .request({ params: { _format: 'json', session_ids: itemIds.join(',') } })
            .then(response => {
              this.cartItems = this.cartItems
                .map(cartItem => {
                  cartItem.item = response.data.sessions.find(
                    responseItem => responseItem.nid === cartItem.item.nid
                  )
                  return cartItem
                })
                .filter(cartItem => cartItem.item)
            })
            .catch(error => {
              error
            })
        }
      } catch (e) {
        localStorage.removeItem(this.cartItemsKey)
      }
    }
  },
  methods: {
    nextStep(step) {
      // First step should be the one chosen by the user - selectedPath.
      if (!this.completedSteps.includes(this.selectedPath) && this.step !== this.selectedPath) {
        this.step = this.selectedPath
      } else {
        // selectTimes and selectDays steps should go one after another.
        if (step === 'selectTimes') {
          this.step = 'selectDays'
        } else {
          // Find the next step - exclude completed steps and current step from the steps list and get the first one.
          const difference = this.steps.filter(
            x => !this.completedSteps.includes(x) && x !== this.step
          )
          this.step = difference[0]
        }
      }
    },
    viewResults() {
      this.step = 'results'
    },
    viewHomeBranchResults() {
      this.selectedLocations = [this.homeBranchId]
      this.step = 'results'
    },
    async loadData() {
      // Wait for the next tick so that all watchers are run.
      await this.$nextTick()
      if (!this.canLoadData) {
        return
      }
      if (!this.shouldUpdateData) {
        this.canLoadData = false
        return
      }
      if (this.isLoadingData) {
        return
      }

      this.canLoadData = false
      this.lastRequestParamsString = this.searchParamsString

      this.isLoadingData = true
      client()
        .request({ params: this.searchParams })
        .then(response => {
          this.data = response.data
          if (this.daxko) {
            this.daxkoPages[this.selectedPage + 1] = this.data.pager
          }
          this.isLoadingData = false
          // If there were other changes while the request was in progress - we should load data again.
          this.loadData()
        })
        .catch(error => {
          error
        })
    },
    getDataFromUrl() {
      const query = this.$route.query
      for (let key in this.defaults) {
        if (query.hasOwnProperty(key) && query[key] !== '') {
          if (Array.isArray(this.defaults[key])) {
            this[key] = query[key].split(',')
          } else {
            if (key === 'selectedPage') {
              if (this.daxko && this.daxkoPages.length === 0) {
                this[key] = 1
              } else {
                // selectedPage should be number, not a string.
                this[key] = parseInt(query[key])
              }
            } else {
              this[key] = query[key]
            }

            // If completedSteps is empty then we should initialize it.
            if (key === 'step' && this.completedSteps.length === 0) {
              this.completedSteps.push(this.defaults.step)
              this.selectedPath = query['step']
            }
          }
        } else {
          this[key] = Array.isArray(this.defaults[key])
            ? this.defaults[key].slice()
            : this.defaults[key]
        }
      }
    },
    updateUrl() {
      // Prepare query with non-default values only.
      let query = {}
      for (let key in this.defaults) {
        if (Array.isArray(this.defaults[key])) {
          if (this[key].length !== 0) {
            query[key] = this[key].join(',')
          }
        } else {
          if (this[key] !== this.defaults[key]) {
            query[key] = this[key]
          }
        }
      }

      this.$router
        .push({
          query
        })
        // TODO: is there any good way to detect if we are already at this router location? - MPR-164
        // Catch to avoid "NavigationDuplicated" error.
        .catch(err => {
          err
        })
    },
    onFilterChange(event, callback = () => {}) {
      callback()
      this[event.filter] = event.value
    },
    clearFilters(callback = () => {}) {
      callback()
      for (let key in this.defaults) {
        if (this.clearFiltersSkip.includes(key)) {
          continue
        }
        this[key] = Array.isArray(this.defaults[key])
          ? this.defaults[key].slice()
          : this.defaults[key]
      }
    },
    startOver() {
      for (let key in this.defaults) {
        this[key] = Array.isArray(this.defaults[key])
          ? this.defaults[key].slice()
          : this.defaults[key]
      }

      this.completedSteps = []
      this.selectedPath = ''
      this.cartItems = []
    },
    addItem(item) {
      item.timestamp = new Date().getTime()
      this.cartItems.push(item)
    },
    removeItem(index) {
      this.cartItems.splice(index, 1)
    },
    removeItems() {
      this.cartItems = []
    },
    noResultsChoice(choice) {
      const skip = [...this.clearFiltersSkip, choice]
      for (let key in this.defaults) {
        if (skip.includes(key)) {
          continue
        }
        this[key] = Array.isArray(this.defaults[key])
          ? this.defaults[key].slice()
          : this.defaults[key]
      }
    },
    clearKeywords() {
      this.searchKeywords = ''
    },
    getHomeBranchId() {
      const cookie = this.getCookie('home_branch')
      if (cookie !== '') {
        try {
          return JSON.parse(cookie).id
        } catch (e) {
          return null
        }
      }
      return null
    },
    getHomeBranchResultsCount() {
      if (this.hideHomeBranchBlock) {
        return
      }
      if (!this.homeBranchId) {
        return
      }
      if (this.homeBranchResultsCount) {
        return
      }

      client()
        .request({
          params: {
            locations: this.homeBranchId,
            exclude: this.searchParams.exclude
          }
        })
        .then(response => {
          this.homeBranchResultsCount = response.data.count
        })
        .catch(error => {
          error
        })
    }
  }
}
</script>

<style lang="scss">
// Overrides for external CSS rules.
.landing-content {
  padding-top: 0 !important;
}

.activity-finder__wrapper {
  position: relative;
}

// Fix Safari grid issue.
// @see https://stackoverflow.com/questions/49403391/bootstrap-4-safari-on-mac-grid-issue
.row:before,
.row:after {
  height: 0;
  width: 0;
}
</style>
