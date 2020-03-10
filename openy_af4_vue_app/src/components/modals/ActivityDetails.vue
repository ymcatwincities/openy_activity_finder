<template>
  <Modal
    id="activity-finder-activity-details"
    v-model="visible"
    :title="'Activity details' | t"
    narrow
    responsive
  >
    <template>
      <div class="activity-details-modal-content">
        <div class="row">
          <div class="col-xs-12 col-md-6 left-wrapper">
            <div class="left">
              <div class="title">{{ item.name }}</div>
              <div class="description">{{ item.description }}</div>
              <div class="row ages">
                <div class="col-xs-3">
                  <strong>{{ 'Ages:' | t }}</strong>
                </div>
                <div class="col-xs-9">{{ item.ages }}</div>
              </div>
              <div v-if="item.gender" class="row gender">
                <div class="col-xs-3">
                  <strong>{{ 'Gender:' | t }}</strong>
                </div>
                <div class="col-xs-9">{{ item.gender }}</div>
              </div>
              <a :href="item.link" target="_blank" class="learn-more">
                {{ 'Learn more about this program' | t }}
                <i class="fa fa-external-link"></i>
              </a>
            </div>
          </div>
          <div class="col-xs-12 col-md-6 right-wrapper">
            <div class="right">
              <div class="info-section">
                <div v-if="item.dates" class="item-detail dates">
                  <i class="fa fa-calendar"></i>
                  <span>
                    <span class="info">{{ item.dates }}</span>
                    <br />
                    <span v-if="item.days" class="details">{{ item.days }}</span>
                  </span>
                </div>

                <div class="item-detail schedule">
                  <i class="fa fa-clock-o"></i>
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

                <div v-if="item.price" class="item-detail price">
                  <i class="fa fa-money"></i>
                  <span>
                    <span class="info">{{ item.price }}</span>
                  </span>
                </div>
                <AvailableSpots
                  :spots="Number(item.spots_available)"
                  :wait-list="Number(item.wait_list_availability)"
                  big
                />
              </div>
              <div v-for="(age, index) in availableAges" :key="age" class="action">
                <span v-if="age && !legacyMode" class="age-icons">
                  <AgeIcon :age="parseInt(age)" :ages="ages" big />
                </span>
                <template v-if="buttonsState[index] === 'default'">
                  <a
                    key="register"
                    role="button"
                    class="btn btn-lg register"
                    :href="item.link"
                    target="_blank"
                    @click="register(index)"
                  >
                    {{ getButtonTitle }}
                    <i class="fa fa-external-link"></i>
                  </a>
                  <a
                    v-if="!isBookmarked(age) && !legacyMode"
                    key="bookmark"
                    role="button"
                    class="bookmark"
                    title="Add bookmark"
                    @click="bookmarkItem(age, index)"
                  >
                    <i class="fa fa-bookmark"></i>
                  </a>
                  <a
                    v-else-if="!legacyMode"
                    key="bookmark"
                    role="button"
                    class="bookmark bookmarked"
                    title="Remove bookmark"
                    @click="unbookmarkItem(age)"
                  >
                    <i class="fa fa-bookmark"></i>
                  </a>
                </template>
                <template v-else-if="buttonsState[index] === 'sentToRegister'">
                  <a
                    key="reset"
                    role="button"
                    class="btn btn-lg action-taken"
                    @click="resetAction(index)"
                  >
                    <span>{{ 'Sent to register' | t }}</span>
                    <i class="fa fa-times-circle-o"></i>
                  </a>
                </template>
                <template v-else-if="buttonsState[index] === 'itemBookmarked'">
                  <a
                    key="reset"
                    role="button"
                    class="btn btn-lg action-taken"
                    @click="resetAction(index)"
                  >
                    <span>{{ 'Item bookmarked' | t }}</span>
                    <i class="fa fa-times-circle-o"></i>
                  </a>
                </template>
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
import AgeIcon from '@/components/AgeIcon.vue'
import AvailableSpots from '@/components/AvailableSpots'

