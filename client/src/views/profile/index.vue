<template>
  <div class="app-container">
    <h1>Profile</h1>
    <el-tabs>
      <el-tab-pane label="Changer l'adresse email">
	<el-form>
	  <el-form-item label="nouveau email">
	    <el-input v-model="emailForm.email"/>
	  </el-form-item>
	  <el-button type="success" @click="changeEmail">Modifier</el-button>
	</el-form>
      </el-tab-pane>
      <el-tab-pane label="Changer de mot de passe">
	<el-form>
	  <el-form-item label="Mot de passe actuel">
	    <el-input v-model="pwdForm.current"/>
	  </el-form-item>
	  <el-form-item label="Nouveau mot de passe">
	    <el-input v-model="pwdForm.newpwd"/>
	  </el-form-item>
	  <el-button type="success" @click="changePwd">Modifier</el-button>
	</el-form>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
// import * as Driver from 'driver.js' // import driver.js
// import 'driver.js/dist/driver.min.css' // import driver.js css
// import steps from './defineSteps'
import { getUserInfo } from '@/api/login'
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  name: 'guide',
  data() {
    const actualEmail = this.$store.state.user.me.email
    return {
      emailForm: {
	email: actualEmail
      },
      pwdForm: {
	current: '',
	newpwd: ''
      }
    }
  },
  mounted() {
    //this.driver = new Driver()
  },
  methods: {
    guide() {
      // this.driver.defineSteps(steps)
      // this.driver.start()
    },
    changePwd() {
      console.log("changing password")
      const data = {
	current: this.pwdForm.current,
	password: this.pwdForm.newpwd
      }
      const userId = this.$store.state.user.me.id
      query('user/changepwd/' + userId, 'POST', data).then((res) => {
	if (res.data.success) {
	  Message({
	    message: "Mot de passe changé avec success",
	    type: 'success',
	    duration: 5000
	  })
	} else {
	  Message({
	    message: "Une erreur s'est produit - ressayez plus tard",
	    type: 'error',
	    duration: 5000
	  })
	}
	
      })
    },
    changeEmail() {
      console.log("changing email with", this.emailForm.email)
      const data = {
	'email' : this.emailForm.email
      }
      const userId = this.$store.state.user.me.id
      query('user/changeemail/' + userId, 'POST', data).then((res) => {
	console.log("response: ", res)
	if (res.data.success) {
	  Message({
	    message: "Adresse email changée avec success",
	    type: 'success',
	    duration: 5000
	  })
	  const token = this.$store.state.user.token
	  getUserInfo(token).then((response) => {
	    if (!response.data) {
	      reject('error')
	    }
	    this.$store.commit('SET_ME', response.data)
	    resolve(response)
	  }).catch(error => {
	    reject(error)
	  })
	} else {
	  Message({
	    message: "Une erreur s'est produit - ressayez plus tard",
	    type: 'error',
	    duration: 5000
	  })
	}
      })
    }
  }
}
</script>
