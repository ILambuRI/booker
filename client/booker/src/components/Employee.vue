<template>
  <div class="row justify-content-md-center">
      <div class="col-md-8">
        <router-view :user="user" :allUsers="allUsers" @userChanged="getAllUsers" />
      </div>
  </div>

</template>

<script>

export default {
  name: 'Employee',
  data () {
    return {
      URL: URL,
      msg: 'Employee',
      allUsers: [],
    }
  },

  props: ["user"],

  components:{
  },

  computed: {
  },

  created() {
    if (!this.user.access || this.user.admin != 1) this.$router.push('/')
    this.getAllUsers()
  },

  methods: {
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
