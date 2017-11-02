<template>
  <div class="row col-md-12 justify-content-md-center">
        <div class="btn-group btn-group-lg mb-3" role="group" aria-label="Basic example">
          <router-link v-for="(room, key) in rooms" :key="key" :to="'/room/' + room.id">
            <button v-if="$route.params.id == room.id" type="button" class="btn btn-secondary mr-2">
              <strong>
                {{ room.name }}
              </strong>
            </button>
            
            <button v-if="$route.params.id != room.id" type="button" class="btn btn-secondary mr-2">
              {{ room.name }}
            </button>
          </router-link>
        </div>
    <div class="row col-md-12 justify-content-md-center">
      <div class="col-md-11">
        
        <calendar></calendar>

      </div>
      <div class="col-md-1">
        <div class="btn-group mb-5" role="group" aria-label="Basic example">
          <router-link :to="'/book/' + $route.params.id">
            <button type="button" class="btn btn-secondary">Book It!</button>
          </router-link>
        </div>
        <div v-if="user.admin == 1" class="btn-group" role="group" aria-label="Basic example">
          <router-link :to="'/book/' + $route.params.id">
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
      roomId : this.$route.params.id
    }
  },

  props: ["user", "rooms"],

  watch: {
  },

  components: {
    'calendar' : Calendar
  },

  computed: {
  },

  created() {
    if (!this.user.access) {
      this.$router.push('/')
    }
  },

  methods: {
    saveAuthor() {
      fetch(this.URL + 'admin/authors/', {
        method: 'POST',
        headers: {  
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
        },  
        body: 'hash=' + this.user.hash + '&name=' + this.name
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.adminEvent('Authors')
        }
        else {
          let error = 'Error in saveAuthor()'+
                      '\nStatus: ' + data.server.status +
                      '\nError code: ' + data.server.code +
                      '\nInfo: ' + data.server.information
          alert(error)
        }
      })

      this.name = ''
    },

    adminEvent(type) {
      this.$emit('adminEvent', type)
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