<template>
  <div id="app">

    <nav class="navbar navbar-expand-lg navbar-light bg-light mb-5">
      <router-link :to="'/'">
        <span class="navbar-brand">Booker</span>
      </router-link>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item active">
            <router-link :to="'/'">
              <span class="nav-link"> Home <span class="sr-only">(current)</span> </span>
            </router-link>
          </li>
        </ul>

        <div v-if="user.access" class="row">
          <router-link v-if="user.admin == 1" :to="'/admin/list-user'" class="btn btn-light mr-sm-1 my-sm-0 text-primary font-weight-bold">
            Admin panel
          </router-link>
          <button type="button" class="btn btn-light mr-sm-1 my-sm-0 text-primary font-weight-bold">
            {{ user.name }}
          </button>
          <button @click="logOut()" type="button" class="btn btn-outline-dark mr-sm-2 my-sm-0">
            <i class="fa fa-sign-out" aria-hidden="true"></i>
          </button>
        </div>
      </div>
    </nav>
    
    <!-- User information is available for the whole application -->
    <router-view :user="user" :timeInterval="timeInterval" @timeIntervalChange="timeInterval = $event" />

  </div>
</template>

<script>

export default {
  name: 'app',
  data () {
    return {
      URL: URL,
      rooms: [],
      timeInterval: '',
      user: {
        access: false,
        id: '',
        name: '',
        email: '',
        hash: '',
        admin: ''
      },
    }
  },

  watch: {

  },

  computed: {

  },

  created() {
    if (localStorage['user']) {
      this.user = JSON.parse(localStorage['user'])
      this.user.access = true
    }
  },

  methods: {
    logOut() {
      localStorage.removeItem("user")
      this.user.access = false
      
      fetch(this.URL + 'user/users/' + this.user.hash, {
        method: 'DELETE',
        headers: {  
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
        },
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {

        }
        else {
          let error = 'Error in logOut()'+
                      '\nStatus: ' + data.server.status +
                      '\nError code: ' + data.server.code +
                      '\nInfo: ' + data.server.information
          alert(error)
        }
      })

      this.$router.push('/login')
    },

    status(response) { 
      if (response.status == 200) {
        return Promise.resolve(response)
      } else {
        console.log('ERROR FETCH RESPONSE!')
        return Promise.reject( new Error(response.statusText) )  
      }
    },

    json(response) {
        return response.json()  
    },
  }
}
</script>
