<template>
  <div class="container">
    <div class="row">
      <div class="col-md-2">
        <h2><span class="badge badge-info">{{ selectedRoom.name }}</span></h2>
      </div>
      <div class="col-md-8 text-center">
        <div class="btn-group btn-group-md mb-3" role="group" aria-label="Basic example">
          <button v-for="(room, key) in rooms" :key="key"
                  @click="selectedRoom.id = room.id;
                          selectedRoom.name = room.name"
                  type="button" class="btn btn-secondary mr-2">
            <strong>
              {{ room.name }}
            </strong>
          </button>
        </div>
      </div>
      <div class="col-md-1 text-center">
          <button @click="changeTimeFormat" type="button" class="btn btn-outline-dark">
            {{ timeFormat }}
          </button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-11">
        
        <calendar :selectedRoomId="selectedRoom.id" :timeFormat="timeFormat" :user="user"></calendar>

      </div>
      <div class="col-md-1">
        <div class="btn-group mb-5" role="group" aria-label="Basic example">
          <router-link :to="'/book/' + selectedRoom.id + '/' + selectedRoom.name">
            <button type="button" class="btn btn-secondary">Book It!</button>
          </router-link>
        </div>
        <div v-if="user.admin == 1" class="btn-group" role="group" aria-label="Basic example">
          <router-link to="/admin/list-user">
            <button type="button" class="btn btn-secondary">Employee List</button>
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Calendar from './sections/Calendar'

export default {
  name: 'Room',
  data () {
    return {
      URL: URL,
      rooms: [],
      timeFormat: 24,
      selectedRoom: {
        id: '',
        name: ''
      },
    }
  },

  props: ["user"],

  watch: {
  },

  components: {
    'calendar' : Calendar
  },

  computed: {
  },

  created() {
    this.getRooms()
    this.$emit('timeIntervalChange', this.timeFormat)
  },

  methods: {
    changeTimeFormat() {
      if (this.timeFormat == 24) {
        this.timeFormat = 12
      }
      else {
        this.timeFormat = 24
      }

      this.$emit('timeIntervalChange', this.timeFormat)
    },

    getRooms() {
      fetch(this.URL + 'booker/rooms/', {method: 'GET'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.rooms = data.data
          Object.assign(this.selectedRoom, this.rooms[0])
        }
        else {
          let error = 'Error in getRooms()'+
                      '\nStatus: ' + data.server.status +
                      '\nError code: ' + data.server.code +
                      '\nInfo: ' + data.server.information
          alert(error)
        }
      })
    },

    status(response) {
      if (response.status == 200) {
        return Promise.resolve(response)
      } else {
        console.log('ERROR RESPONSE')
        return Promise.reject( new Error(response.statusText) )
      }
    },

    json(response) {
        return response.json()
    },
  }
}
</script>