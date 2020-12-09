<template>
  <div class="daxko-pager-component text-center">
    <span class="left">
      <a
        class="prev-page"
        :class="{ disabled: value === 1 }"
        role="button"
        :title="'Previous page' | t"
        @click="prevPage"
      >
        <i class="fa fa-chevron-left"></i>
      </a>
    </span>
    <span class="right">
      <a
        class="next-page"
        :class="{ disabled: !daxkoPages[value + 1] }"
        role="button"
        :title="'Next page' | t"
        @click="nextPage"
      >
        <i class="fa fa-chevron-right"></i>
      </a>
    </span>
  </div>
</template>

<script>
export default {
  name: 'DaxkoPager',
  props: {
    value: {
      type: Number,
      required: true
    },
    daxkoPages: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      selectedPage: this.value
    }
  },
  watch: {
    value() {
      this.selectedPage = this.value
    }
  },
  methods: {
    prevPage() {
      if (this.selectedPage === 1) {
        return
      }
      this.trackEvent('pagination', 'Click on previous page', this.selectedPage)
      this.selectedPage--
      this.updateParent()
    },
    nextPage() {
      if (!this.daxkoPages[this.selectedPage + 1]) {
        return
      }
      this.trackEvent('pagination', 'Click on next page', this.selectedPage)
      this.selectedPage++
      this.updateParent()
    },
    updateParent() {
      this.$emit('input', this.selectedPage)
    }
  }
}
</script>

<style lang="scss">
.daxko-pager-component {
  display: flex;
  justify-content: space-between;
  padding: 10px;
  border-top: 1px solid $af-black;
  color: $af-black;

  a {
    padding: 10px;
    display: inline-block;
    color: $af-darker-gray;
    font-size: 16px;
    line-height: 16px;
    height: 35px;

    &.disabled {
      color: $af-lighter-gray;
      cursor: not-allowed;
    }
  }
}
</style>
