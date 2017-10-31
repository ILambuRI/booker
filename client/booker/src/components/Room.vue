<template>
  <div class="row col-md-12 justify-content-md-center">

ROOM {{ $route.params.id }}
  </div>
</template>

<script>

export default {
  name: 'Room',
  data () {
    return {
      URL: URL,
    }
  },

  watch: {
  },

  props: ["user"],

  computed: {
  },

  created() {
    if (!this.user.access) {
      this.$router.push('/')
    }
  },

  methods: {
    saveAuthor() {
      fetch(this.URL + 'admin/authors/', {
        method: 'POST',
        headers: {  
          "Content-type": "application/x-www-form-urlencoded; charset=UTF-8"  
        },  
        body: 'hash=' + this.user.hash + '&name=' + this.name
      })
      .then(this.status)
      .then(this.json)
      .then((data) => {
        if (data.server.status == 200) {
          this.adminEvent('Authors')
        }
        else {
          let error = 'Error in saveAuthor()'+
                      '\nStatus: ' + data.server.status +
                      '\nError code: ' + data.server.code +
                      '\nInfo: ' + data.server.information
          alert(error)
        }
      })

      this.name = ''
    },

    adminEvent(type) {
      this.$emit('adminEvent', type)
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