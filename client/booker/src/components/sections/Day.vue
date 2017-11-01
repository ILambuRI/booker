<template>
    <p>{{day['data']}}</p>
</template>

<script>
export default {
  name: 'Day',
  data () {
    return {

    }
  },

  props:['day']
}
</script>