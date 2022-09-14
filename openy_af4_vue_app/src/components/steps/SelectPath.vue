<template>
  <div class="select-path-component">
    <div class="banner" :style="{ background: 'url(' + image + ') center center/cover no-repeat' }">
      <div class="separator"></div>
      <div class="shadow">
        <h1 class="text-center text-uppercase" :class="{ 'visually-hidden': !labelDisplay }">
          {{ label }}
        </h1>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-12 col-xs-12">
          <div class="description text-center">
            <p>{{ 'Find activities that fit your schedule(s)!' | t }}</p>
            <p>
              <strong>{{ 'How would you like to start?' | t }}</strong>
            </p>
          </div>
        </div>
      </div>
      <div class="row paths">
        <div v-for="path in paths" :key="path.id" :class="pathClasses" @click="onClick(path.id)">
          <button type="button" class="path btn">
            <i class="fa" :class="path.icon" aria-hidden="true"></i>
            <span class="text-uppercase">{{ path.name }}</span>
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-12 col-xs-12">
          <div class="search">
            <slot name="search" />
          </div>
        </div>
        <div class="col-12 col-xs-12">
          <div class="homebranch text-center">
            <slot name="home-branch" />
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
    label: {
      type: String,
      required: true
    },
    labelDisplay: {
      type: Boolean,
      required: true
    },
    paths: {
      type: Array,
      required: true
    },
    backgroundImage: {
      type: Object,
      required: true
    },
    bsVersion: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      image: this.backgroundImage.mobile
    }
  },
  computed: {
    pathClasses() {
      return this.bsVersion === 4 ? 'col-6 col-md-3' : 'col-xs-6 col-sm-3'
    }
  },
  mounted() {
    this.chooseImage()
    window.addEventListener('resize', this.chooseImage)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.chooseImage)
  },
  methods: {
    chooseImage() {
      const width = window.document.documentElement.clientWidth
      if (width >= 992) {
        this.image = this.backgroundImage.desktop
      } else {
        this.image = this.backgroundImage.mobile
      }
    },
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
  .banner {
    height: 240px;
    color: $white;

    @include media-breakpoint-up('sm') {
      height: 360px;
    }

    @include media-breakpoint-up('lg') {
      height: 400px;
    }

    .separator {
      @include media-breakpoint-up('lg') {
        height: 5px;
        background-color: $af-black;
        opacity: 0.6;
      }
    }

    .shadow {
      background-color: rgba(35, 31, 32, 0.4);
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-around;
    }

    h1 {
      font-size: 48px;
      line-height: 60px;
      margin: 0;
    }
  }

  .row {
    margin-left: -5px;
    margin-right: -5px;

    @include media-breakpoint-up('sm') {
      margin-left: -10px;
      margin-right: -10px;
    }

    @include media-breakpoint-up('lg') {
      margin-left: -20px;
      margin-right: -20px;
    }
  }

  [class*='col-'] {
    padding-left: 5px;
    padding-right: 5px;

    @include media-breakpoint-up('sm') {
      padding-left: 10px;
      padding-right: 10px;
    }

    @include media-breakpoint-up('lg') {
      padding-left: 20px;
      padding-right: 20px;
    }
  }

  .description {
    margin: 20px 0;

    @include media-breakpoint-up('lg') {
      margin: 40px 0;
    }
  }

  .paths {
    margin-bottom: 5px;

    @include media-breakpoint-up('sm') {
      margin-bottom: 15px;
    }

    @include media-breakpoint-up('lg') {
      margin-bottom: 40px;
    }
  }

  .path {
    text-align: left;
    font-size: 18px;
    line-height: 50px;
    font-weight: bold;
    background-color: $af-blue;
    color: $white;
    margin-bottom: 5px;
    border-radius: 5px;
    width: 100%;
    padding: 0;

    &:hover {
      background-color: $af-blue;
    }

    @include media-breakpoint-up('lg') {
      text-align: center;
      font-size: 24px;
      line-height: 36px;
      padding-bottom: 20px;
      margin-bottom: 0;
    }

    svg {
      width: 50px;
      text-align: center;
      font-size: 20px;
      @include media-breakpoint-up('lg') {
        display: block;
        margin: 30px auto;
        font-size: 40px;
      }
    }

    .fa {
      width: 50px;
      text-align: center;
      font-size: 20px;

      @include media-breakpoint-up('lg') {
        display: block;
        margin: 0 auto;
        font-size: 40px;
        padding: 30px 0;
      }
    }
  }

  .search,
  .homebranch {
    max-width: 340px;
    margin: 0 auto;
  }
  .homebranch {
    margin: 10px auto;
  }
}
</style>
