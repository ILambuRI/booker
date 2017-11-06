<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="row col-md-4">
      <div class="col-md-12">
          <div v-show="showModal" class="alert alert-success" role="alert">
            <strong>User added successfuly!</strong>
            <button @click="showModal = false" type="button" class="close" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <user-form :allUsers="allUsers" @saveUser="saveUser" :clearForm="clearForm"></user-form> 
      </div>
    </div>
  </div>
</template>

<script>
import userForm from '../sillyComponents/AddEditUserForm'

export default {
  name: 'AddUser',
  data () {
    return {
      URL: URL,
      showModal: false,
      clearForm: false,
    }
  },

  components: {
    userForm
  },

  props: ["user", "allUsers"],

  created() {
    if (!this.user.access || this.user.admin != 1) this.$router.push('/')
  },

  methods: {
    saveUser(data) {
      fetch(this.URL + 'admin/users/', {
        method: 'POST',
        headers: {  
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },  
        body: 'hash=' + this.user.hash + '&name=' + data.name + '&email=' + data.email + '&password=' + data.password
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.showModal = true
          this.clearForm = true
          setTimeout(() => {
            this.clearForm = false
          }, 1000)
          this.$emit('userChanged')
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
