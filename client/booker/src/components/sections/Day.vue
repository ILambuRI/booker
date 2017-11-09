<template>
  <div>
    <p class="font-weight-bold">{{ day['day'] }}</p>
    <div class="day-events font-weight-bold">
      <span v-for="(event, key) in day.events" :key="key">
        <!-- <a v-if="user.id == event.user_id || user.admin == 1" :href="'/#/event/detail/' + user.id">
          {{ event.start | formatTime(timeFormat) }} - {{ event.end | formatTime(timeFormat) }}
        </a> -->

        <!-- <a v-if="user.id == event.user_id || user.admin == 1" href="" @click.prevent="newWindow(event.id)">
          {{ event.start | formatTime(timeFormat) }} - {{ event.end | formatTime(timeFormat) }}
        </a> -->
        <!-- <span v-else>
          {{ event.start | formatTime(timeFormat) }} - {{ event.end | formatTime(timeFormat) }}
        </span> -->

        <a href="" @click.prevent="newWindow(event.id)">
          {{ event.start | formatTime(timeFormat) }} - {{ event.end | formatTime(timeFormat) }}
        </a>
        <br>
      </span>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Day',
  data () {
    return {

    }
  },

  props:['day', 'timeFormat', 'user', 'selectedRoomId'],

  filters: {
    formatTime(value, timeFormat) {
      if (timeFormat == 24) {
        let date = new Date(value * 1000)
        return date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
      }
      else {
        let date = new Date(value * 1000)
        return date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
      }
    }
  },

  methods: {
    newWindow(id) {
      // var newWindow = window.open('/#/event/detail/' + id + '/' + this.timeFormat + '/' + this.selectedRoomId, 'Event Details','left=200,top=100,width=1000,height=800,toolbar=1,resizable=0')
      var newWindow = window.open(WINDOW_OPEN_URL + id + '/' + this.timeFormat + '/' + this.selectedRoomId, 'Event Details','left=200,top=100,width=1000,height=800,toolbar=1,resizable=0')
      newWindow.onbeforeunload = () => { this.$emit('windowClosed') }
    }
  }
}
</script>
<style scope>
.day-events {
  height: 81px;
  overflow: auto;
}
.day-events a {
  font-size: x-small;
}
</style>
