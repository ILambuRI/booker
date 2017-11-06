<template>
  <form>
    <div class="form-group">
      <label>Name</label>
      <input v-model.trim="name" type="text" class="form-control" placeholder="Name">
      <small v-if="name == ''" class="form-text text-muted">Start whith latin, remain are numbers and latin (3 - 20 characters) ...</small>
      <small v-if="!validName & name.length > 0" class="form-text text-danger">Not yet correct ...</small>
      <small v-if="validName & nameExist" class="form-text text-danger">Name is already taken, please enter another!</small>
      <small v-if="validName & !nameExist" class="form-text text-success">Сorrectly!</small>
      <!-- <small v-if="validName" class="form-text text-success">Сorrectly!</small> -->
    </div>

    <div class="form-group">
      <label>Email</label>
      <input v-model.trim="email" type="email" class="form-control" aria-describedby="emailHelp" placeholder="Enter email">
      <small v-if="email == ''" class="form-text text-muted">Start whith latin, remain are numbers and latin (3 - 20 characters) ...</small>
      <small v-if="!validEmail & email.length > 0" class="form-text text-danger">Not yet correct ...</small>
      <small v-if="validEmail & emailExist" class="form-text text-danger">Email is already taken, please enter another!</small>
      <small v-if="validEmail" class="form-text text-success">Сorrectly!</small>
    </div>

    <div class="form-group">
      <label>Password</label>
      <input v-model.trim="password" type="password" class="form-control" placeholder="Password">
      <small v-if="password == ''" class="form-text text-muted">Only latin and numbers (5 - 32 characters) ...</small>
      <small v-if="!validPassword & password.length > 0" class="form-text text-danger">Not yet correct ...</small>
      <small v-if="validPassword" class="form-text text-success">Сorrectly!</small>
    </div>

    <div class="form-group">
      <label>Confirm password</label>
      <input v-model.trim="confirm" type="password" class="form-control" placeholder="Confirm the password">
      <small v-if="confirm == ''" class="form-text text-muted">Confirm the password</small>
      <small v-if="password != confirm & confirm != ''" class="form-text text-danger">Not yet correct ...</small>
      <small v-if="password == confirm & validPassword" class="form-text text-success">Сorrectly!</small>
    </div>

    <router-link to="/admin/list-user">
      <button class="float-left btn btn-dark">
        Cancel
      </button>
    </router-link>
    <button @click.prevent="saveUser()" class="float-right btn btn-dark" :disabled="!validBtnAccess">
      Submit
    </button>
  </form>
</template>

<script>

export default {
  name: 'AddUser',
  data () {
    return {
      URL: URL,
      name: '',
      email: '',
      password: '',
      confirm: '',
      nameExist: false,
      emailExist: false,
      showModal: false,
    }
  },

  props: ["allUsers", "clearForm", "userInfo"],

  watch: {
    // 'userInfo': {
    //   handler() {
    //     console.log(this.userInfo)
    //     if (this.userInfo.name) {
    //       this.name = this.userInfo.name
    //       this.email = this.userInfo.email
    //     }
    //   },

    //   deep: true
    // },

    'clearForm'() {
      if (this.clearForm == true) {
        this.name = ''
        this.email = ''
        this.password = ''
        this.confirm = ''
      }
    },

    'name'() {
      this.nameExist = false

      this.allUsers.map((el) => {
        if (el.name.toLowerCase() == this.name.toLowerCase() && (!this.userInfo || el.name.toLowerCase() != this.userInfo.name.toLowerCase()) ) {
          this.nameExist = true
          return false
        }
      })
    },

    'email'() {
      this.emailExist = false

      this.allUsers.map((el) => {
        if (el.email.toLowerCase() == this.email.toLowerCase() && (!this.userInfo || el.email.toLowerCase() != this.userInfo.email.toLowerCase()) ) {
          this.emailExist = true
          return false
        }
      })
    }
  },

  created() {
    if (this.userInfo && this.userInfo.name) {
      this.name = this.userInfo.name
      this.email = this.userInfo.email
    }
  },

  computed: {
    validBtnAccess() {
      if (this.validName &&
          this.validPassword &&
          this.validEmail &&
          !this.nameExist &&
          !this.emailExist &&
          this.password == this.confirm) {

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

    validEmail() {
      let x = /[a-z0-9!#$&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?/i.exec(this.email)

      if (x && this.email.length >= 3 && this.email.length <= 50) {
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
    saveUser() {
      this.$emit('saveUser', {name: this.name, email: this.email, password: this.password})
    },
  }
}

</script>
