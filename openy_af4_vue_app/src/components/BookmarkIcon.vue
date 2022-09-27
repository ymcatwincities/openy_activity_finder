<template>
  <div class="bookmark-toggle">
    <a role="button" title="Bookmarked items" @click="onClick">
      <span v-if="counter > 0" class="counter">{{ counter }}</span>
      <font-awesome-icon icon="bookmark" />
    </a>
  </div>
</template>

<script>
export default {
  name: 'BookmarkIcon',
  props: {
    counter: {
      type: Number,
      default: 0
    }
  },
  methods: {
    onClick() {
      this.$emit('click')
    }
  }
}
</script>

<style lang="scss">
.modal-body {
  .bookmark-toggle {
    right: auto;
    margin-left: -52px;
    display: none;

    @include media-breakpoint-up('sm') {
      display: block;
    }
  }
}

.bookmark-toggle {
  line-height: 50px;
  position: fixed;
  top: 50%;
  margin-top: -25px;
  right: 0;
  background-color: $af-blue;
  border-top-left-radius: 5px;
  border-bottom-left-radius: 5px;
  border: 2px solid $white;
  border-right: none;

  a {
    display: block;
    width: 50px;
    height: 50px;
    text-align: center;
  }

  .counter {
    color: $white;
    background-color: $af-violet;
    padding: 0 6px;
    display: inline-block;
    height: 24px;
    position: absolute;
    top: 50%;
    left: 0;
    transform: translate(-50%, -50%);
    border: 2px solid $white;
    border-radius: 2em;
    font-size: 10px;
    line-height: 20px;
    font-weight: bold;
    min-width: 24px;
  }

  .fa,
  .svg-inline--fa {
    color: $white;
    font-size: 18px;
    line-height: 50px;
  }
}
</style>
