<template>
  <div class="step-component">
    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <div class="top">
            <div class="controls">
              <span class="title">
                <slot name="title" />
              </span>
              <span class="buttons hidden-xs hidden-sm">
                <button
                  v-if="filtersSelected"
                  type="button"
                  class="btn btn-lg btn-next"
                  @click="onNext"
                >
                  {{ nextLabel }}
                </button>
                <button v-else type="button" class="btn btn-lg btn-skip" @click="onSkip">
                  {{ skipLabel }}
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <slot :handle-sticky="handleSticky" />
        </div>
      </div>
    </div>

    <div ref="bottom" class="bottom hidden-xs hidden-sm">
      <div class="container" :class="{ sticky: sticky }">
        <div class="row">
          <div class="col-xs-12">
            <div class="full-width separator"></div>
            <div class="controls">
              <span class="title">
                <slot name="title" />
              </span>
              <span class="buttons">
                <button
                  v-if="filtersSelected"
                  type="button"
                  class="btn btn-lg btn-next"
                  @click="onNext"
                >
                  {{ nextLabel }}
                </button>
                <button v-else type="button" class="btn btn-lg btn-skip" @click="onSkip">
                  {{ skipLabel }}
                </button>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div ref="bottomMobile" class="full-width hidden-md hidden-lg">
      <div class="buttons-mobile" :class="{ sticky: stickyMobile }">
        <div class="separator"></div>
        <button v-if="filtersSelected" type="button" class="btn btn-lg btn-next" @click="onNext">
          {{ nextLabel }}
        </button>
        <button v-else type="button" class="btn btn-lg btn-skip" @click="onSkip">
          {{ skipLabel }}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Step',
  props: {
    filtersSelected: {
      type: Boolean,
      default: false
    },
    skipLabel: {
      type: String,
      default: 'Skip'
    },
    nextLabel: {
      type: String,
      default: 'Next'
    }
  },
  data() {
    return {
      sticky: false,
      stickyMobile: false,
      stickyHeight: 95,
      stickyMobileHeight: 60
    }
  },
  mounted() {
    this.handleSticky()
    window.addEventListener('scroll', this.handleSticky)
    window.addEventListener('resize', this.handleSticky)
  },
  beforeDestroy() {
    window.removeEventListener('scroll', this.handleSticky)
    window.removeEventListener('resize', this.handleSticky)
  },
  methods: {
    onSkip() {
      this.$emit('skip')
    },
    onNext() {
      this.$emit('next')
    },
    handleSticky() {
      const clientHeight = window.document.documentElement.clientHeight
      const rect = this.$refs.bottom.getBoundingClientRect()
      const rectMobile = this.$refs.bottomMobile.getBoundingClientRect()
      this.sticky = rect.top + this.stickyHeight >= clientHeight ? true : false
      this.stickyMobile = rectMobile.top + this.stickyMobileHeight >= clientHeight ? true : false
    }
  }
}
</script>

<style lang="scss">
.step-component {
  .top {
    margin-top: 20px;
    margin-bottom: 20px;

    @include media-breakpoint-up('lg') {
      margin-top: 40px;
      margin-bottom: 40px;
    }
  }

  .bottom {
    height: 95px;

    @include media-breakpoint-up('lg') {
      margin-top: 40px;
    }

    .sticky {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      background-color: $white;
    }

    .controls {
      padding-top: 20px;
      padding-bottom: 20px;
    }
  }

  .controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: $af-black;

    .title {
      font-size: 12px;
      line-height: 18px;

      @include media-breakpoint-up('lg') {
        font-size: 14px;
        line-height: 21px;
      }
    }

    .buttons {
      .btn {
        border-radius: 5px;
        font-weight: bolder;
        font-size: 18px;
        line-height: 46px;
        padding: 0 30px;

        &.btn-skip {
          background-color: $white;
          color: $af-blue;
          border: 2px solid $af-blue;
        }

        &.btn-next {
          background-color: $af-violet;
          border: 2px solid $af-violet;
        }
      }
    }
  }

  .full-width {
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
  }

  .separator {
    height: 5px;
    background-color: $af-black;
    opacity: 0.2;
  }

  .buttons-mobile {
    bottom: 0;
    left: 0;
    width: 100%;

    &.sticky {
      position: fixed;
    }

    .separator {
      opacity: 0.1;
    }

    .btn {
      width: 100%;
      font-weight: bold;
      font-size: 18px;
      line-height: 46px;
      padding: 0 10px;

      &.btn-skip {
        background-color: $white;
        color: $af-blue;
        border: 2px solid $af-blue;
      }

      &.btn-next {
        background-color: $af-violet;
        border: 2px solid $af-violet;
      }
    }
  }
}
</style>
