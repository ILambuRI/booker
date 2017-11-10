<template>

  <event-form :user="user" :timeInterval="timeInterval" @eventFormSave="saveEvent" :eventSubmited="eventSubmited"></event-form>

</template>
<script>
  import EventForm from './sillyComponents/EventForm'

  export default {
    props: ["user", 'timeInterval'],

    components: {
      EventForm
    },
  
    data() {
      return {
        URL: URL,
        eventSubmited: []
      }
    },

    methods: {
      parseDateSubmited() {
        let date = null
        for (let i=0, len=this.eventSubmited.length; i<len; i++) {
          date = new Date(this.eventSubmited[i].start * 1000)
          if (this.timeInterval == 12) {
            this.eventSubmited[i].start = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
          }
          else {
            this.eventSubmited[i].start = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
          }

          date = new Date(this.eventSubmited[i].end * 1000)
          if (this.timeInterval == 12) {
            this.eventSubmited[i].end = date.toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true })
          }
          else {
            this.eventSubmited[i].end = date.toLocaleString('ru-RU', { hour: 'numeric', minute: 'numeric', hour24: true })
          }

          if (this.eventSubmited[i].success == true) {
            this.eventSubmited[i].success = 'Created'
          }
          else {
            this.eventSubmited[i].success = 'Not Created'
          }

          this.eventSubmited[i].created = date.toLocaleDateString()
        }
      },

      saveEvent(dataEvent) {
        this.eventSubmited = []
        
        fetch(this.URL + 'user/events/', {
          method: 'POST',
          headers: {  
            "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
          },  
          body: 'userId=' + dataEvent.userId + '&roomId=' + dataEvent.roomId + '&desc=' + dataEvent.desc +
                '&timeStart=' + dataEvent.timeStart + '&timeEnd=' + dataEvent.timeEnd + '&timeCreate=' + dataEvent.timeCreate +
                '&type=' + dataEvent.type + '&duration=' + dataEvent.duration
        })
        .then(this.status)
        .then(this.json)
        .then((data) => {
          if (data.server.status == 200) {
            this.eventSubmited = data.data
            this.parseDateSubmited()
          }
          else {
            let error = 'Error in saveUser()'+
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