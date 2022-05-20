<template>
  <div class="results-component">
    <div class="container">
      <div class="row">
        <div
          v-if="!data.facets"
          class="col-12 col-xs-12 col-sm-3 d-none d-md-block hidden-xs hidden-sm"
        >
          <Loading />
        </div>
        <div v-else :class="filtersClasses">
          <div class="search">
            <slot name="search" />
          </div>
          <slot name="filters" />
        </div>

        <div v-if="isLoadingData" class="col-12 col-xs-12 col-md-9">
          <Loading />
        </div>
        <div v-else :class="resultsClasses">
          <div class="header d-md-none hidden-md hidden-lg">
            <span class="results">
              {{ data.count | formatPlural('1 Result', '@count Results') }}
            </span>
            <a class="start-over" role="button" @click="startOver()">{{ 'Start Over' | t }}</a>
          </div>
          <div class="header d-none d-md-flex hidden-xs hidden-sm">
            <span>
              <span class="results">
                {{ data.count | formatPlural('1 Result', '@count Results') }}
              </span>
              | <a class="start-over" role="button" @click="startOver()">{{ 'Start Over' | t }}</a>
            </span>
            <span>
              <slot name="sort" />
            </span>
          </div>

          <ResultsList
            :results="data.table"
            :ages="ages"
            :selected-ages="selectedAges"
            :legacy-mode="legacyMode"
            :disable-spots-available="disableSpotsAvailable"
            @showActivityDetailsModal="showActivityDetailsModal($event)"
          />
          <slot v-if="!data.table.length" name="no-results" />

          <ActivityDetailsModal
            v-model="activityDetailsModal.visible"
            :item="activityDetailsModal.item"
            :cart-items="cartItems"
            :ages="ages"
            :selected-ages="selectedAges"
            :legacy-mode="legacyMode"
            :disable-spots-available="disableSpotsAvailable"
            :request-more-info="requestMoreInfo"
            @bookmark="handleActivityDetailsBookmark($event)"
            @unbookmark="handleActivityDetailsUnbookmark($event)"
          />
          <BookmarkedItemsModal
            v-if="!legacyMode"
            v-model="bookmarkedItemsModal.visible"
            :cart-items="cartItems"
            :ages="ages"
            :disable-spots-available="disableSpotsAvailable"
            @removeItem="removeItem($event)"
            @removeItems="removeItems"
          />

          <slot v-if="data.table.length" name="pager" />
        </div>
      </div>
    </div>

    <BookmarkIcon v-if="!legacyMode" :counter="cartItems.length" @click="onBookmarkIconClick()" />
    <BookmarkFeatureModal v-if="!legacyMode" />
  </div>
</template>

<script>
import Loading from '@/components/Loading.vue'
import ActivityDetailsModal from '@/components/modals/ActivityDetails.vue'
import BookmarkedItemsModal from '@/components/modals/BookmarkedItems.vue'
import BookmarkFeatureModal from '@/components/modals/BookmarkFeature.vue'
import BookmarkIcon from '@/components/BookmarkIcon'
import ResultsList from '@/components/ResultsList.vue'

export default {
  name: 'Results',
  components: {
    Loading,
    ActivityDetailsModal,
    BookmarkedItemsModal,
    BookmarkFeatureModal,
    BookmarkIcon,
    ResultsList
  },
  props: {
    data: {
      type: Object,
      required: true
    },
    ages: {
      type: Array,
      required: true
    },
    selectedAges: {
      type: Array,
      required: true
    },
    isLoadingData: {
      type: Boolean,
      required: true
    },
    cartItems: {
      type: Array,
      required: true
    },
    legacyMode: {
      type: Boolean,
      required: true
    },
    disableSpotsAvailable: {
      type: Boolean,
      required: true
    },
    requestMoreInfo: {
      type: Boolean,
      default: false
    },
    bsVersion: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      activityDetailsModal: {
        item: {},
        visible: false
      },
      bookmarkedItemsModal: {
        visible: false
      }
    }
  },
  computed: {
    filtersClasses() {
      return this.bsVersion === 4 ? 'col-3 d-none d-lg-block' : 'col-md-3 hidden-xs hidden-sm'
    },
    resultsClasses() {
      return this.bsVersion === 4 ? 'col-12 col-lg-9' : ' col-xs-12 col-md-9'
    }
  },
  methods: {
    startOver() {
      this.$emit('startOver')
    },
    showActivityDetailsModal(item) {
      this.activityDetailsModal.item = item
      this.activityDetailsModal.visible = true
    },
    handleActivityDetailsBookmark(age) {
      if (age) {
        this.$emit('addItem', {
          item: this.activityDetailsModal.item,
          age: age
        })
      } else {
        this.$emit('addItem', {
          item: this.activityDetailsModal.item,
          age: null
        })
      }
    },
    handleActivityDetailsUnbookmark(age) {
      this.cartItems.forEach((cartItem, index) => {
        if (
          cartItem.age === age &&
          cartItem.item.product_id === this.activityDetailsModal.item.product_id &&
          cartItem.item.nid === this.activityDetailsModal.item.nid
        ) {
          this.removeItem(index)
        }
      })
    },
    onBookmarkIconClick() {
      this.bookmarkedItemsModal.visible = true
    },
    removeItem(index) {
      this.$emit('removeItem', index)
    },
    removeItems() {
      this.$emit('removeItems')
    }
  }
}
</script>

<style lang="scss">
.results-component {
  .search {
    margin: 40px 0;
  }

  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    line-height: 18px;
    margin-top: 20px;
    margin-bottom: 20px;

    @include media-breakpoint-up('lg') {
      font-size: 14px;
      line-height: 21px;
      margin-top: 40px;
      margin-bottom: 40px;
    }

    .results {
      color: $af-black;
      font-weight: bold;
    }

    .start-over {
      color: $af-blue;
    }
  }
}
</style>
