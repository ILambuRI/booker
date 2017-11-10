<template>

  <event-form :user="user"
              :timeInterval="timeInterval"
              @eventFormUpdate="updateEvent"
              @eventFormDelete="deleteEvent"
              @clearPastResult="clearPastResult"
              :eventDetail="eventDetail"
              :eventUpdated="eventUpdated"
              :eventDeleted="eventDeleted">
  </event-form>

</template>

<script>
import EventForm from './sillyComponents/EventForm'

export default {
  name: 'EventDetail',
  data () {
    return {
      URL: URL,
      eventDeleted: [],
      eventUpdated: [],
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

  created() {
    this.getEventDetail()
  },

  methods: {
    clearPastResult() {
      this.eventDeleted = []
      this.eventUpdated = []
    },

    parseDate(eventData) {
      let date = null
      for (let i=0, len=eventData.length; i<len; i++) {
        date = new Date(eventData[i].start * 1000)
        if (this.timeInterval == 12) {
          eventData[i].start = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
        }
        else {
          eventData[i].start = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
        }

        date = new Date(eventData[i].end * 1000)
        if (this.timeInterval == 12) {
          eventData[i].end = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
        }
        else {
          eventData[i].end = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
        }
        
        eventData[i].created = date.toLocaleDateString()

        if (eventData[i].success == true) {
          eventData[i].success = 'Updated'
        }
        else {
          eventData[i].success = 'Not updated'
        }
      }

      return eventData
    },

    updateEvent(updateParams) {
      // var xhr = new XMLHttpRequest();
      // xhr.open('PUT', this.URL + 'user/events/', false);
      // xhr.send('hash=' + this.user.hash + '&roomId=' + this.$route.params.selectedRoomId + '&userId=' + updateParams.userId + '&eventId=' + this.$route.params.id + '&startHour=' + updateParams.startHour + '&startMinutes=' + updateParams.startMinutes + '&endHour=' + updateParams.endHour + '&endMinutes=' + updateParams.endMinutes + '&desc=' + updateParams.desc + '&reacurring=' + updateParams.reacurring);
      // if (xhr.status != 200) {
      //   alert( xhr.status + ': ' + xhr.statusText );
      // } else {
      //   console.log(JSON.parse(xhr.responseText).data)
      //   this.eventUpdated = this.parseDate(JSON.parse(xhr.responseText).data)
      //   console.log(this.eventUpdated)
      // }

      fetch(this.URL + 'user/events/', {
        method: 'PUT',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },
        body: 'hash=' + this.user.hash +
              '&userId=' + updateParams.userId +
              '&eventId=' + this.$route.params.id +
              '&roomId=' + this.$route.params.selectedRoomId +
              '&startHour=' + updateParams.startHour +
              '&startMinutes=' + updateParams.startMinutes +
              '&endHour=' + updateParams.endHour +
              '&endMinutes=' + updateParams.endMinutes +
              '&desc=' + updateParams.desc +
              '&reacurring=' + updateParams.reacurring
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.eventUpdated = this.parseDate(data.data)
        }
        // else {
        //   let error = 'Error in updateEvent()'+
        //               '\nStatus: ' + data.server.status +
        //               '\nError code: ' + data.server.code +
        //               '\nInfo: ' + data.server.information
        //   alert(error)
        // }
      })
      
    },

    deleteEvent(eventParams) {
      fetch(this.URL + 'user/events/' + this.user.hash + '/' + eventParams.id + '/' + eventParams.recurring, {method: 'DELETE'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.eventDeleted = this.parseDate(data.data)
        }
        // else {
        //   let error = 'Error in deleteEvent()'+
        //               '\nStatus: ' + data.server.status +
        //               '\nError code: ' + data.server.code +
        //               '\nInfo: ' + data.server.information
        //   alert(error)
        // }
      })
    },

    getEventDetail() {
      fetch(this.URL + 'user/events/' + this.$route.params.id, {method: 'GET'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          Object.assign(this.eventDetail, data.data[0])
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
