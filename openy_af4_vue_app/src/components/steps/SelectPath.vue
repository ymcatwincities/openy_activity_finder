<template>
  <div class="select-path-component">
    <div class="container">
      <div class="row">
        <div class="col-xs-12">
          <h1 class="text-center text-uppercase">{{ 'Activity Finder' | t }}</h1>
        </div>
        <div class="col-xs-12 col-sm-8 col-sm-offset-2">
          <div class="text-center text-uppercase">
            {{ 'Find programs & classes that fit your schedule(s)' | t }}
          </div>
        </div>
      </div>
      <div class="row">
        <div
          v-for="path in paths"
          :key="path.id"
          class="col-xs-6 col-sm-3"
          @click="onClick(path.id)"
        >
          <div class="path" role="button">
            <div class="text-center">{{ path.name }}</div>
            <div class="text-center text-uppercase">{{ 'Get Started' | t }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SelectPath',
  props: {
    value: {
      type: String,
      required: true
    },
    paths: {
      type: Array,
      required: true
    }
  },
  methods: {
    onClick(id) {
      this.trackEvent('selectPath', 'Start with ' + id)
      this.$emit('input', id)
      this.$emit('nextStep')
    }
  }
}
</script>

<style lang="scss">
.select-path-component {
  .path {
    padding: 40px;

    &:hover {
      background-color: $af-light-gray;
    }
  }
}
</style>
