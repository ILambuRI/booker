<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="row col-md-5">
      <div class="col-md-12">
        <h3> Boardroom booker: {{ $route.params.room_name }} </h3>
        <form>
          <div v-if="errorInfoMsg" class="alert alert-info">
            <strong>{{errorInfoMsg}}</strong>
          </div>

          <div class="form-group">
            <label>1. Booked for:</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-5" role="group">
                <select class="form-control" v-model="selectedUserId">
                  <option v-for="(user, key) in allUsers" :key="key" :value="user.id">
                    {{ user.name }}
                  </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Dropdowns date -->
          <div class="form-group">
            <label>2. I wood like to book this meeting:</label>
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-12" role="group">
                <select @change="createDateOptions()" class="form-control" v-model="month" :disabled="eventDetail">
                  <option v-for="(num, key) in monthCount" :key="key" :value="key">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control" v-model="day" :disabled="eventDetail">
                  <option v-for="(num, key) in daysCount" :key="key" :value="num">
                    {{ num }}
                  </option>
                </select>

                <select class="form-control" v-model="year" :disabled="eventDetail">
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
              <div class="btn-group col-md-12" role="group">
                <select class="form-control" v-model="eventTime.startHour">
                  <option v-if="timeInterval == 24" v-for="(i, index) in 13" :key="index" :value="i+7">
                    {{ i+7 }}
                  </option>
                  <option v-if="timeInterval == 12" v-for="(val, key) in hoursCount[hoursCount.selectedStart]" :key="key" :value="val">
                    {{ val }}
                  </option>
                </select>

                <select class="form-control" v-model="eventTime.startMinutes">
                  <option :value="0">00</option>
                  <option :value="30">30</option>
                </select>

                <select v-if="timeInterval == 12" class="form-control" v-model="hoursCount.selectedStart">
                  <option value="am"> AM </option>
                  <option value="pm"> PM </option>
                </select>
              </div>
            </div>
          </div>

          <!-- Dropdowns end time -->
          <div class="form-group">
            <div class="btn-toolbar" role="toolbar">
              <div class="btn-group col-md-12" role="group">
                <select class="form-control" v-model="eventTime.endHour">
                  <option v-if="timeInterval == 24" v-for="(i, index) in 13" :key="index" :value="i+7">
                    {{ i+7 }}
                  </option>

                  <option v-if="timeInterval == 12" v-for="(val, key) in hoursCount[hoursCount.selectedEnd]" :key="key" :value="val">
                    {{ val }}
                  </option>
                </select>

                <select class="form-control" v-model="eventTime.endMinutes">
                  <option :value="0">00</option>
                  <option :value="30">30</option>
                </select>

                <select v-if="timeInterval == 12" class="form-control" v-model="hoursCount.selectedEnd">
                  <option value="am"> AM </option>
                  <option value="pm"> PM </option>
                </select>
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
          <div v-if="!eventDetail" class="form-group">
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

          <!-- Update/Delete recurring checkbox -->
          <div v-if="btnDetailAccess && eventDetail && eventDetail.event_id && eventDetail.event_id.length > 0" class="form-check">
            <label class="form-check-label">
              <input type="checkbox" class="form-check-input" v-model="recurringApplyOccurrences">
              Apply to all occurrences ?
            </label>
          </div>

        </form>
        <router-link to="/">
          <button class="float-left btn btn-dark">
            Cancel
          </button>
        </router-link>
        <button v-if="!eventDetail" @click="saveEvent()" class="float-right btn btn-dark" :disabled="!validBtnAccess" data-toggle="modal" data-target="#eventInformation">
          Submit
        </button>
        <button v-if="eventDetail && btnDetailAccess && !errorInfoMsg" @click="errorMsg = ''; $emit('clearPastResult')" class="float-right btn btn-dark" :disabled="!validBtnAccess" data-toggle="modal" data-target="#updateConfirm">
          Update
        </button>
        <button v-if="eventDetail && btnDetailAccess && !errorInfoMsg" @click="errorMsg = ''; $emit('clearPastResult')" class="float-right btn btn-dark mr-5" :disabled="!validBtnAccess" data-toggle="modal" data-target="#deleteConfirm">
          Delete
        </button>
      </div>
    </div>

    <!-- Modal Submit -->
    <div class="modal fade" id="eventInformation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Information about creating events</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div v-if="errorMsg" class="alert alert-danger">
              <strong>{{errorMsg}}</strong>
            </div>

            <div v-if="!errorMsg" v-for="(event, index) in eventSubmited" :key="index" class="alert alert-info">
              <p>
                {{ index + 1 }}. Event from <strong>{{ event.start }}</strong> to <strong>{{ event.end }}</strong> on {{ event.created }} - <strong>{{ event.success }}</strong>
              </p>
              <p>
                <strong>Description: </strong>{{ event.desc }}</strong>
              </p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div v-if="errorMsg" class="alert alert-danger">
            <strong>{{errorMsg}}</strong>
          </div>
          <div v-else class="modal-body">
            Are you sure ?
          </div>

          <div v-if="eventDeleted && eventDeleted[0].start" v-for="(event, index) in eventDeleted" :key="index" class="alert alert-info">
            <p>
              {{ index + 1 }}. Event from <strong>{{ event.start }}</strong> to <strong>{{ event.end }}</strong> on {{ event.created }} - <strong>Deleted</strong>
            </p>
            <p>
              <strong>Description: </strong>{{ event.desc }}</strong>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button v-if="!errorMsg" @click="deleteEvent()" type="button" class="btn btn-primary">Yes</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Update -->
    <div class="modal fade" id="updateConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div v-if="errorMsg" class="alert alert-danger">
            <strong>{{errorMsg}}</strong>
          </div>
          <div v-else class="modal-body">
            Are you sure ?
          </div>

          <div v-if="eventUpdated && eventUpdated[0].start" v-for="(event, index) in eventUpdated" :key="index" class="alert alert-info">
            <p>
              {{ index + 1 }}. Event from <strong>{{ event.start }}</strong> to <strong>{{ event.end }}</strong> on {{ event.created }} - <strong>{{ event.success }}</strong>
            </p>
            <p>
              <strong>Description: </strong>{{ event.desc }}</strong>
            </p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button v-if="!errorMsg" @click="updateEvent()" type="button" class="btn btn-primary">Yes</button>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>
