<template>
  <tbody>
    <tr v-for="(week, key) in monthArr" :key="key">
      <td v-for="(day, key) in week" :key="key">
        <day v-bind:day="day"></day>
      </td>
    </tr>
  </tbody>
</template>

<script>
import Day from './Day'

export default {
  name: 'Month',
  data () {
    return {

    }
  },

  props:['monthArr'],

  components:{
    Day
  }
}  
</script>