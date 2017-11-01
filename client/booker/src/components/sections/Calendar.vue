<template>
  <div class="container">
      
      <table class="table table-bordered">
        <thead>
          <tr>
            <th v-on:click="getPrevMonth" scope="col" colspan="2"><<</th>
            <th scope="col" colspan="3">
              {{ year }} - {{ month }}
              <button v-on:click="changeFormat" class="float-right btn btn-outline-dark">
                {{ format }}
              </button>
            </th>
            <th v-on:click="getNextMonth" scope="col" colspan="2">>></th>
          </tr>
        </thead>
        <thead>
          <tr v-if="format == 'RU'">
            <th v-for="(name, key) in ruWeek" :key="key" scope="col"> {{ name }} </th>
          </tr>
          <tr v-if="format == 'ENG'">
            <th v-for="(name, key) in engWeek" :key="key" scope="col"> {{ name }} </th>
          </tr>
        </thead>
          <month v-bind:monthArr="monthArr"></month>
      </table>

  </div>
</template>

<script>
import Month from './Month'

export default {
  name: 'Calendar',
  data () {
    return {
      month:'',
      monthArr:[],
      year: new Date().getFullYear(),
      monthNum: new Date().getMonth(),
      format:'RU',
      engWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
      ruWeek: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']
    }
  },
  created() {
    this.getMonthArr(this.year, this.monthNum)
  },
  components:{
    Month
  },
  methods: {
    getMonthArr(year, month, format = 'RU') {
      let timeNow = Math.floor( new Date().getTime()  / 1000 )
      let timeFirstDay = Math.floor( new Date(year, month, 1).getTime()  / 1000 )
      let timeLastDay =  Math.floor( new Date(year, month + 1, 0).getTime()  / 1000 )

      // let D = new Date(year, month, Dlast)
      // let DNlast = new Date(D.getFullYear(), D.getMonth(), Dlast).getDay()+1
      // let DNfirst = new Date(D.getFullYear(), D.getMonth(), 1).getDay()+1
      // console.log( timeNow )
      // console.log( Math.floor(date.getTime() / 1000) )
      
      this.monthArr = []
      let monthName = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]
      // let monthArr = ["Январь", "Февраль", "Март", "Апрель", "Май", "Июнь", "Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь"]

      var date = new Date(year, month)
      this.month = monthName[month]
      this.monthArr[0] = []

      /* Fill empty days */
      for (var i = 0; i < this.getNumDay(date, format); i++) {
          this.monthArr[0].push( {} )
      }

      /* Weeks count */
      var week = 0
      
      while (date.getMonth() == month) {
        this.monthArr[week].push( {data: date.getDate()} )
        
        if (this.getNumDay(date, format) == 6) {
          week++
          this.monthArr[week] = []
        }

        date.setDate(date.getDate() + 1)
      }
    },

    /* Get the day of the week (0 - 6) */
    getNumDay(date, format) {
      var day = date.getDay()

      if (format == 'RU') {
        if (day == 0) {
          day = 7
        }

        return day - 1
      }

      return day
    },

    getNextMonth() {
      this.monthNum++

      if (this.monthNum > 11) {
        this.monthNum = 0
        this.year++
      }

      this.getMonthArr(this.year, this.monthNum)
    },

    getPrevMonth() {
      this.monthNum--

      if (this.monthNum < 0) {
        this.monthNum = 11
        this.year--
      }

      this.getMonthArr(this.year, this.monthNum)
    },

    changeFormat() {
      if (this.format == 'RU') {
        this.format = 'ENG'
        this.getMonthArr(this.year, this.monthNum, 'ENG')
      }
      else if (this.format == 'ENG') {
        this.format = 'RU'
        this.getMonthArr(this.year, this.monthNum, 'RU')
      }
    }
  }
}
</script>