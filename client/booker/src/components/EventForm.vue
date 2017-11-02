<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="row col-md-5">
      <div class="col-md-12">
        <h3> Boardroom booker: {{ getRoomName }} </h3>
        <div v-if="errorMsg" class="alert alert-danger">
          <strong>{{errorMsg}}</strong>
          <button @click="errorMsg = ''" type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div v-if="user.admin == 1" class="form-group">
            <label>1. Booked for:</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-5" role="group">
                <select class="form-control" v-model="selectedUserId">
                  <option v-for="(user, key) in adminData.allUsers" :key="key" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <div v-if="user.admin == 0" class="form-group">
            <label>1. Booked for: <strong> {{ getUserName }} </strong></label>
          </div>
          
          <!-- Dropdowns date -->
          <div class="form-group">
            <label>2. I wood like to book this meeting:</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-7" role="group">
                <select @change="createDateOptions()" class="form-control mr-2" v-model="month">
                  <option v-for="(num, key) in monthCount" :key="key" :value="key">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="day">
                  <option v-for="(num, key) in daysCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="year">
                  <option v-for="(num, key) in yearsCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Dropdowns start time -->
          <div class="form-group">
            <label>3. Specify what the time start and end of the meeting (This will be what people see on the calendar.)</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-3" role="group">
                <select class="form-control mr-2" v-model="eventTime.startHour">
                  <option v-for="(num, key) in hoursCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="eventTime.startMinutes">
                  <option :value="0">00</option>
                  <option :value="30">30</option>
                </select>

                <!-- <select class="form-control mr-2" v-model="year">
                  <option v-for="(num, key) in yearsCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select> -->
              </div>
            </div>
          </div>

          <!-- Dropdowns end time -->
          <div class="form-group">
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group  col-md-3" role="group">
                <select class="form-control mr-2" v-model="eventTime.endHour">
                  <option v-for="(num, key) in hoursCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="eventTime.endMinutes">
                  <option :value="0">00</option>
                  <option :value="30">30</option>
                </select>

                <!-- <select class="form-control mr-2" v-model="year">
                  <option v-for="(num, key) in yearsCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select> -->
              </div>
            </div>
          </div>

          <!-- Desc area -->
          <div class="form-group">
            <label>4. Enter the specifics for the meeting. (This will be what people see when they click on an event link.)</label>
            <textarea class="form-control" rows="4" v-model="description"></textarea>
            <small v-if="!validDescription" class="form-text text-muted">Enter a description of the event (3-100 chars)</small>
            <small v-if="validDescription" class="form-text text-success"><strong>Сorrectly!</strong></small>
          </div>

          <!-- Reacurring event -->
          <div class="form-group">
            <label>5. Is this going to be a reacurring event?</label>
            <div class="custom-controls-stacked">
              <label class="custom-control custom-radio">
                <input @click="recurringType = recurringDuration = ''" type="radio" class="custom-control-input" :value="false" v-model="recurringEvent">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">No</span>
              </label>
              <label class="custom-control custom-radio">
                <input @click="recurringType = recurringDuration = ''" type="radio" class="custom-control-input" :value="true" v-model="recurringEvent">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Yes</span>
              </label>
            </div>
          </div>

          <!-- Reacurring options -->
          <div v-if="recurringEvent" class="form-group ">
            <label>6. If it is recurring, specify weekly, be-weekly, or monthly.</label>
            <div class="custom-controls-stacked">
              <label class="custom-control custom-radio">
                <input @click="recurringDuration = ''" type="radio" class="custom-control-input" value="Weekly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Weekly</span>
              </label>
              <label class="custom-control custom-radio">
                <input @click="recurringDuration = ''" type="radio" class="custom-control-input" value="Bi-weekly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Bi-weekly</span>
              </label>
              <label class="custom-control custom-radio">
                <input @click="recurringDuration = ''" type="radio" class="custom-control-input" value="Monthly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Monthly</span>
              </label>
            </div>
            <label class="label-input">
              If weekly or bi-weekly, specify the number of weeks for it keep recurring.
            </label>

            <div v-if="recurringType == 'Weekly'" class="row col-md-2">
            <label>Duration:</label>
              <input type="text" class="form-control" v-model.trim="recurringDuration">
              <small v-if="!validDuration" class="form-text text-muted">Enter 1 - 4</small>
              <small v-if="validDuration" class="form-text text-success"><strong>Сorrectly!</strong></small>
            </div>

            <div v-if="recurringType == 'Bi-weekly'" class="row col-md-2">
            <label>Duration:</label>
              <input type="text" class="form-control" v-model.trim="recurringDuration">
              <small v-if="!validDuration" class="form-text text-muted">Enter 1 or 2</small>
              <small v-if="validDuration" class="form-text text-success"><strong>Сorrectly!</strong></small>
            </div>
          </div>

        </form>
          <button @click="saveEvent()" class="float-left btn btn-dark" :disabled="!validBtnAccess">
            Submit
          </button>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props: ["user", "rooms", "adminData"],
  
    data() {
      return {
        errorMsg: '',
        selectedUserId: '',
        day: '',
        month: '',
        year: '',
        description: '',

        eventTime: {
          startHour: 8,
          startMinutes: 0,
          endHour: 20,
          endMinutes: 0,
        },

        recurringEvent: false,
        recurringType: '',
        recurringDuration: '',

        daysCount: [],
        yearsCount: [],
        monthCount: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'],
        hoursCount: [],
      }
    },

    created() {
      if (!this.user.access) {
        this.$router.push('/')
      }
      else {
        this.selectedUserId = this.user.id
      }

      let data = new Date()
      this.day = data.getDate()
      this.month = data.getMonth()
      this.year = data.getFullYear()

      for(var i = 0; i<3; i++)
          this.yearsCount.push(+this.year + i)

      this.createDateOptions()

      for(var i = 8; i<=20; i++)
        this.hoursCount.push(i)
    },

    watch: {
    },

    computed: {
      validBtnAccess() {
        if (this.validDescription && this.validDuration) {
          return true
        }

        return false
      },

      validDescription() {
        if (this.description.length >= 3 && this.description.length <= 100) {
          return true
        }

        return false
      },

      validDuration() {
        if (this.recurringEvent) {
          if (this.recurringType == 'Weekly' && (this.recurringDuration >= 1 && this.recurringDuration <= 4) )
            return true

          if (this.recurringType == 'Bi-weekly' && (this.recurringDuration == 1 || this.recurringDuration == 2) )
            return true

          if (this.recurringType == 'Monthly')
            return true
        }
        else {
          return true
        }

        return false
      },

      getUserName() {
        if (this.user.name)
          return this.user.name
        
        return 'No name'
      },

      getRoomName() {
        let currentRoom = this.rooms.filter((el) => {
          if (el.id == this.$route.params.id)
              return true
        })

        if (currentRoom.length > 0)
          return currentRoom[0].name
        
        return 'No room name'
      }
    },

    methods: {
      saveEvent() {
        this.errorMsg = ''
        let dataEvent = {}

        function toTimestamp(strDate) {
          let datum = Date.parse(strDate)
          return datum / 1000
        }

        let strDateStart = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +this.eventTime.startHour+ ':' +this.eventTime.startMinutes+ ':00'
        let strDateEnd = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +this.eventTime.endHour+ ':' +this.eventTime.endMinutes+ ':00'

        dataEvent.timeStart = Math.floor( toTimestamp(strDateStart) )
        dataEvent.timeEnd = Math.floor( toTimestamp(strDateEnd) )
        dataEvent.timeCreate = Math.floor( new Date().getTime()  / 1000 )
        dataEvent.desc = this.description
        dataEvent.type = this.recurringType
        dataEvent.duration = this.recurringDuration
        dataEvent.roomId = this.$route.params.id
        dataEvent.userId = this.selectedUserId

        if ( !(1800 + +dataEvent.timeStart <= dataEvent.timeEnd) ) {
          this.errorMsg = 'Enter the correct amount of time!'
        }
        else if (dataEvent.timeStart < dataEvent.timeCreate) {
          this.errorMsg = 'Can not reserve on the past date or time!'
        }
        else {
          console.log(dataEvent)
        }

        // let timeEnd =  Math.floor( new Date(year, month + 1, 0).getTime()  / 1000 )
        // console.log( new Date(dataEvent.timeStart * 1000).getHours() )
        // console.log( new Date(dataEvent.timeStart * 1000).getMinutes() )
        // alert(toTimestamp('02/13/2009 23:31:30'));

      },

      createDateOptions() {
        let cntMonthDays = new Date(this.year, this.month + 1, 0).getDate()

        if (this.day > cntMonthDays)
            this.day = cntMonthDays

        this.daysCount = []
        for(var i=1; i<=cntMonthDays; i++)
            this.daysCount.push(i)
      },
    }
  }
</script>