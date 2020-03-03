<template>
  <div class="loading-component text-center">
    <svg class="spinner" viewBox="0 0 50 50" data-size="normal" data-flow="centered">
      <circle
        class="path"
        cx="25"
        cy="25"
        r="20"
        fill="none"
        stroke-width="5"
        stroke="#93bfec"
      ></circle>
    </svg>
  </div>
</template>

<script>
export default {
  name: 'Loading'
}
</script>

<style lang="scss">
.loading-component {
  height: 200px;
}
</style>
