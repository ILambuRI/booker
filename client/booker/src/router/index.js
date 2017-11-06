import Vue from 'vue'
import Router from 'vue-router'

import Login from '@/components/Login'
import Room from '@/components/Room'
import EventForm from '@/components/EventForm'
import Employee from '@/components/Employee'
import EmployeeList from '@/components/sections/EmployeeList'
import AddUser from '@/components/sections/AddUser'
import EditUser from '@/components/sections/EditUser'
import EventDetail from '@/components/EventDetail'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Room page',
      component: Room
    },
    
    {
      path: '/login',
      name: 'Login page',
      component: Login
    },

    {
      path: '/book/:id/:room_name',
      name: 'EventForm page',
      component: EventForm
    },

    {
      path: '/event/detail/:id/:timeInterval',
      name: 'EventDetail page',
      component: EventDetail
    },

    {
      path: '/admin',
      name: 'Employee List',
      component: Employee,
      children: [
        { path: 'add-user', component: AddUser },
        { path: 'edit-user/:id', component: EditUser },
        { path: 'list-user', component: EmployeeList },
      ]
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
