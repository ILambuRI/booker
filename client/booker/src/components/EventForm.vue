<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="row col-md-5">
      <div class="col-md-12">
         <h3> Boardroom booker: {{ getRoomName }} </h3>
        <form>

          <div class="form-group">
            <label>1. Booked for: <strong> {{ getUserName }} </strong></label>
            
            <!-- <input v-model.trim="name" type="text" class="form-control" placeholder="Enter your name">
            <small v-if="name == ''" class="form-text text-muted">Start whith latin, one space between words is allowed (3 - 50 characters) ...</small>
            <small v-if="!validName & name.length > 0" class="form-text text-danger">Not yet correct ...</small>
            <small v-if="validName" class="form-text text-success">Сorrectly!</small> -->
          </div>
          
          <!-- Dropdowns date -->
          <div class="form-group">
            <label>2. I wood like to book this meeting:</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group" role="group">
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
              <div class="btn-group" role="group">
                <select class="form-control mr-2" v-model="eventTime.startHour">
                  <option v-for="(num, key) in hoursCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="eventTime.startMinutes">
                  <option v-for="(num, key) in minutesCount" :key="key" :value="key">
                    {{ num }}
                  </option>
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
              <div class="btn-group" role="group">
                <select class="form-control mr-2" v-model="eventTime.endHour">
                  <option v-for="(num, key) in hoursCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control mr-2" v-model="eventTime.endMinutes">
                  <option v-for="(num, key) in minutesCount" :key="key" :value="key">
                    {{ num }}
                  </option>
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
          </div>

          <!-- Reacurring event -->
          <div class="form-group">
            <label>5. Is this going to be a reacurring event?</label>
            <div class="custom-controls-stacked">
              <label class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" :value="false" v-model="recurringEvent">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">No</span>
              </label>
              <label class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" :value="true" v-model="recurringEvent">
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
                <input type="radio" class="custom-control-input" value="Weekly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Weekly</span>
              </label>
              <label class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" value="Bi-weekly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Bi-weekly</span>
              </label>
              <label class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" value="Monthly" v-model="recurringType">
                <span class="custom-control-indicator"></span>
                <span class="custom-control-description font-weight-bold">Monthly</span>
              </label>
            </div>
              <div class="label-input">If weekly, or bi-weekly, specify the number of weeks for it ti keep recurring. If to keep recurring. If monthly, specify the number of months. (If you choose 'bi-weekly' and put in an odd number of weeks, the computer will round down)</div>
                <label for="inputZip">Duration (max 4 weeks)</label>
              <div class="col-md-2">
                <input type="text" class="form-control" v-model.trim="recurringDuration">
                <small v-if="recurringDuration == ''" class="form-text text-muted">Number 1 - 4</small>
              </div>
          </div>

        </form>
          <button @click="saveEvent()" class="float-left btn btn-dark">
            Submit
          </button>
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    props: ["user", "rooms"],
  
    data() {
      return {
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
        minutesCount: [],
      }
    },

    created() {
      var data = new Date()
      this.day = data.getDate()
      this.month = data.getMonth()
      this.year = data.getFullYear()
      this.createDateOptions()

      for(var i = 8; i<=20; i++)
        this.hoursCount.push(i)

      for(var i = 0; i<=59; i++)
        this.minutesCount.push(i)
    },

    watch: {
    },

    computed: {
      getUserName() {
        if (this.user.name)
          return this.user.name
        
        return false
      },

      getRoomName() {
        let currentRoom = this.rooms.filter((el) => {
          if (el.id == this.$route.params.id)
              return true
        })

        if (currentRoom.length > 0)
          return currentRoom[0].name
        
        return false
      }
    },

    methods: {
      createDateOptions() {
        var cntMonthDays = new Date(this.year, this.month + 1, 0).getDate()

        if (this.day > cntMonthDays)
            this.day = cntMonthDays

        this.daysCount = []
        for(var i=1; i<=cntMonthDays; i++)
            this.daysCount.push(i)

        this.yearsCount = []
        for(var i = 0; i<3; i++)
            this.yearsCount.push(+this.year + i)
      },
    }
  }
</script>