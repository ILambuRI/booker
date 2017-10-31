import Vue from 'vue'
import Router from 'vue-router'

import LoginPage from '@/components/Login'
import RoomPage from '@/components/Room'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Login page',
      component: LoginPage
    },

    {
      path: '/room/:id',
      name: 'Room page',
      component: RoomPage
    },


    // {
    //   path: '/admin',
    //   name: 'Admin',
    //   component: Admin,
    //   children: [
    //     { path: 'new-user', component: UserRegistration },
    //     { path: 'edit-user/:id', component: EditUser },

    //     { path: 'new-author', component: NewAuthor },
    //     { path: 'edit-author/:id', component: EditAuthor },

    //     { path: 'new-genre', component: NewGenre },
    //     { path: 'edit-genre/:id', component: EditGenre },

    //     { path: 'new-book', component: NewBook },
    //     { path: 'edit-book/:id', component: EditBook },

    //     { path: 'all-orders', component: AllOrders },
    //   ]
    // },
  ]
})
