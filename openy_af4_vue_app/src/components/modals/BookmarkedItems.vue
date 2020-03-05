<template>
  <Modal id="activity-finder-bookmarked-items" v-model="visible" flyout>
    <template v-slot:modal-title>
      <i class="fa fa-bookmark"></i>
      <span>Bookmarked items</span>
    </template>
    <template>
      <div class="bookmarked-items-modal-content">
        <div class="row">
          <div v-if="!cartItems.length" class="col-xs-12">
            <BookmarkFeatureDescription />
          </div>
          <div v-else class="col-xs-12">
            <div class="message">
              <p>
                <strong>The following items are NOT reserved.</strong> Others may still register for
                them.
              </p>
            </div>

            <div class="cart-items">
              <div
                v-for="(item, index) in cartItems"
                :key="item.item.nid + '_' + index"
                class="item"
              >
                <div class="title">{{ item.item.name }}</div>

                <div class="row">
                  <div class="col-xs-8">
                    <div class="dates">
                      <i class="fa fa-calendar"></i>
                      <span>{{ item.item.dates }}</span>
                    </div>

                    <div class="schedule">
                      <i class="fa fa-clock-o"></i>
                      <span>
                        <span
                          v-for="(schedule, schedule_index) in item.item.schedule"
                          :key="schedule_index"
                        >
                          <span class="time">{{ schedule.time }}</span>
                          <br />
                          <span class="days">{{ schedule.days }}</span>
                        </span>
                      </span>
                    </div>
                  </div>
                  <div class="col-xs-4">
                    <span class="age">
                      <span class="age-label">Age:</span>
                      <AgeIcon v-if="item.age" :age="parseInt(item.age)" :ages="ages" big />
                      <span v-else>
                        {{ item.item.ages }}
                      </span>
                    </span>
                  </div>
                </div>
                <div class="actions">
                  <template v-if="buttonsState[index] === 'default'">
                    <a
                      key="register"
                      role="button"
                      class="btn btn-lg register"
                      :href="item.item.link"
                      target="_blank"
                      @click="register(index)"
                    >
                      {{ 'Register now' | t }}
                      <i class="fa fa-external-link"></i>
                    </a>
                    <a
                      key="remove"
                      role="button"
                      class="remove"
                      title="Remove"
                      @click="removeItem(index)"
                    >
                      <i class="fa fa-trash"></i>
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
                </div>
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
import BookmarkFeatureDescription from '@/components/modals/BookmarkFeatureDescription.vue'
import AgeIcon from '@/components/AgeIcon.vue'

export default {
  name: 'BookmarkedItemsModal',
  components: {
    Modal,
    BookmarkFeatureDescription,
    AgeIcon
  },
  props: {
    value: {
      type: Boolean,
      default: false
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
    }
  },
  data() {
    return {
      visible: this.value,
      buttonsState: {}
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
        this.cartItems.forEach((item, index) => {
          this.buttonsState = {
            ...this.buttonsState,
            ...{ [index]: 'default' }
          }
        })
      }
    },
    cartItems() {
      this.buttonsState = {}
      this.cartItems.forEach((item, index) => {
        this.buttonsState = {
          ...this.buttonsState,
          ...{ [index]: 'default' }
        }
      })
    }
  },
  methods: {
    register(index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'sentToRegister' }
      }
      this.trackEvent(
        'register',
        'Click in bookmarked items',
        this.cartItems[index].item.product_id
      )
    },
    removeItem(index) {
      this.trackEvent(
        'unbookmark',
        'Click in bookmarked items',
        this.cartItems[index].item.product_id
      )
      this.$emit('removeItem', index)
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
.bookmarked-items-modal-content {
  padding: 20px 10px;
  color: $af-black;

  .message {
    padding: 0 10px;
  }

  .cart-items {
    .item {
      padding: 10px;
      border: 1px solid $af-light-gray;
      border-top: 5px solid $af-light-blue;
      margin: 10px 0;
      position: relative;

      .title {
        color: $af-blue;
        font-size: 14px;
        line-height: 21px;
        font-weight: bold;
        margin-bottom: 10px;
      }

      .dates {
        font-size: 12px;
        line-height: 18px;
        margin-bottom: 10px;

        .fa {
          font-size: 20px;
          color: $af-dark-gray;
          margin-right: 10px;
        }
      }

      .schedule {
        display: flex;
        margin-bottom: 10px;

        .fa {
          font-size: 22px;
          color: $af-dark-gray;
          margin-right: 10px;
        }

        .time {
          font-size: 12px;
          line-height: 18px;
        }

        .days {
          font-size: 10px;
          line-height: 15px;
        }
      }

      .age {
        display: flex;
        justify-content: space-around;

        .age-label {
          font-size: 12px;
          line-height: 30px;
        }
      }

      .actions {
        display: flex;
        justify-content: space-between;

        .register {
          background-color: $af-violet;
          flex-grow: 1;
          margin-right: 10px;
          border-radius: 5px;
          font-size: 18px;
          line-height: 50px;
          font-weight: bolder;
          padding: 0;
        }

        .remove {
          display: inline-block;
          width: 50px;
          height: 50px;
          border: 2px solid $af-blue;
          border-radius: 5px;
          text-align: center;

          .fa {
            font-size: 18px;
            line-height: 46px;
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
}
</style>
