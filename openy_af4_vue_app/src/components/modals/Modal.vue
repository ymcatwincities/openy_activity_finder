<template>
  <b-modal
    :id="id"
    v-model="visible"
    :title="title"
    no-stacking
    centered
    scrollable
    :hide-footer="true"
    :modal-class="{ 'af-modal': true, 'af-flyout': flyout, 'af-narrow': narrow }"
    dialog-class="af-modal-dialog"
    header-class="af-modal-header"
    content-class="af-modal-content"
    body-class="af-modal-body"
  >
    <template v-slot:modal-header-close>
      &times;
    </template>
    <template v-slot:default="{ ok, cancel, close, hide }">
      <slot :ok="ok" :cancel="cancel" :close="close" :hide="hide" />
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
      required: true
    },
    flyout: {
      type: Boolean,
      default: false
    },
    narrow: {
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
.af-modal {
  // Overriding YGTC customizations and Bootstrap 3/4 compatibilities.
  &.modal.show {
    opacity: 1;

    .modal-dialog {
      display: flex;
      transform: none;
      margin: $modal-dialog-margin;
      width: auto;
      height: unset;

      @include media-breakpoint-up(sm) {
        margin: $modal-dialog-margin-y-sm-up auto;
      }
    }

    .modal-content {
      border-radius: 0.3rem;
    }

    .modal-header {
      padding: 1rem;
      border-top-left-radius: calc(0.3rem - 1px);
      border-top-right-radius: calc(0.3rem - 1px);

      .close {
        margin-top: -1rem;
      }
    }

    .modal-body {
      .row {
        margin-left: -15px;
        margin-right: -15px;
      }
    }
  }

  // Activity Finder general modal styles.
  &.modal.show.af-flyout {
    .modal-dialog {
      padding: 0;
      margin: 0;
      height: 100%;
      max-height: 100%;
      position: absolute;
      right: 0;
      width: 100%;
      max-width: 360px;
    }

    .modal-content {
      height: 100%;
      max-height: 100%;
    }
  }

  &.modal.show.af-narrow {
    .modal-content {
      max-width: 340px;
    }
  }

  .modal-dialog {
    padding-left: 5px;
    padding-right: 5px;
  }

  .modal-header {
    background-color: $white;
    border-bottom: 1px solid $af-dark-gray;
    align-items: center;

    .modal-title {
      color: $af-dark-gray;
      text-transform: uppercase;
      font-size: 18px;
      line-height: 27px;
    }

    .close {
      color: $white;
      background-color: $af-blue;
      opacity: 1;
      padding: 14px 19px;
    }
  }

  .modal-body {
    padding: 0;
  }
}
</style>
