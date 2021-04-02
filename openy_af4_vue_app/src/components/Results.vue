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

          <div
            v-for="item in data.table"
            :key="item.nid ? item.nid : item.offering_id"
            class="result"
            role="button"
            @click="showActivityDetailsModal(item)"
          >
            <div class="d-sm-none hidden-sm hidden-md hidden-lg">
              <div class="result-header">
                <span class="title">
                  {{ item.name }}
                </span>
              </div>

              <div class="ages-spots">
                <span v-if="item.ages || (selectedAges.length && !legacyMode)" class="ages">
                  <span class="age-label">{{ 'Ages' | t }}:</span>
                  <span v-if="!selectedAges.length || legacyMode" class="info">
                    {{ item.ages }}
                  </span>
                  <template v-for="age in selectedAges" v-else>
                    <template
                      v-if="
                        (!item.min_age || parseInt(item.min_age) <= age) &&
                          (!item.max_age || parseInt(item.max_age) >= age)
                      "
                    >
                      <AgeIcon :key="age" :age="parseInt(age)" :ages="ages" big />
                    </template>
                  </template>
                </span>
                <AvailableSpots
                  v-if="!disableSpotsAvailable && item.spots_available !== ''"
                  :spots="Number(item.spots_available)"
                  :wait-list="Number(item.wait_list_availability)"
                />
              </div>

              <div v-if="item.dates" class="item-detail dates">
                <i class="fa fa-calendar"></i>
                <span>
                  <span class="info">{{ item.dates }}</span>
                  <br />
                  <span v-if="item.days" class="details">{{ item.days }}</span>
                </span>
              </div>

              <div class="item-detail schedule">
                <i class="fa fa-clock fa-clock-o"></i>
                <span class="schedule-items">
                  <span
                    v-for="(schedule, index) in item.schedule"
                    :key="index"
                    class="schedule-item"
                  >
                    <span class="info">{{ schedule.time }}</span>
                    <br />
                    <span class="details">{{ schedule.days }}</span>
                  </span>
                </span>
              </div>

              <div v-if="item.location" class="item-detail">
                <i class="fa fa-map-marker"></i>
                <span>
                  <span class="info">{{ item.location }}</span>
                  <br />
                  <span v-if="item.roomName" class="details">{{ item.roomName }}</span>
                </span>
              </div>

              <div v-if="item.instructor" class="item-detail instructor">
                <i class="fa fa-user"></i>
                <span>
                  <span class="info">{{ item.instructor }}</span>
                  <br />
                  <span v-if="item.substitute" class="details">{{ item.substitute }}</span>
                </span>
              </div>
            </div>

            <div class="d-none d-sm-block hidden-xs">
              <div class="result-header">
                <span class="title">
                  {{ item.name }}
                </span>
                <span v-if="item.ages || (selectedAges.length && !legacyMode)" class="ages">
                  <span class="age-label">{{ 'Ages' | t }}:</span>
                  <span v-if="!selectedAges.length || legacyMode" class="info">
                    {{ item.ages }}
                  </span>
                  <template v-for="age in selectedAges" v-else>
                    <template
                      v-if="
                        (!item.min_age || parseInt(item.min_age) <= age) &&
                          (!item.max_age || parseInt(item.max_age) >= age)
                      "
                    >
                      <AgeIcon :key="age" :age="parseInt(age)" :ages="ages" big />
                    </template>
                  </template>
                </span>
              </div>

              <div class="row">
                <div class="col-sm-4">
                  <div v-if="item.dates" class="item-detail dates">
                    <i class="fa fa-calendar"></i>
                    <span>
                      <span class="info">{{ item.dates }}</span>
                      <br />
                      <span v-if="item.days" class="details">{{ item.days }}</span>
                    </span>
                  </div>

                  <div class="item-detail schedule">
                    <i class="fa fa-clock fa-clock-o"></i>
                    <span class="schedule-items">
                      <span
                        v-for="(schedule, index) in item.schedule"
                        :key="index"
                        class="schedule-item"
                      >
                        <span class="info">{{ schedule.time }}</span>
                        <br />
                        <span class="details">{{ schedule.days }}</span>
                      </span>
                    </span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div v-if="item.location" class="item-detail">
                    <i class="fa fa-map-marker"></i>
                    <span>
                      <span class="info">{{ item.location }}</span>
                      <br />
                      <span v-if="item.roomName" class="details">{{ item.roomName }}</span>
                    </span>
                  </div>

                  <div v-if="item.instructor" class="item-detail instructor">
                    <i class="fa fa-user"></i>
                    <span>
                      <span class="info">{{ item.instructor }}</span>
                      <br />
                      <span v-if="item.substitute" class="details">{{ item.substitute }}</span>
                    </span>
                  </div>
                </div>

                <div class="col-sm-4">
                  <div v-if="item.price" class="item-detail price">
                    <i class="fa fa-money-bill fa-money"></i>
                    <span>
                      <span class="info">{{ item.price }}</span>
                    </span>
                  </div>
                  <AvailableSpots
                    v-if="!disableSpotsAvailable && item.spots_available !== ''"
                    :spots="Number(item.spots_available)"
                    :wait-list="Number(item.wait_list_availability)"
                  />
                </div>
              </div>
            </div>
          </div>
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
            :selected-ages="selectedAges"
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
import AvailableSpots from '@/components/AvailableSpots'
import AgeIcon from '@/components/AgeIcon.vue'
import BookmarkIcon from '@/components/BookmarkIcon'

export default {
  name: 'Results',
  components: {
    Loading,
    ActivityDetailsModal,
    BookmarkedItemsModal,
    BookmarkFeatureModal,
    AvailableSpots,
    AgeIcon,
    BookmarkIcon
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

  .result {
    padding: 10px;
    color: $af-black;

    @include media-breakpoint-up('lg') {
      padding: 20px;
    }

    &:nth-of-type(odd) {
      background-color: $af-light-gray;
    }

    .result-header {
      display: flex;
      justify-content: space-between;
    }

    .title {
      font-size: 14px;
      line-height: 21px;
      color: $af-blue;
      font-weight: bold;
      margin-bottom: 10px;

      @include media-breakpoint-up('lg') {
        font-size: 18px;
        line-height: 27px;
        margin-bottom: 20px;
      }
    }

    .ages-spots {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .age-label {
      font-size: 10px;
      line-height: 15px;
      margin-right: 5px;

      @include media-breakpoint-up('lg') {
        font-size: 12px;
        line-height: 18px;
        margin-right: 10px;
      }
    }

    .item-detail {
      display: flex;
      margin-bottom: 10px;

      @include media-breakpoint-up('lg') {
        margin-bottom: 20px;
      }

      &:last-child {
        margin-bottom: 0;
      }

      .schedule-items {
        display: flex;

        @include media-breakpoint-up('md') {
          flex-direction: column;
        }

        .schedule-item {
          margin-right: 10px;

          @include media-breakpoint-up('md') {
            margin-right: 0;
          }

          &:last-child {
            margin-right: 0;
          }
        }
      }

      .details {
        font-size: 10px;
        line-height: 15px;
      }

      .fa {
        font-size: 20px;
        color: $af-dark-gray;
        margin-right: 10px;
        width: 20px;
        text-align: center;
        flex-shrink: 0;
      }
    }

    .info {
      font-size: 12px;
      line-height: 18px;
    }
  }
}
</style>
