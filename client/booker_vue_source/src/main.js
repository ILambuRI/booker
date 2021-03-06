// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'

Vue.config.productionTip = false

/* Checking the route access */
router.beforeEach((to, from, next) => {
  if (!localStorage['user'] && to.path != '/login') {
    next('/login')
  }
  else if (to.path == '/login' && localStorage['user']) {
    next('/')
  }
  else {
    next()
  }
})

new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
