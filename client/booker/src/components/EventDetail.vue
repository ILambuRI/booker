<template>
  <event-form :user="user" :timeInterval="timeInterval" @eventFormSave="saveEvent" :eventDetail="eventDetail"></event-form>
</template>

<script>
import EventForm from './sillyComponents/EventForm'

export default {
  name: 'EventDetail',
  data () {
    return {
      URL: URL,
      eventDetail: {
        id: '',
        user_id: '',
        created: '',
        desc: '',
        end: '',
        start: '',
        event_id: '',
        room_id: ''
      },
      allUsers: [],
      timeInterval: this.$route.params.timeInterval
    }
  },

  props: ["user"],

  components:{
    EventForm
  },

  computed: {
  },

  created() {
    this.getEventDetail()

    if (this.user.admin == 1) {
      this.getAllUsers()
    }
  },

  methods: {
    saveEvent() {

    },

    getAllUsers() {
      fetch(this.URL + 'admin/users/' + this.user.hash, {method: 'GET'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.allUsers = data.data
        }
        else {
          let error = 'Error in getAllUsers()'+
                      '\nStatus: ' + data.server.status +
                      '\nError code: ' + data.server.code +
                      '\nInfo: ' + data.server.information
          alert(error)
        }
      })
    },

    getEventDetail() {
      fetch(this.URL + 'user/events/' + this.$route.params.id, {method: 'GET'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          Object.assign(this.eventDetail, data.data[0])
          console.log(this.eventDetail)
        }
        else {
          let error = 'Error in getEventDetail()'+
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
