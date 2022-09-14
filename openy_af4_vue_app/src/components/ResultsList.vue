<template>
  <div class="results-list-component">
    <div
      v-for="item in results"
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
          <font-awesome-icon icon="calendar" />
          <span>
            <span class="info">{{ item.dates }}</span>
            <br />
            <span v-if="item.days" class="details">{{ item.days }}</span>
          </span>
        </div>

        <div class="item-detail schedule">
          <font-awesome-icon icon="clock" />
          <span class="schedule-items">
            <span v-for="(schedule, index) in item.schedule" :key="index" class="schedule-item">
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
              <font-awesome-icon icon="calendar" />
              <span>
                <span class="info">{{ item.dates }}</span>
                <br />
                <span v-if="item.days" class="details">{{ item.days }}</span>
              </span>
            </div>

            <div class="item-detail schedule">
              <font-awesome-icon icon="clock" />
              <span class="schedule-items">
                <span v-for="(schedule, index) in item.schedule" :key="index" class="schedule-item">
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
              <font-awesome-icon icon="money-bill" />
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
  </div>
</template>

<script>
import AvailableSpots from '@/components/AvailableSpots'
import AgeIcon from '@/components/AgeIcon.vue'

export default {
  name: 'ResultsList',
  components: {
    AvailableSpots,
    AgeIcon
  },
  props: {
    results: {
      type: Array,
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
    legacyMode: {
      type: Boolean,
      required: true
    },
    disableSpotsAvailable: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {}
  },
  methods: {
    showActivityDetailsModal(item) {
      this.$emit('showActivityDetailsModal', item)
    }
  }
}
</script>

<style lang="scss">
.results-list-component {
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

      .fa,
      .svg-inline--fa {
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
