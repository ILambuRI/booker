<template>
  <div>
    <div class="row">
        <div class="col-md-4 div-bordered" v-on:click="getPrevMonth"><<</div>
        <div class="col-md-4 div-bordered">
          {{ year }} - {{ month }}
          <button v-on:click="changeFormat" class="float-right btn btn-outline-dark">
            {{ format }}
          </button>
        </div>
        <div class="col-md-4 div-bordered" v-on:click="getNextMonth">>></div>
    </div>
    <div class="row">
      <table class="table table-bordered">
        <thead>
          <tr v-if="format == 'Mo'">
            <th style="width: 14.29%;" v-for="(name, key) in ruWeek" :key="key" scope="col"> {{ name }} </th>
          </tr>
          <tr v-if="format == 'Su'">
            <th style="width: 14.29%;" v-for="(name, key) in engWeek" :key="key" scope="col"> {{ name }} </th>
          </tr>
        </thead>
          <month v-bind:monthArr="monthArr" :timeFormat="timeFormat" :user="user"></month>
      </table>
    </div>
  </div>
</template>

<script>
import Month from './Month'

export default {
  name: 'Calendar',
  data () {
    return {
      URL: URL,
      allEvents: [],
      month:'',
      monthArr:[],
      year: new Date().getFullYear(),
      monthNum: new Date().getMonth(),
      format:'Mo',
      engWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
      ruWeek: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']
    }
  },

  props: ["selectedRoomId", "timeFormat", "user"],
  
  watch: {
    'selectedRoomId'() {
      this.getAllEvents()
    },

    'monthNum'() {
      this.getAllEvents()
    }
  },

  created() {
    if (this.selectedRoomId) {
      this.getAllEvents()
    }
  },

  components:{
    Month
  },

  methods: {
    getMonthArr() {
      let timeNow = Math.floor( new Date().getTime()  / 1000 )
      let timeFirstDay = Math.floor( new Date(this.year, this.monthNum, 1).getTime()  / 1000 )
      let timeLastDay =  Math.floor( new Date(this.year, this.monthNum + 1, 0).getTime()  / 1000 )
      
      // this.getAllEvents(timeFirstDay, timeLastDay)

      // let D = new Date(year, month, Dlast)
      // let DNlast = new Date(D.getFullYear(), D.getMonth(), Dlast).getDay()+1
      // let DNfirst = new Date(D.getFullYear(), D.getMonth(), 1).getDay()+1
      // console.log( timeFirstDay )
      // console.log( timeLastDay )

      /* Group events by day */
      let formatedEvents = {}
      for (let i=0, len=this.allEvents.length; i<len; i++) {
        let day = new Date(this.allEvents[i].start * 1000).getDate()
        if (formatedEvents[day]) {
          formatedEvents[day].push(this.allEvents[i])
        }
        else {
          formatedEvents[day] = [ this.allEvents[i] ]
        }
      }

      this.monthArr = []
      let monthName = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
      // let monthArr = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]

      var date = new Date(this.year, this.monthNum)
      this.month = monthName[this.monthNum]
      this.monthArr[0] = []

      /* Fill empty days */
      for (var i = 0; i < this.getNumDay(date); i++) {
          this.monthArr[0].push( {} )
      }

      /* Weeks count */
      var week = 0
      
      while (date.getMonth() == this.monthNum) {
        let dataForCurrentDay = {
          day: date.getDate(),
          events: formatedEvents[date.getDate()] || []
        }

        this.monthArr[week].push( dataForCurrentDay )
        
        if (this.getNumDay(date) == 6) {
          week++
          this.monthArr[week] = []
        }

        date.setDate(date.getDate() + 1)
      }
    },

    /* Get the day of the week (0 - 6) */
    getNumDay(date) {
      var day = date.getDay()

      if (this.format == 'Mo') {
        if (day == 0) {
          day = 7
        }

        return day - 1
      }

      return day
    },

    getNextMonth() {
      if (this.monthNum + 1 > 11) {
        this.monthNum = 0
        this.year++
      }
      else {
        this.monthNum++
      }
    },

    getPrevMonth() {
      if (this.monthNum - 1 < 0) {
        this.monthNum = 11
        this.year--
      }
      else {
        this.monthNum--
      }
    },

    changeFormat() {
      if (this.format == 'Mo') {
        this.format = 'Su'
      }
      else {
        this.format = 'Mo'
      }

      this.getMonthArr()
    },

    getAllEvents() {
      fetch(this.URL + 'booker/events/' + this.selectedRoomId + '/' + (this.monthNum + 1) + '/' + this.year, {method: 'GET'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.allEvents = data.data
          this.getMonthArr()
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
<style scoped>
.div-bordered {
  border: 1px solid #e9ecef;
  padding: .75rem;
}
</style>
