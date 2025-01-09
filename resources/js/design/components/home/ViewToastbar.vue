<template>
  <v-row @mouseover="keepToastbar()" @mouseleave="resumeTimeoutHide()" class="toastbar-view pa-4" @click.self="hideToastbar()" :class="{'toastbar-view-visible': states.shown}" :style="{'background-color': item_payload.items[item_payload.items_active] ? item_payload.items[item_payload.items_active].background_color : item_payload.placeholder.background_color}">
    <v-col class="flex-grow-1">
      <p class="font-weight-medium body-1 white--text mb-0 toastbar-view-text">{{item_payload.items[item_payload.items_active] ? item_payload.items[item_payload.items_active].display_message : item_payload.placeholder.display_message}}</p>
    </v-col>
    <v-col class="flex-grow-0 min-width-fit-content" v-show="item_payload.items.length > 1">
      <v-btn icon small color="white" @click="showPreviousItem()">
        <v-icon>mdi-chevron-left</v-icon>
      </v-btn>
      <span class="font-weight-medium body-1 white--text mb-0">{{item_payload.items_active + 1}} of {{item_payload.items.length}}</span>
      <v-btn icon small color="white" @click="showNextItem()">
        <v-icon>mdi-chevron-right</v-icon>
      </v-btn>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data() {
    return {
      item_payload: {
        items_active: 0,
        items: [],
        placeholder: {
          background_color: "#232323",
          display_message: "",
        }
      },
      states: {
        shown: false,
      },
      instances: {
        show_timeout: null,
        hide_timeout: null
      }
    }
  },
  created() {
    let app = this
    app.$eventBus.$on("dchvt_toast", (payload) => app.showToastbar(payload))
  },
  beforeDestroy() {
    let app = this

    app.$eventBus.$off("dchvt_toast")
  },
  methods: {
    showToastbar(payload) {
      let app = this
      if (payload.force_hide) {
        app.states.shown = false
        return
      }

      if(typeof payload.message === "object") {
        /* es ist ein geparstes Objekt mit Übersetzung */
        payload.message = app.$lang.get(null, payload.message.key, payload.message.params, payload.message.alt)
      }


      let item = {
        message: payload.message,
        display_message: payload.message
      }


      switch (payload.status) {
        case "success":
          item.background_color = "#3aa63e";
          break;
        case "failure":
          item.background_color = "#a63a3a";
          break;
        case "pending":
          item.background_color = "#232323";
          break;
      }

      if (payload.error) {
        item.error = payload.error

        if (payload.error.response && payload.error.response.data && payload.error.response.data.message) {
          item.display_message = payload.error.response.data.message
        } else if (payload.error.data && payload.error.data.message) {
          item.display_message = payload.error.data.message
        } else if (payload.error.response && payload.error.response.statusText) {
          item.display_message = payload.error.response.statusText
        } else if (payload.error.message) {
          item.display_message = payload.error.message
        }
      }

      app.item_payload.items.unshift(item)
      app.item_payload.items_active = 0
      clearTimeout(app.instances.show_timeout);
      app.instances.show_timeout = setTimeout(function() {
        app.states.shown = true
        clearTimeout(app.instances.hide_timeout);
        app.instances.hide_timeout = setTimeout(function() {
          app.hideToastbar()
        }, 3000);
      }, 100);
    },
    keepToastbar() {
      let app = this
      clearTimeout(app.instances.show_timeout);
      clearTimeout(app.instances.hide_timeout);
      app.states.shown = true
    },
    resumeTimeoutHide() {
      let app = this
      clearTimeout(app.instances.show_timeout);
      clearTimeout(app.instances.hide_timeout);
      app.instances.hide_timeout = setTimeout(function() {
        app.hideToastbar()
      }, 3000);
    },
    hideToastbar() {
      let app = this
      clearTimeout(app.instances.hide_timeout);

      if(app.item_payload.items.length > 0){
        app.item_payload.placeholder.background_color = app.item_payload.items[0].background_color
        app.item_payload.placeholder.display_message = app.item_payload.items[0].display_message
      }

      app.states.shown = false
      app.item_payload.items = []
    },
    showPreviousItem() {
      let app = this
      if (app.item_payload.items_active > 0) {
        app.item_payload.items_active--;
      }
    },
    showNextItem() {
      let app = this
      if ((app.item_payload.items_active + 1) < app.item_payload.items.length) {
        app.item_payload.items_active++;
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.toastbar-view {
  position: fixed;
  min-height: 80px;
  width: 50%;
  bottom: -100%;
  left: 25%;
  cursor: pointer;
  transition: all 0.5s ease-in-out;
  z-index: 250;
}

.toastbar-view-visible {
  bottom: 0 !important;
  transition: all 0.5s ease-in-out;
}

.toastbar-view-text {
  -moz-user-select: text;
  -khtml-user-select: text;
  -webkit-user-select: text;
  -ms-user-select: text;
  user-select: text;
  cursor: text;
}
</style>
