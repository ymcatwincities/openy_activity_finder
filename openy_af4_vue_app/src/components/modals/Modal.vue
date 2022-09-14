<template>
  <b-modal
    :id="id"
    v-model="visible"
    :title="title"
    no-stacking
    centered
    scrollable
    :hide-footer="true"
    :modal-class="{
      'af-modal': true,
      'af-flyout': flyout,
      'af-narrow': narrow,
      'af-responsive': responsive
    }"
    modal-outer-class="af-modal-wrapper"
    dialog-class="af-modal-dialog"
    header-class="af-modal-header"
    title-class="af-modal-title"
    content-class="af-modal-content"
    body-class="af-modal-body"
  >
    <template v-slot:modal-header-close>
      <span aria-hidden="true">&times;</span>
    </template>
    <template v-slot:modal-title>
      <slot name="modal-title" />
    </template>
    <template v-slot:default>
      <slot />
    </template>
  </b-modal>
</template>

<script>
import '@/../node_modules/bootstrap/scss/_modal.scss'

export default {
  name: 'Modal',
  props: {
    value: {
      type: Boolean,
      default: false
    },
    id: {
      type: String,
      required: true
    },
    title: {
      type: String,
      default: ''
    },
    flyout: {
      type: Boolean,
      default: false
    },
    narrow: {
      type: Boolean,
      default: false
    },
    responsive: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      visible: this.value
    }
  },
  watch: {
    value() {
      this.visible = this.value
    },
    visible() {
      this.$emit('input', this.visible)
    }
  }
}
</script>

<style lang="scss">
[modal-outer-class='af-modal-wrapper'] {
  z-index: 2001 !important;
}
.af-modal {
  // TODO: Move out to another place - MPR-163.
  // Overriding Open Y Lily customizations and Bootstrap 3/4 compatibilities.
  &.modal.show {
    &.show {
      opacity: 1;

      .af-modal-content {
        border-radius: 0.3rem;
      }

      .af-modal-header {
        padding: 1rem;
        border-top-left-radius: calc(0.3rem - 1px);
        border-top-right-radius: calc(0.3rem - 1px);

        .close {
          margin-top: -1rem;
        }
      }

      .af-modal-body {
        .row {
          margin-left: -15px;
          margin-right: -15px;
        }
      }
    }

    .af-modal-dialog {
      display: flex;
      transform: none;
      margin: $modal-dialog-margin;
      width: auto;
      height: unset;

      @include media-breakpoint-up(sm) {
        margin: $modal-dialog-margin-y-sm-up auto;
      }
    }
  }

  // Activity Finder general modal styles.
  &.modal.af-flyout {
    .af-modal-dialog {
      padding: 0;
      margin: auto;
      height: 100%;
      max-height: 100%;
      position: fixed;
      right: 0;
      width: 100%;
      max-width: 360px;
    }

    .af-modal-content {
      height: 100%;
      max-height: 100%;
    }

    &.fade {
      .af-modal-dialog {
        right: -360px;
        transition: opacity 0.3s linear, right 0.3s ease-out;
      }
    }

    &.show {
      .af-modal-dialog {
        right: 0;
      }
    }
  }

  &.modal.af-narrow {
    .af-modal-content {
      max-width: 340px;
      margin: 0 auto;
    }

    &.af-responsive {
      @include media-breakpoint-up('lg') {
        .af-modal-content {
          max-width: 680px;
          width: 680px;
        }
      }
    }
  }

  .af-modal-dialog {
    padding-left: 5px;
    padding-right: 5px;
  }

  .af-modal-header {
    padding: 0 0 0 1rem !important;
    background-color: $white;
    border-bottom: 1px solid $af-dark-gray;
    align-items: center;

    &:after {
      width: 0;
      height: 0;
    }

    .af-modal-title {
      color: $af-dark-gray;
      text-transform: uppercase;
      font-size: 18px;
      line-height: 27px;

      .fa,
      .svg-inline--fa {
        width: 50px;
        margin-left: -10px;
        text-align: center;
      }
    }

    .close {
      color: $af-dark-gray;
      font-size: 3rem;
      font-weight: 100;
      position: relative;
      padding: 0;
      margin: 0 0 0 auto !important;
      width: 50px;
      height: 50px;
      background-color: $white;
      opacity: 1;
    }
  }

  .af-modal-body {
    padding: 0;
  }
}
</style>
