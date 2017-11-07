<template>
  <event-form :user="user"
              :timeInterval="timeInterval"
              @eventFormUpdate="updateEvent"
              @eventFormDelete="deleteEvent"
              :eventDetail="eventDetail"
              :deletedSuccess="deletedSuccess">
  </event-form>
</template>

<script>
import EventForm from './sillyComponents/EventForm'

export default {
  name: 'EventDetail',
  data () {
    return {
      URL: URL,
      deletedSuccess: false,
      eventDetail: {
        id: '',
        user_id: '',
        user_name: '',
        created: '',
        desc: '',
        end: '',
        start: '',
        event_id: '',
        room_id: ''
      },

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
  },

  methods: {
    updateEvent() {
      alert('updateEvent')
    },

    deleteEvent(eventParams) {
      fetch(this.URL + 'user/events/' + eventParams.id + '/' + eventParams.recurring, {method: 'DELETE'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          // this.$router.push('/')
          // alert('DELETED')
          this.deletedSuccess = true
        }
        else {
          let error = 'Error in deleteEvent()'+
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
          // console.log(this.eventDetail)
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
