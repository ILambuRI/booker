<template>
  <event-form :user="user"
              :timeInterval="timeInterval"
              @eventFormUpdate="updateEvent"
              @eventFormDelete="deleteEvent"
              :eventDetail="eventDetail"
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
    parseDateDeleted() {
      let date = null
      for (let i=0, len=this.eventDeleted.length; i<len; i++) {
        date = new Date(this.eventDeleted[i].start * 1000)
        if (this.timeInterval == 12) {
          this.eventDeleted[i].start = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
        }
        else {
          this.eventDeleted[i].start = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
        }

        date = new Date(this.eventDeleted[i].end * 1000)
        if (this.timeInterval == 12) {
          this.eventDeleted[i].end = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
        }
        else {
          this.eventDeleted[i].end = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
        }

        this.eventDeleted[i].created = date.toLocaleDateString()
      }
    },

    updateEvent(updateParams) {
      // console.log(updateParams)
      // // console.log(this.user.hash)
      // console.log(this.$route.params.id)
      // console.log(Object.assign({}, updateParams, {hash: this.user.hash, eventId: this.$route.params.id}))

      var xhr = new XMLHttpRequest();

      xhr.open('PUT', this.URL + 'user/events/', false);
      xhr.send('hash=' + this.user.hash + '&userId=' + updateParams.userId + '&eventId=' + this.$route.params.id + '&startHour=' + updateParams.startHour + '&startMinutes=' + updateParams.startMinutes + '&endHour=' + updateParams.endHour + '&endMinutes=' + updateParams.endMinutes + '&desc=' + updateParams.desc + '&reacurring=' + updateParams.reacurring);

      // 4. Если код ответа сервера не 200, то это ошибка
      if (xhr.status != 200) {
        // обработать ошибку
        alert( xhr.status + ': ' + xhr.statusText ); // пример вывода: 404: Not Found
      } else {
        // вывести результат
        alert( xhr.responseText ); // responseText -- текст ответа.
      }

      // fetch(this.URL + 'user/events/', {
      //   method: 'PUT',
      //   headers: {
      //     'Accept': 'application/json'
      //     // "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
      //   },  
      //   body: 'hash=' + this.user.hash + '&userId=' + updateParams.userId + '&eventId=' + this.$route.params.id + '&startHour=' + updateParams.startHour + '&startMinutes=' + updateParams.startMinutes + '&endHour=' + updateParams.endHour + '&endMinutes=' + updateParams.endMinutes + '&desc=' + updateParams.desc + '&reacurring=' + "'" + updateParams.reacurring + "'"
      // })
      // .then(this.status)
      // .then(this.json)
      // .then((data) => {
      //   if (data.server.status == 200) {
      //     // this.showModal = true
      //     // this.$emit('userChanged')
      //   }
      //   else {
      //     let error = 'Error in saveUser()'+
      //                 '\nStatus: ' + data.server.status +
      //                 '\nError code: ' + data.server.code +
      //                 '\nInfo: ' + data.server.information
      //     alert(error)
      //   }
      // })
      

      // fetch(this.URL + 'user/events/', {
      //   method: 'PUT',
      //   headers: {
      //     "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
      //   },
      //   body: 'hash=' + this.user.hash
              
      //   body: 'hash=' + this.user.hash +
      //         '&userId=' + updateParams.userId +
      //         '&eventId=' + this.$route.params.id +
      //         '&startHour=' + updateParams.startHour +
      //         '&startMinutes=' + updateParams.startMinutes +
      //         '&endHour=' + updateParams.endHour +
      //         '&endMinutes=' + updateParams.endMinutes +
      //         '&desc=' + updateParams.desc +
      //         '&reacurring=' + updateParams.reacurring
      // })
      // .then(this.status)
      // .then(this.json)
      // .then((data) => {
      //   if (data.server.status == 200) {
      //     console.log(data.data)
      //   }
      //   else {
      //     let error = 'Error in updateEvent()'+
      //                 '\nStatus: ' + data.server.status +
      //                 '\nError code: ' + data.server.code +
      //                 '\nInfo: ' + data.server.information
      //     alert(error)
      //   }
      // })
      
    },

    deleteEvent(eventParams) {
      fetch(this.URL + 'user/events/' + this.user.hash + '/' + eventParams.id + '/' + eventParams.recurring, {method: 'DELETE'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.eventDeleted = data.data
          this.parseDateDeleted()
          // console.log(this.eventDeleted)
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
