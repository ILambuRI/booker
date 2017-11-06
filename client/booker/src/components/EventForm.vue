<template>
  <event-form :user="user" :timeInterval="timeInterval" @eventFormSave="saveEvent"></event-form>
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
      }
    },

    methods: {
      saveEvent(dataEvent) {
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
            console.log(data)
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