<script>
  export default {
    props: ["user", 'timeInterval', "eventDetail", "eventSubmited", "eventDeleted", "eventUpdated"],
  
    data() {
      return {
        URL: URL,
        allUsers: [],
        errorMsg: '',
        errorInfoMsg: '',

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

        recurringApplyOccurrences: false,
        btnDetailAccess: true,

        daysCount: [],
        yearsCount: [],
        monthCount: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sept','Oct','Nov','Dec'],
        hoursCount: {
          selectedStart: 'am',
          selectedEnd: 'pm',
          am: [8, 9, 10, 11],
          pm: [12, 1, 2, 3, 4, 5, 6, 7, 8],
        },
      }
    },

    watch: {
      'eventDetail': {
        handler() {
          this.selectedUserId = this.eventDetail.user_id
          this.description = this.eventDetail.desc,
          this.parseDate()

          if (this.user.admin == 0) {
            this.allUsers = []
            this.allUsers.push( {name: this.eventDetail.user_name, id: this.eventDetail.user_id} )
            this.selectedUserId = this.eventDetail.user_id

            if (this.eventDetail.user_id != this.user.id) {
              this.btnDetailAccess = false
            }
          }

          this.errorInfoMsg = ''
          let timestampNow = Math.floor( new Date().getTime() / 1000 )
          if (this.eventDetail.start < timestampNow) {
            this.errorInfoMsg = 'You can not delete or update a past event!'
          }
        },

        deep: true
      },
    },

    created() {
      if (!this.user.access) {
        this.$router.push('/')
      }
      else {
        this.selectedUserId = this.user.id
      }

      if (this.user.admin == 1) {
        this.getAllUsers()
      }
      else {
        this.allUsers.push( {name: this.user.name, id: this.user.id} )
      }

      let data = new Date()
      this.day = data.getDate()
      this.month = data.getMonth()
      this.year = data.getFullYear()

      for(var i = 0; i<3; i++)
          this.yearsCount.push(+this.year + i)

      this.createDateOptions()

      if (this.timeInterval == 12) {
        this.eventTime.endHour = 8
      }
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
    },

    methods: {
      parseDate() {
        let date = new Date(this.eventDetail.start * 1000)
        this.day = date.getDate()
        this.month = date.getMonth()
        this.year = date.getFullYear()

        if (this.timeInterval == 12) {
          this.eventTime.startMinutes = date.toLocaleString('en-US', { minute: 'numeric', hour12: true })

          let hourUs = date.toLocaleString('en-US', { hour: 'numeric', hour12: true })
          hourUs = hourUs.split(' ')

          this.eventTime.startHour = +hourUs[0]
          this.hoursCount.selectedStart = hourUs[1].toLowerCase()

          date = new Date(this.eventDetail.end * 1000)

          this.eventTime.endMinutes = date.toLocaleString('en-US', { minute: 'numeric', hour12: true })
          hourUs = date.toLocaleString('en-US', { hour: 'numeric', hour12: true })
          hourUs = hourUs.split(' ')

          this.eventTime.endHour = +hourUs[0]
          this.hoursCount.selectedEnd = hourUs[1].toLowerCase()

        }
        else {
          this.eventTime.startHour = date.getHours()
          this.eventTime.startMinutes = date.getMinutes()

          date = new Date(this.eventDetail.end * 1000)
          this.eventTime.endHour = date.getHours()
          this.eventTime.endMinutes = date.getMinutes()
        }
      },

      saveEvent() {
        this.errorMsg = ''
        let dataEvent = {}

        function toTimestamp(strDate) {
          let datum = Date.parse(strDate)
          return datum / 1000
        }

        let startHour = this.eventTime.startHour
        let endHour = this.eventTime.endHour
        let strDateStart = ''
        let strDateEnd = ''

        if (this.timeInterval == 12) {
          if (this.hoursCount.selectedStart == 'pm' && startHour < 12) {
            startHour = startHour + 12
          }

          if (this.hoursCount.selectedEnd == 'pm' && endHour < 12) {
            endHour = endHour + 12
          }

          strDateStart = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +startHour+ ':' +this.eventTime.startMinutes+ ':00'
          strDateEnd = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +endHour+ ':' +this.eventTime.endMinutes+ ':00'
        }
        else {
          strDateStart = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +this.eventTime.startHour+ ':' +this.eventTime.startMinutes+ ':00'
          strDateEnd = 1 + this.month+ '/' +this.day+ '/' +this.year+ ' ' +this.eventTime.endHour+ ':' +this.eventTime.endMinutes+ ':00'
        }

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
          this.$emit('eventFormSave', dataEvent)
        }
      },

      deleteEvent() {
        this.errorMsg = 'Result:'
        let deleteParams = {}
        deleteParams.id = this.eventDetail.id
        if (!this.recurringApplyOccurrences) {
          deleteParams.recurring = 'false'
        }

        if (this.recurringApplyOccurrences) {
          deleteParams.recurring = 'true'
        }

        this.$emit('eventFormDelete', deleteParams)
      },

      updateEvent() {
        this.errorMsg = 'Result:'
        let updateParams = {
          userId: this.selectedUserId,
          desc: this.description,
          startHour: this.eventTime.startHour,
          startMinutes: this.eventTime.startMinutes,
          endHour: this.eventTime.endHour,
          endMinutes: this.eventTime.endMinutes,
          reacurring: this.recurringApplyOccurrences,
        }

        if (this.timeInterval == 12) {
          if (this.hoursCount.selectedStart == 'pm' && updateParams.startHour < 12) {
            updateParams.startHour = updateParams.startHour + 12
          }

          if (this.hoursCount.selectedEnd == 'pm' && updateParams.endHour < 12) {
            updateParams.endHour = updateParams.endHour + 12
          }
        }
        
        this.$emit('eventFormUpdate', updateParams)
      },

      createDateOptions() {
        let cntMonthDays = new Date(this.year, this.month + 1, 0).getDate()

        if (this.day > cntMonthDays)
            this.day = cntMonthDays

        this.daysCount = []
        for(var i=1; i<=cntMonthDays; i++)
            this.daysCount.push(i)
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