<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="row col-md-4">
      <div class="col-md-12">
        <div v-show="showModal" class="alert alert-success" role="alert">
          <strong>User edited successfuly!</strong>
          <button @click="showModal = false" type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <user-form :allUsers="allUsers" @saveUser="saveUser" :userInfo="{name, email}"></user-form>
        
      </div>
    </div>
  </div>
</template>

<script>
import userForm from '../sillyComponents/AddEditUserForm'

export default {
  name: 'EditUser',
  data () {
    return {
      URL: URL,
      name: '',
      email: '',
      showModal: false,
    }
  },

  components: {
    userForm
  },

  props: ["user", "allUsers"],

  created() {
    if (!this.user.access || this.user.admin != 1) this.$router.push('/')
    this.getUserInfo()
  },

  methods: {
    getUserInfo() {
      for (let i=0, len = this.allUsers.length; i<=len; i++) {
        if (this.allUsers[i].id == this.$route.params.id) {
          this.name = this.allUsers[i].name
          this.email = this.allUsers[i].email
          return false
        }
      }
    },

    saveUser(data) {
      fetch(this.URL + 'admin/users/', {
        method: 'PUT',
        headers: {
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"
        },  
        body: 'hash=' + this.user.hash + '&id=' + this.$route.params.id + '&name=' + data.name + '&email=' + data.email + '&password=' + data.password
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.showModal = true
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