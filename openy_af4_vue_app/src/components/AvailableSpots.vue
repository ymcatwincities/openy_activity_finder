<template>
  <span class="available-spots-component" :class="classes">
    {{ value }}
  </span>
</template>

<script>
export default {
  name: 'AvailableSpots',
  props: {
    spots: {
      type: Number,
      default: 0
    }
  },
  computed: {
    value() {
      if (this.spots === 0) {
        return this.t('Full')
      } else if (this.spots < 10) {
        return this.formatPlural(this.spots, '1 spot', '@count spots')
      } else {
        return this.t('10+ spots')
      }
    },
    classes() {
      if (this.spots === 0) {
        return ['full']
      } else if (this.spots <= 3) {
        return ['low']
      } else {
        return []
      }
    }
  }
}
</script>

<style lang="scss">
.available-spots-component {
  display: inline-block;
  font-size: 10px;
  line-height: 18px;
  padding: 0 4px;
  color: $af-dark-gray;
  background-color: $white;
  border: 1px solid $af-dark-gray;
  border-radius: 3px;

  @include media-breakpoint-up('lg') {
    font-size: 12px;
    line-height: 23px;
  }

  &.full {
    color: $white;
    background-color: $af-black;
    border-color: $af-black;
    font-weight: bold;
    text-transform: uppercase;
  }

  &.low {
    color: $white;
    background-color: $af-red;
    border-color: $af-red;
    font-weight: bold;
  }
}
</style>
