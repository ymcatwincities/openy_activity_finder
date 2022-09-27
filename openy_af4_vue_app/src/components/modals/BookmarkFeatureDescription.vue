<template>
  <div class="bookmark-feature-description">
    <div class="top">
      <span class="bookmark-icon">
        <font-awesome-icon icon="bookmark" />
      </span>
      <p class="title">{{ 'Now you can bookmark activities!' | t }}</p>
      <p>
        {{
          'Build up a list of activities, manage the items within it, and register when ready.' | t
        }}
      </p>
    </div>
    <div class="bottom">
      <p>
        {{ "When you find an activity you're interested in, just click the bookmark icon." | t }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BookmarkFeatureDescription'
}
</script>

<style lang="scss">
.bookmark-feature-description {
  text-align: center;
  color: $af-black;
  font-size: 12px;
  line-height: 18px;

  .top {
    padding: 0 20px 10px 20px;
  }

  .bottom {
    padding: 20px 20px 10px 20px;
    background-color: $af-light-gray;
    font-style: italic;
  }

  .bookmark-icon {
    line-height: 50px;
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 2px solid $af-blue;
    border-radius: 5px;
    margin-bottom: 20px;

    .fa,
    .svg-inline--fa {
      color: $af-blue;
      font-size: 18px;
      line-height: 50px;
    }
  }

  .title {
    font-size: 18px;
    line-height: 24px;
    font-weight: bold;
  }
}
</style>
