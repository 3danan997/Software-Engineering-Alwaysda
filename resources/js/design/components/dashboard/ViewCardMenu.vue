<template>
  <v-card class="card card-menu" v-if="items && items.length > 0">
    <v-row class="ma-0">
      <v-col cols="12" v-for="(item, index) in items" class="align-self-center" :class="{'ma-0 pa-0': item.divider, 'clickable enable-hover': !item.divider}" @click="onItemClicked(item)" :key="index">
        <p v-if="!item.divider" class="font-weight-medium text-font-caption card-menu-item-text mb-0 py-1">{{item.title}}</p>
        <v-divider v-else class="divider"></v-divider>
      </v-col>
    </v-row>
  </v-card>
</template>
<script>
export default {
  props: ['items', 'bus', 'payload', 'menu', 'index'],
  methods: {
    onItemClicked(item){
      if(item.divider) return true

      let app = this
      let payload = app.$lodash.cloneDeep(app.$props.payload)
      payload.index = app.$props.index
      payload.menu_item_clicked = item
      app.$eventBus.$emit(app.$props.bus, payload)
    },
  }
}
</script>
<style lang="scss">

</style>