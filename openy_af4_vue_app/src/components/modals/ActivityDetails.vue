<template>
  <Modal id="activity-finder-activity-details" v-model="visible" title="Activity details">
    <template>
      <div class="activity-details-modal-content">
        <div class="row">
          <div class="col-xs-12 col-md-6 left-wrapper">
            <div class="left">
              <span class="title">{{ item.name }}</span>
              <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
              </p>
              <div class="row">
                <div class="col-xs-3"><strong>Ages:</strong></div>
                <div class="col-xs-9">{{ item.ages }}</div>
              </div>
              <div class="row">
                <div class="col-xs-3"><strong>Gender:</strong></div>
                <div class="col-xs-9">Girls</div>
              </div>
              <a :href="item.link" target="_blank">Learn more about this program</a>
            </div>
          </div>
          <div class="col-xs-12 col-md-6 right-wrapper">
            <div class="right">
              <div class="info">
                <span>
                  <strong>{{ item.dates }}</strong>
                </span>
                <br />
                <span v-for="(schedule, index) in item.schedule" :key="index" class="schedule">
                  {{ schedule.days }}<br />
                  {{ schedule.time }}
                </span>
                <br />
                <span>
                  <strong>{{ item.location }}</strong>
                </span>
                <br />
                <span>Room Name</span>
                <br />
                <span>
                  <strong>Instructor Name</strong>
                </span>
                <br />
                <span>SUB: Instructor Name</span>
                <br />
                <span>{{ item.price }}</span>
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
                    {{ 'Register' | t }}
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
                    <i class="fa fa-bookmark-o"></i>
                  </a>
                  <a
                    v-else-if="!legacyMode"
                    key="bookmark"
                    role="button"
                    class="bookmark bookmarked"
                    title="Remove bookmark"
                    @click="unbookmarkItem(age, index)"
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
                <template v-else-if="buttonsState[index] === 'itemUnbookmarked'">
                  <a
                    key="reset"
                    role="button"
                    class="btn btn-lg action-taken"
                    @click="resetAction(index)"
                  >
                    <span>{{ 'Item unbookmarked' | t }}</span>
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

export default {
  name: 'ActivityDetailsModal',
  components: {
    Modal,
    AgeIcon
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
    unbookmarkItem(age, index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'itemUnbookmarked' }
      }
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
  }

  .left-wrapper,
  .right-wrapper {
    padding-left: 10px;
    padding-right: 10px;
  }

  .left {
    margin: 20px 10px;

    @include media-breakpoint-up('sm') {
      margin-left: 20px;
    }

    .row {
      margin-left: -15px !important;
      margin-right: -15px !important;
    }
  }

  .right {
    margin: 20px 10px;

    @include media-breakpoint-up('sm') {
      margin-right: 20px;
    }
  }

  .left {
    .title {
      font-size: 14px;
      line-height: 21px;
      font-weight: bold;
    }
  }

  .right-wrapper {
    background-color: $af-light-gray;

    .right {
      .info {
        margin-bottom: 20px;
      }

      .action {
        padding: 10px;
        background-color: $white;
        border: 1px solid $af-darker-gray;
        border-radius: 5px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;

        .register {
          background-color: $af-violet;
          flex-grow: 1;
          border-radius: 5px;
          font-size: 18px;
          line-height: 27px;
          font-weight: bolder;
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
  }

  .age-icons {
    line-height: 50px;
    width: 50px;
    text-align: center;
    margin-right: 10px;
  }
}
</style>
