<template>
  <div>
    <span class="font-weight-bold">{{ day['day'] }}</span>
    <div class="day-events font-weight-bold">
      <span v-for="(event, key) in day.events" :key="key">
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
      var newWindow = window.open(WINDOW_OPEN_URL + id + '/' + this.timeFormat + '/' + this.selectedRoomId, 'Event Details','left=200,top=100,width=1100,height=800,toolbar=1,resizable=0')
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
