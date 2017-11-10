<template>
  <div class="row col-md-12 justify-content-md-center">

    <div class="row col-md-4">
      <div class="col-md-12">
        <div v-if="errorMsg" class="alert alert-danger">
          <strong>{{ errorMsg }}</strong>
          <button @click="errorMsg = ''" type="button" class="close" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form>
          <div class="form-group">
            <label>Name</label>
            <input v-model.trim="name" type="text" class="form-control" placeholder="Enter your name">
            <small v-if="name == ''" class="form-text text-muted">Start whith latin, one space between words is allowed (3 - 50 characters) ...</small>
            <small v-if="!validName & name.length > 0" class="form-text text-danger">Not yet correct ...</small>
            <small v-if="validName" class="form-text text-success"><strong>Сorrectly!</strong></small>
          </div>

          <div class="form-group">
            <label>Password</label>
            <input v-model.trim="password" type="password" class="form-control" placeholder="Enter your password">
            <small v-if="password == ''" class="form-text text-muted">Only latin and numbers (5 - 32 characters) ...</small>
            <small v-if="!validPassword & password.length > 0" class="form-text text-danger">Not yet correct ...</small>
            <small v-if="validPassword" class="form-text text-success"><strong>Сorrectly!</strong></small>
          </div>
        </form>
          <button @click="logIn()" class="float-right btn btn-dark" :disabled="!validBtnAccess">
            Login
          </button>
      </div>
    </div>

  </div>
</template>

<script>

export default {
  name: 'Login',
  data () {
    return {
      URL: URL,
      errorMsg: '',
      name: '',
      password: '',
    }
  },

  props: ["user", "rooms"],

  computed: {
    validBtnAccess() {
      if (this.validName && this.validPassword) {
        return true
      }

      return false
    },

    validName() {
      let x = /^[a-z]+(\s{1}[a-z]+)?$/i.exec(this.name)

      if (x && this.name.length >= 3 && this.name.length <= 50) {
        return true
      }

      return false
    },

    validPassword() {
      let x = /^[a-z0-9]+$/i.exec(this.password)

      if (x) {
        if (x && this.password.length >= 5 && this.password.length <= 32)
          return true
      }

      return false
    },
  },

  methods: {
    logIn() {
      fetch(this.URL + 'user/users/', {
        method: 'PUT',
        headers: {  
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
        },  
        body: 'name=' + this.name + '&password=' + this.password
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.user.access = true
          this.user.id = data.data.id
          this.user.name = data.data.name
          this.user.email = data.data.email
          this.user.admin = data.data.admin
          this.user.hash = data.data.hash

          localStorage['user'] = JSON.stringify(this.user)

          this.$router.push('/')
        }
        else if (data.server.code == '13') {
          this.errorMsg = 'Enter the correct name!'
        }
        else if (data.server.code == '14') {
          this.errorMsg = 'Enter the correct password!'
        }
        else {
          let error = 'Error in logIn()'+
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
