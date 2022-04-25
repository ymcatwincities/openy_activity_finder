<template>
  <div class="foldable-component">
    <div v-b-toggle="collapseId" class="foldable-title">
      <span class="left">
        <i class="fa fa-chevron-down" aria-hidden="true" />
        <i class="fa fa-chevron-up" aria-hidden="true" />
        <span class="title">
          <strong>{{ label }}</strong>
        </span>
      </span>
      <span class="right">
        <span v-if="counterMax > 0 && counter >= counterMax" class="max text-uppercase">
          {{ 'Max' | t }}
        </span>
        <span v-if="counter > 0" class="counter">
          {{ counter }}
        </span>
      </span>
    </div>
    <b-collapse :id="collapseId" role="tabpanel" class="foldable-content">
      <slot />
    </b-collapse>
  </div>
</template>

<script>
export default {
  name: 'Foldable',
  props: {
    label: {
      type: String,
      default: ''
    },
    collapseId: {
      type: String,
      default: 'foldable'
    },
    counter: {
      type: Number,
      default: 0
    },
    counterMax: {
      type: Number,
      default: 0
    }
  }
}
</script>

<style lang="scss">
.foldable-component {
  .foldable-title {
    font-size: 12px;
    line-height: 50px;
    display: flex;
    justify-content: space-between;

    .left {
      display: flex;
      align-items: center;
      flex: auto;
    }

    .counter {
      margin: 10px;
      display: inline-block;
      background-color: $af-blue;
      text-align: center;
      color: $white;
      border-radius: 5px;
      font-weight: bold;
      min-width: 30px;
      font-size: 12px;
      line-height: 30px;
    }

    .max {
      margin-left: 5px;
      color: $af-red;
      font-size: 10px;
      line-height: 50px;
      font-weight: bold;
    }

    &.collapsed .fa-chevron-up,
    &:not(.collapsed) .fa-chevron-down {
      display: none;
    }

    .fa-chevron-up,
    .fa-chevron-down {
      line-height: 50px;
      flex: 0 0 50px;
      text-align: center;
    }
  }

  .foldable-content {
    background-color: $af-light-gray;

    .option {
      display: flex;
      height: 50px;

      input[type='checkbox'],
      input[type='radio'] {
        display: none;

        + label {
          cursor: pointer;
          display: flex;
          position: relative;
          margin: auto 0;
          padding-left: 50px;
          color: $af-black;
          font-size: 12px;
          line-height: 18px;
          font-weight: normal;

          &:before {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            top: 0;
            height: 18px;
            width: 18px;
            margin: auto 16px;
            border: 2px solid $af-dark-gray;
            border-radius: 3px;
            align-self: center;
          }
        }

        &:checked + label {
          &:before {
            background: $af-blue;
            border-width: 0;
          }

          &:after {
            content: '';
            display: block;
            position: absolute;
            left: 19px;
            top: 4px;
            margin-top: 0;
            width: 12px;
            height: 7px;
            border-left: 2px solid $white;
            border-bottom: 2px solid $white;
            transform: rotate(-45deg);
            align-self: center;
          }
        }

        &:disabled + label {
          color: $af-dark-gray;

          &:before {
            border-color: $af-dark-gray;
          }
        }
      }
    }
  }
}
</style>
