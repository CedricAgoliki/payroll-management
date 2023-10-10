<template>
  <div>
   <h2>Autres options</h2>
     <el-form :model="settingsForm">
       <!--<el-form-item label="Jour de paie">
         <el-input-number v-model='settingsForm.payday' :min="1" :max="28"></el-input-number>
       </el-form-item>-->
       
       <el-form-item label="Nom du Directeur">
	 <el-input v-model='settingsForm.director'></el-input>
       </el-form-item>
       
       <el-button type="primary" @click="saveSettings">Ajouter</el-button>
     </el-form>
  </div>
</template>

<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  data() {
    return {
      settingsForm: {
        director: '',
        payday: ''
      }
    }
  },
  created() {
    this.getSettings()
  },
  methods: {
    getSettings() {
      query('settings', 'GET').then((res) => {
	this.settingsForm.director = res.data.director
      })
    },
    saveSettings() {
      const data = {
	director: this.settingsForm.director,
      }
      query('settings/others', 'POST', data).then((res) => {
        if (res.data.success) {
	  this.getSettings()
          Message({
	    message: "Parametres bien enregistrés",
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
	    message: "Parametres non enregistés, une erreur s'est produite",
            type: 'error',
            duration: 5000
          })
        }
      })
    }
  }
}
</script>

