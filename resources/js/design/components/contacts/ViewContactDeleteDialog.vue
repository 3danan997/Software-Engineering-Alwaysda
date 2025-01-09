<template>
  <v-dialog v-model="views_payload.dialog" persistent scrollable width="500px">
    <v-card class="card">
      <v-card-title>
        <p class="font-weight-bold text-font-subtitle text-color-level-0 mb-0">{{ views_payload.title }}</p>
      </v-card-title>
      <v-card-text>
          <p class="font-weight-medium text-font-body text-color-level-1 mt-1 mb-0">{{
              views_payload.description }}</p>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn class="font-weight-bold text-font-caption" text @click="onButtonClicked('negative')">Abbrechen
        </v-btn>
        <v-btn class="font-weight-bold text-font-caption" color="accent" text @click="onButtonClicked('positive')">Löschen
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
<script>
export default {
  data() {
    return {
      views_payload: {
        dialog: false,
        id: null,
        title: null,
        description: null,
      },
    }
  },
  created() {
    let app = this
    app.$eventBus.$on('deleteContact', (payload) => app.onPayloadReceive(payload))
  },
  beforeDestroy() {
    this.$eventBus.$off('deleteContact')
  },
  methods: {
    onPayloadReceive(payload) {
      let app = this
      app.views_payload.id = payload.id
      app.views_payload.title = payload.title
      app.views_payload.description = payload.description
      app.views_payload.dialog = true
    },
    onButtonClicked(action) {
      let app = this

      if(action === "positive") {
        app.$eventBus.$emit("contactDelete", {id: app.views_payload.id})
      }

      app.views_payload.dialog = false
    },
  }
}
</script>
<style lang="scss" scoped>


</style>