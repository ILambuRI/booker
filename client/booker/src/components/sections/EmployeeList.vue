<template>
  <div class="row col-md-12 justify-content-md-center">
    <div class="col-md-4">
      <div v-show="showModal" class="alert alert-success" role="alert">
        <strong>User deleted successfuly!</strong>
        <button @click="showModal = false" type="button" class="close" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    </div>
    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Name</th>
          <th scope="col">Email</th>
          <th scope="col">Edit</th>
          <th scope="col">Delete</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(user, key) in allUsers" :key="key">
          <th scope="row"> {{ user.id }} </th>
          <td> {{ user.name }} </td>
          <td> {{ user.email }} </td>
          <td>
            <router-link :to="'/admin/edit-user/' + user.id">
              <button type="button" class="btn btn-secondary">Edit</button>
            </router-link>
          </td>
          <td>
            <button @click="selectedId = user.id; selectedName = user.name" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#deleteConfirm">
              Delete
            </button>
          </td>
        </tr>
      </tbody>
    </table>
    <router-link to="/admin/add-user/">
      <button type="button" class="btn btn-secondary">Add Employee</button>
    </router-link>

    <!-- Modal -->
    <div class="modal fade" id="deleteConfirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Delete Confirm</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            Are you sure you want to delete the {{ selectedName }} ?
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
            <button @click="deleteUser" data-dismiss="modal" type="button" class="btn btn-primary">Yes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  name: 'EmployeeList',
  data () {
    return {
      URL: URL,
      showModal: false,
      selectedId: '',
      selectedName: '',
    }
  },

  props: ["user", "allUsers"],

  components:{
  },

  computed: {
  },

  created() {
    if (!this.user.access || this.user.admin != 1) this.$router.push('/')
    // console.log(this.adminData)
  },

  methods: {
    deleteUser() {
      fetch(this.URL + 'admin/users/' + this.user.hash + '/' + this.selectedId, {method: 'DELETE'})
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.showModal = true
          this.$emit('userChanged')
        }
        else {
          let error = 'Error in getRooms()'+
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
