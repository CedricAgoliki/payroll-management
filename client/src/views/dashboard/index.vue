<template>
  <div class="dashboard-container">
    <el-dialog title="Premiere connection: changer votre mot de passe" 
	      :visible.sync="changePasswordDialog"
	       :show-close="false"
	       :close-on-click-modal="false">
      <div>
	<el-form>
	  <el-button type="success" icon="el-icon-view" @click="showPwd"></el-button>
	  <el-form-item label="Nouveau mot de passe">
	    <el-input :type="passwordType" v-model="passwordForm.password"></el-input>
	  </el-form-item>
	  <el-form-item label="Confirmation">
	    <el-input :type="passwordType" v-model="passwordForm.confirmation"></el-input>
	  </el-form-item>
	  <el-button type="success" @click="changePwd">Changer le mot de passe</el-button>
	  <el-button type="danger" @click="logout">Deconnexion</el-button>
	</el-form>
      </div>
    </el-dialog>
    <component :is="currentRole"></component>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import adminDashboard from './admin'
import editorDashboard from './editor'
import { Message } from 'element-ui'
import { query } from '@/api/api'

export default {
  name: 'dashboard',
  components: { adminDashboard, editorDashboard },
  data() {
    return {
      changePasswordDialog: false,
      passwordType: 'password',
      passwordForm: {
	password: '',
	confirmation: ''
      },
      currentRole: 'adminDashboard'
    }
  },
  computed: {
    ...mapGetters([
      'roles'
    ])
  },
  created() {
    this.checkFirstLogin()
    if (!this.roles.includes('admin')) {
      this.currentRole = 'editorDashboard'
    }
  },
  methods: {
    checkFirstLogin() {
      const def = this.$store.state.user.me.default_password 
      if (def === 1) this.changePasswordDialog = true
    },
    changePwd() {
      const password = this.passwordForm.password.trim()
      const confirmation = this.passwordForm.confirmation.trim()
	if (password === confirmation) {
	  const data = {
	    "password": password,
	  }
	  const me = this.$store.state.user.me
	  query('user/changefirstpwd/' + me.id, 'POST', data).then((res) => {
	    if (res.data.success) {
	      Message({
		message: "Mot de passe défini avec success",
		type: 'success',
		duration: 5000
	      })
	      this.changePasswordDialog = false
	    } else {
	      Message({
		message: "Une erreur s'est produit - ressayez plus tard",
		type: 'error',
		duration: 5000
	      })
	    }
	  })
	} else {
          Message({
	    message: "Reverifier bien votre mot de passe",
            type: 'error',
            duration: 5000
          })
	}
    },
    showPwd() {
      if (this.passwordType === 'password') {
	this.passwordType = 'input'
      } else {
	this.passwordType = 'password'
      }
    },
    logout() {
      this.$store.dispatch('LogOut').then(() => {
	location.reload()
      })
    }
  }
}
</script>
