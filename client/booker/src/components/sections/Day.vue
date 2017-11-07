<template>
  <div>
    <p class="font-weight-bold">{{ day['day'] }}</p>
    <div class="day-events">
      <p v-for="(event, key) in day.events" :key="key">
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
      </p>
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

  props:['day', 'timeFormat', 'user'],

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
      window.open('/#/event/detail/' + id + '/' + this.timeFormat, 'Event Details','left=200,top=100,width=1000,height=800,toolbar=1,resizable=0')
    }
  }
}
</script>
<style scope>
.day-events {
  height: 81px;
  overflow: auto;
}
</style>
