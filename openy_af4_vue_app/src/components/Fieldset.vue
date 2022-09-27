<template>
  <div class="fieldset-component">
    <div v-b-toggle="collapseId" class="fieldset-title">
      <span class="left">
        <span class="title text-uppercase">{{ label }}</span>
      </span>
      <span class="right">
        <span v-if="counterMax > 0 && counter >= counterMax" class="max text-uppercase">
          {{ 'Max' | t }}
        </span>
        <span
          v-else-if="counterOptions >= 0"
          class="options"
          :class="{ 'no-options': counterOptions === 0 }"
        >
          {{ counterOptions | formatPlural('1 Result', '@count Results') }}
        </span>
        <span v-if="collapsible && !counter" class="icon">
          <font-awesome-icon icon="plus" />
          <font-awesome-icon icon="minus" />
        </span>
        <span v-else-if="collapsible && counter && hideCounter" class="icon">
          <font-awesome-icon icon="minus" />
        </span>
        <span v-if="counter" class="counter" :class="{ 'hide-counter': hideCounter }">
          {{ counter }}
        </span>
      </span>
    </div>
    <b-collapse
      v-if="collapsible"
      :id="collapseId"
      role="tabpanel"
      class="fieldset-content"
      :accordion="accordion"
      :visible="!collapsed"
      @shown="handleSticky"
      @hidden="handleSticky"
    >
      <slot />
    </b-collapse>
    <div v-else class="fieldset-content">
      <slot />
    </div>
  </div>
</template>

<script>
export default {
  name: 'Fieldset',
  props: {
    label: {
      type: String,
      default: 'Fieldset'
    },
    collapsible: {
      type: Boolean,
      default: true
    },
    collapsed: {
      type: Boolean,
      default: true
    },
    collapseId: {
      type: String,
      default: 'fieldset'
    },
    counter: {
      type: Number,
      default: 0
    },
    counterMax: {
      type: Number,
      default: 0
    },
    hideCounter: {
      type: Boolean,
      default: false
    },
    counterOptions: {
      type: Number,
      default: -1
    },
    accordion: {
      type: String,
      default: ''
    },
    handleSticky: {
      type: Function,
      default: () => {}
    }
  }
}
</script>

<style lang="scss">
.fieldset-component {
  .fieldset-title {
    border: 1px solid $af-dark-gray;
    padding: 0 10px;
    display: flex;
    justify-content: space-between;
    min-height: 50px;

    .left,
    .right {
      display: flex;
      align-items: center;
    }

    .title {
      font-size: 12px;
      line-height: 18px;
      font-weight: bold;

      @include media-breakpoint-up('lg') {
        font-size: 14px;
        line-height: 50px;
      }
    }

    .counter {
      margin-left: 10px;
      display: inline-block;
      background-color: $af-blue;
      text-align: center;
      color: $white;
      border-radius: 5px;
      font-weight: bold;
      min-width: 30px;
      font-size: 12px;
      line-height: 30px;

      &.hide-counter {
        display: none;
      }
    }

    .right {
      white-space: nowrap;
      margin-left: 10px;
    }

    .max {
      color: $af-red;
      font-size: 10px;
      line-height: 15px;
      font-weight: bold;
    }

    .options {
      white-space: nowrap;
      font-size: 10px;
      line-height: 50px;

      &.no-options {
        color: $af-red;
        font-weight: bold;
      }
    }

    .icon {
      font-size: 18px;
      line-height: 50px;
      margin-left: 10px;
      padding: 0 8px;
    }

    &.collapsed .fa-minus,
    &:not(.collapsed) .fa-plus {
      display: none;
    }

    &.collapsed {
      border-bottom-width: 0;
      .hide-counter {
        display: inline-block;
      }
    }
  }

  &:last-child {
    .fieldset-title {
      &.collapsed {
        border-bottom-width: 1px;
      }
    }
  }

  .fieldset-content {
    .options {
      background-color: $af-light-gray;
      padding: 10px 10px 5px;

      @include media-breakpoint-up('lg') {
        padding: 20px 20px 10px;
      }

      .row {
        margin-left: -5px;
        margin-right: -5px;
        display: flex;
        flex-wrap: wrap;

        @include media-breakpoint-up('md') {
          margin-left: -10px;
          margin-right: -10px;
        }
      }

      .option {
        margin-bottom: 5px;
        padding-left: 5px;
        padding-right: 5px;

        @include media-breakpoint-up('md') {
          padding-left: 10px;
          padding-right: 10px;
        }

        @include media-breakpoint-up('lg') {
          margin-bottom: 10px;
        }

        input[type='checkbox'],
        input[type='radio'] {
          display: none;

          & + label {
            position: relative;
            background-color: $white;
            border: 2px solid $af-blue;
            border-radius: 5px;
            display: flex;
            margin: 0;
            font-family: Verdana, Geneva, sans-serif;
            height: 100%;
            padding: 4px 10px;

            &:before {
              content: '';
              border: 2px solid $af-dark-gray;
            }

            .title {
              font-size: 12px;
              line-height: 18px;
              font-weight: bold;
              color: $af-blue;
              display: block;
            }

            .description {
              font-size: 10px;
              line-height: 15px;
              font-weight: bold;
              display: block;
            }

            .results-count {
              font-size: 10px;
              line-height: 15px;
              font-weight: normal;
              color: $af-dark-gray;
              display: block;
            }
          }
        }

        input[type='checkbox']:checked + label,
        input[type='radio']:checked + label {
          background-color: $af-blue;
          border-color: $af-blue;

          .title,
          .description,
          .results-count {
            color: $white;
          }
        }

        &.check {
          label {
            &:before {
              color: $white;
              border-radius: 3px;
              margin: 12px 16px 12px 6px;
              width: 18px;
              height: 18px;
              flex: 0 0 18px;
            }
          }

          input[type='checkbox']:checked + label,
          input[type='radio']:checked + label {
            &:before {
              border: none;
              background-color: $white;
            }

            &:after {
              content: '';
              display: block;
              position: absolute;
              left: 19px;
              top: 20px;
              width: 12px;
              height: 7px;
              border-left: 2px solid $af-blue;
              border-bottom: 2px solid $af-blue;
              transform: rotate(-45deg);
            }
          }

          input[type='checkbox']:disabled + label,
          input[type='radio']:disabled + label {
            background-color: $af-light-gray;
            border-color: $af-light-gray;
            cursor: default;

            .title {
              color: $af-dark-gray;
            }

            .results-count {
              color: $af-red;
              font-weight: bold;
            }
          }
        }

        &.radio {
          margin-top: 0;

          label {
            padding-left: 0px;

            &:before {
              padding: 3px;
              border-radius: 50%;
              background-color: $white;
              background-clip: content-box;
              box-shadow: inset 0 0 0 3px $white;
              margin: 11px 15px 11px 5px;
              width: 20px;
              height: 20px;
              flex: 0 0 20px;
            }
          }

          input[type='checkbox']:checked + label,
          input[type='radio']:checked + label {
            &:before {
              border-color: $white;
              box-shadow: inset 0 0 0 3px $af-blue;
            }
          }

          input[type='checkbox']:disabled + label,
          input[type='radio']:disabled + label {
            background-color: $af-light-gray;
            border-color: $af-light-gray;
            cursor: default;

            &:before {
              box-shadow: inset 0 0 0 3px $af-light-gray;
              background-color: $af-light-gray;
            }

            .title {
              color: $af-dark-gray;
            }

            .results-count {
              color: $af-red;
              font-weight: bold;
            }
          }
        }
      }
    }
  }
}
</style>
