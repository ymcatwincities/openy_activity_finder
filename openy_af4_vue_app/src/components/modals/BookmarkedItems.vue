<template>
  <Modal id="activity-finder-bookmarked-items" v-model="visible" flyout>
    <template v-slot:modal-title>
      <font-awesome-icon icon="bookmark" />
      <span>{{ 'Bookmarked items' | t }}</span>
    </template>
    <template>
      <div class="bookmarked-items-modal-content">
        <div v-if="!cartItems.length">
          <BookmarkFeatureDescription />
        </div>
        <div v-else>
          <div class="message">
            <p>
              <strong>{{ 'The following items are NOT reserved.' | t }}</strong>
              {{ 'Others may still register for them.' | t }}
            </p>
          </div>

          <div class="cart-items">
            <div v-for="(item, index) in cartItems" :key="item.item.nid + '-' + index" class="item">
              <div class="title">{{ item.item.name }}</div>

              <div class="row">
                <div class="col-8 col-xs-8">
                  <div class="item-detail dates">
                    <font-awesome-icon icon="calendar" />
                    <span>
                      <span class="info">{{ item.item.dates }}</span>
                      <br />
                      <span class="details">{{ item.item.days }}</span>
                    </span>
                  </div>

                  <div class="item-detail schedule">
                    <font-awesome-icon icon="clock" />
                    <span class="schedule-items">
                      <span
                        v-for="(schedule, schedule_index) in item.item.schedule"
                        :key="schedule_index"
                        class="schedule-item"
                      >
                        <span class="info">{{ schedule.time }}</span>
                        <br />
                        <span class="details">{{ schedule.days }}</span>
                      </span>
                    </span>
                  </div>
                </div>
                <div class="col-4 col-xs-4">
                  <span class="age">
                    <span class="age-label">Age:</span>
                    <AgeIcon v-if="item.age" :age="parseInt(item.age)" :ages="ages" big />
                    <span v-else class="info">
                      {{ item.item.ages }}
                    </span>
                  </span>
                  <span class="spots">
                    <AvailableSpots
                      v-if="!disableSpotsAvailable && item.item.spots_available !== ''"
                      :spots="Number(item.item.spots_available)"
                      :wait-list="Number(item.item.wait_list_availability)"
                    />
                  </span>
                </div>
              </div>
              <div class="actions">
                <template v-if="buttonsState[index] === 'default'">
                  <a
                    key="register"
                    role="button"
                    class="btn btn-lg register"
                    :class="{ disabled: isRegisterDisabled(index) }"
                    :href="item.item.link"
                    target="_blank"
                    @click="register(index)"
                  >
                    {{ getButtonTitle(index) }}
                    <i class="fa fa-external-link fa-external-link-alt"></i>
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
                    <i class="fa fa-redo fa-repeat"></i>
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
              </div>
            </div>

            <div class="bookmarked-items-footer">
              <div class="footer-content">
                <div class="left">
                  {{ 'Bookmarks are automatically cleared after 5 days.' | t }}
                </div>
                <div class="right">
                  <a role="button" class="clear-all" @click="removeItems">
                    {{ 'Clear All' | t }}
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <BookmarkIcon :counter="cartItems.length" @click="hideModal()" />
    </template>
  </Modal>
</template>

<script>
import Modal from '@/components/modals/Modal.vue'
import BookmarkFeatureDescription from '@/components/modals/BookmarkFeatureDescription.vue'
import AgeIcon from '@/components/AgeIcon.vue'
import AvailableSpots from '@/components/AvailableSpots'
import BookmarkIcon from '@/components/BookmarkIcon'

export default {
  name: 'BookmarkedItemsModal',
  components: {
    Modal,
    BookmarkFeatureDescription,
    AgeIcon,
    AvailableSpots,
    BookmarkIcon
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
    disableSpotsAvailable: {
      type: Boolean,
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
    removeItems() {
      this.trackEvent('unbookmark', 'Click in bookmarked items')
      this.$emit('removeItems')
    },
    resetAction(index) {
      this.buttonsState = {
        ...this.buttonsState,
        ...{ [index]: 'default' }
      }
    },
    getButtonTitle(index) {
      let title = this.t('Register now')
      // parseInt('') -> NaN
      // parseInt('0') -> 0
      if (parseInt(this.cartItems[index].item.spots_available) === 0) {
        title =
          this.cartItems[index].item.wait_list_availability > 0
            ? this.t('Waiting list')
            : this.t('Full')
      }
      return title
    },
    isRegisterDisabled(index) {
      // parseInt('') -> NaN
      // parseInt('0') -> 0
      return (
        parseInt(this.cartItems[index].item.spots_available) === 0 &&
        !this.cartItems[index].item.wait_list_availability
      )
    },
    hideModal() {
      this.visible = false
    }
  }
}
</script>

<style lang="scss">
.bookmarked-items-modal-content {
  padding: 20px 10px;
  color: $af-black;

  .row {
    margin-left: -5px !important;
    margin-right: -5px !important;
  }

  [class*='col-'] {
    padding-left: 5px;
    padding-right: 5px;
  }

  .message {
    padding: 0 10px;
  }

  .cart-items {
    .item {
      padding: 10px;
      border: 1px solid $af-light-gray;
      border-top: 5px solid $af-light-blue;
      margin-bottom: 10px;
      position: relative;

      &:last-child {
        margin-bottom: 0;
      }

      .title {
        color: $af-blue;
        font-size: 14px;
        line-height: 21px;
        font-weight: bold;
        margin-bottom: 10px;
      }

      .item-detail {
        display: flex;
        margin-bottom: 10px;

        .schedule-items {
          display: flex;
          flex-direction: column;
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

      .age {
        display: flex;
        justify-content: space-around;
        margin-bottom: 10px;

        .age-label {
          font-size: 12px;
          line-height: 30px;
        }

        .info {
          line-height: 30px;
        }
      }

      .spots {
        display: flex;
        justify-content: space-around;
      }

      .actions {
        display: flex;
        justify-content: space-between;

        .register {
          background-color: $af-violet;
          flex-grow: 1;
          border-radius: 5px;
          font-size: 18px;
          line-height: 50px;
          font-weight: bolder;
          padding: 0;
        }

        .remove {
          line-height: 46px;
          display: inline-block;
          width: 50px;
          height: 50px;
          border: 2px solid $af-blue;
          border-radius: 5px;
          text-align: center;
          margin-left: 10px;

          .fa,
          .svg-inline--fa {
            font-size: 18px;
            line-height: 46px;
          }
        }

        .action-taken {
          display: flex;
          justify-content: space-between;
          align-items: center;
          background-color: $af-light-gray;
          color: $af-darker-gray;
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

  .bookmarked-items-footer {
    height: 126px;

    .footer-content {
      display: flex;
      justify-content: space-between;
      position: fixed;
      bottom: 0;
      margin-left: -10px;
      margin-right: -10px;
      padding: 10px 20px;
      background-color: $af-light-gray;
      width: calc(100% - 2px);
      max-width: 358px;
      font-size: 12px;
      line-height: 18px;

      .left {
        max-width: 220px;
      }

      .right {
        display: flex;
        flex-direction: column;
        justify-content: space-around;
      }

      .clear-all {
        color: $af-blue;
        font-weight: bold;
      }
    }
  }
}
</style>