export default {
  name: 'ActivityDetailsModal',
  components: {
    Modal,
    AgeIcon,
    AvailableSpots
  },
  props: {
    value: {
      type: Boolean,
      default: false
    },
    item: {
      type: Object,
      required: true
    },
    cartItems: {
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
      type: Number,
      required: true
    }
  },
  data() {
    return {
      visible: this.value,
      buttonsState: {}
    }
  },
  computed: {
    availableAges() {
      if (this.legacyMode) {
        return [null]
      }

      const availableAges = this.selectedAges.filter(age => {
        return (
          parseInt(this.item.min_age) <= parseInt(age) &&
          parseInt(this.item.max_age) >= parseInt(age)
        )
      })

      return availableAges.length ? availableAges : [null]
    },
    getButtonTitle() {
      let title = this.t('Register')
      if (Number(this.item.spots_available) === 0 && Number(this.item.wait_list_availability) > 0) {
        title = this.t('Waiting list')
      }
      return title
    }
  },
  watch: {
    value() {
      this.visible = this.value
    },
    visible() {
      this.$emit('input', this.visible)
      if (this.visible) {
        this.buttonsState = {}
        this.availableAges.forEach((age, index) => {
          this.buttonsState = {
            ...this.buttonsState,
            ...{ [index]: 'default' }
          }
        })
      }
    }
  },
  methods: {
    isBookmarked(age) {
      let bookmarked = false
      this.cartItems.forEach(item => {
        if (item.age === age && item.item.product_id === this.item.product_id) {
          bookmarked = true
        }
      })

      return bookmarked
    },
    register(index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'sentToRegister' }
      }
      this.trackEvent('register', 'Click in activity details', this.item.product_id)
    },
    bookmarkItem(age, index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'itemBookmarked' }
      }
      this.trackEvent('bookmark', 'Click in activity details', this.item.product_id)
      this.$emit('bookmark', age)
    },
    unbookmarkItem(age) {
      this.trackEvent('unbookmark', 'Click in activity details', this.item.product_id)
      this.$emit('unbookmark', age)
    },
    resetAction(index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'default' }
      }
    }
  }
}
</script>

<style lang="scss">
.activity-details-modal-content {
  color: $af-black;

  .row {
    margin-left: -10px !important;
    margin-right: -10px !important;

    @include media-breakpoint-up('lg') {
      display: flex;
    }
  }

  [class*='col-'] {
    padding-left: 10px;
    padding-right: 10px;
  }

  .right-wrapper {
    display: flex;
    background-color: $af-light-gray;
  }

  .left {
    margin: 20px 10px;

    @include media-breakpoint-up('lg') {
      margin-right: 0;
    }

    .title,
    .description,
    .ages,
    .gender {
      margin-bottom: 10px;
    }

    .title {
      font-size: 18px;
      line-height: 24px;
      font-weight: bold;
    }

    .description,
    .ages,
    .gender,
    .learn-more {
      font-size: 12px;
      line-height: 18px;
    }

    .learn-more {
      color: $af-blue;
    }
  }

  .right {
    margin: 20px 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    width: 100%;

    @include media-breakpoint-up('lg') {
      margin-left: 0;
    }

    .info-section {
      margin-bottom: 20px;
    }

    .item-detail {
      display: flex;
      margin-bottom: 10px;

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

      .info {
        font-size: 12px;
        line-height: 18px;
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

    .action {
      padding: 10px;
      background-color: $white;
      border: 1px solid $af-darker-gray;
      border-radius: 5px;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;

      &:last-child {
        margin-bottom: 0;
      }

      .register {
        background-color: $af-violet;
        flex-grow: 1;
        border-radius: 5px;
        font-size: 18px;
        line-height: 50px;
        font-weight: bolder;
        padding: 0;
      }

      .bookmark {
        display: inline-block;
        width: 50px;
        height: 50px;
        border-radius: 5px;
        text-align: center;
        margin-left: 10px;
        border: 2px solid $af-blue;

        .fa {
          color: $af-blue;
          font-size: 18px;
          line-height: 46px;
        }
      }

      .bookmarked {
        border: none;
        background-color: $af-green;

        .fa {
          color: $white;
          line-height: 50px;
        }
      }

      .action-taken {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: $af-green;
        flex-grow: 1;
        border-radius: 5px;
        font-size: 18px;
        line-height: 50px;
        font-weight: bolder;
        padding: 0 15px 0 20px;

        .fa {
          font-size: 20px;
        }
      }
    }
  }

  .age-icons {
    line-height: 50px;
    width: 50px;
    text-align: center;
    margin-right: 10px;
  }
}
</style>
