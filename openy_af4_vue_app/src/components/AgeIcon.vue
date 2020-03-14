<template>
  <span class="age-icon-component" :class="classes">
    {{ label }}
  </span>
</template>

<script>
export default {
  name: 'AgeIcon',
  props: {
    age: {
      type: Number,
      required: true
    },
    ages: {
      type: Array,
      required: true
    },
    big: {
      type: Boolean,
      default: false
    }
  },
  computed: {
    label() {
      const ageFilter = this.ages.find(x => parseInt(x.value) === this.age)
      // Replace all non digits except 'm' and '+' with empty string.
      // '12mos' => '12m'
      // '6yrs' => '6'
      // '16+yrs' => '16+'
      return ageFilter.label.replace(/[^\dm+]/g, '')
    },
    classes() {
      const classes = ['age-icon-' + this.age]

      if (this.age === 6) {
        classes.push('age-icon-width-middle')
      } else if (this.label.includes('m') || this.label.includes('+')) {
        classes.push('age-icon-width-wide')
      } else if (this.age >= 120) {
        classes.push('age-icon-width-middle')
      } else {
        classes.push('age-icon-width-narrow')
      }

      if (this.big) {
        classes.push('age-icon-big')
      }

      return classes
    }
  }
}
</script>

<style lang="scss">
.age-icon-component {
  font-weight: bold;
  border-radius: 2em;
  display: inline-block;
  text-align: center;
  font-size: 14px;
  line-height: 30px;
  height: 30px;
  margin-right: 5px;

  &:last-child {
    margin-right: 0;
  }

  @include media-breakpoint-up('lg') {
    margin-right: 10px;
  }

  &.age-icon-width-narrow {
    width: 30px;
  }

  &.age-icon-width-middle {
    width: 35px;
  }

  &.age-icon-width-wide {
    width: 40px;
  }

  @each $age, $colors in $af-ages {
    &.age-icon-#{$age} {
      background-color: map-get($colors, background);
      color: map-get($colors, color);
    }
  }
}
</style>
