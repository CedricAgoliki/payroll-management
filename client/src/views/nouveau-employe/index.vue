<template>
  <div class="tab-container">
    <el-form :label-position="labelPosition" ref="employeForm" :model="employeForm">
      <div v-if="step == 0"> 
	<h2>Ajout d'un nouveau employé</h2>
	<el-form-item label="Nom et prenoms" prop="identite">
	  <el-col :span="11">
	    <el-input v-model="employeForm.lastName" placeholder="Nom"></el-input>
	  </el-col>
	  <el-col class="line" :span="2">.</el-col>
	  <el-col :span="11">
	    <el-input v-model="employeForm.firstName" placeholder="Prénoms"></el-input>
	  </el-col>
	</el-form-item>
   
	<el-form-item :label-width="labelWidth" 
		      label="Numéro de sécurité sociale"
		      prop="securite-sociale">
	  <el-input v-model="employeForm.socialSecurityNumber"></el-input>
	</el-form-item>
	
	<el-form-item :label-width="labelWidth" 
	  label="Numéro de compte bancaire" 
	  prop="compte-bancaire">
	  <el-input v-model="employeForm.bankAccount"></el-input>
	</el-form-item>
	
	<el-form-item :label-width="labelWidth" label="Numéro matricule" prop="matricule">
	  <el-input v-model="employeForm.matricule"></el-input>
	</el-form-item>

	<el-form-item :label-width="labelWidth" label="Personne à charge" prop="matricule">
	  <el-input v-model="employeForm.dependants"></el-input>
	</el-form-item>

	<el-form-item :label-width="labelWidth" label="Congés par mois" prop="matricule">
	  <el-input v-model="employeForm.leavesPerMonth"></el-input>
	</el-form-item>

      <el-button type="primary" @click="previousStep()">precedant</el-button>
      <el-button type="primary" @click="nextStep()">Suivant</el-button>
    </div>

    <div v-if="step == 1">
      <h2>Autres informations sur l'employée</h2>
      <el-button type="primary" @click="previousStep()">precedant</el-button>
      <el-button type="primary" @click="nextStep()">Suivant</el-button>
    </div>

    <div v-if="step == 2"> 
      <h2>Informations sur le contrat</h2>
      <el-form-item label="Date de contrat" prop="identite">
	<el-col :span="7">
	  <el-date-picker type="date" 
			  v-model="employeForm.contractStart" 
			  placeholder="Date du contrat"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-col>
	<el-col class="line" :span="2">.</el-col>
	<el-col :span="7">
	  <el-date-picker type="date" 
			  v-model="employeForm.contractEnd" 
			  placeholder="Date de contrat prévue"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-col>
      </el-form-item>

      <el-form-item :label-width="labelWidth" label="Commentaire" prop="commentaire">
	<el-input v-model="employeForm.contractCommentary"></el-input>
      </el-form-item>
    
      <el-button type="primary" @click="previousStep()">precedant</el-button>
      <el-button type="success" @click="ajouterEmploye">Ajouter</el-button>
    </div>
  </el-form>
  </div>
</template>

<script>
import { query } from '@/api/api'

export default {
  name: 'tab',
  data() {
    return {
      labelPosition: 'top',
      labelWidth: '200px',
      step: 0,
      employeForm: {
        firstName: '',
        lastName: '',
        baseSalary: '',
        leavesPerMonth: '',
        dependants: '',
        contractStart: '',
        contractEnd: '',
        contractCommentary: '',
        socialSecurityNumber: '',
        bankAccount: ''
      }
    }
  },
  created() {
  },
  methods: {
    nextStep() {
      this.step++
    },
    previousStep() {
      this.step--
    },
    ajouterEmploye() {
      const emp = {
        'firstName': this.employeForm.firstName,
        'lastName': this.employeForm.lastName,
        'socialSecurityNumber': this.employeForm.socialSecurityNumber,
        'bankAccount': this.employeForm.bankAccount,
        'matricule': this.employeForm.matricule,
        'contractStart': this.employeForm.contractStart,
        'contractEnd': this.employeForm.contractEnd,
        'baseSalary': this.employeForm.baseSalary,
        'dependants': this.employeForm.dependants
      }
      console.log(emp)
      query('employee/enter', 'post', emp)
    }
  }
}
</script>

<style scoped>
  .tab-container{
    margin-left: 100px;
    margin-right: 100px;
    /*margin: 100px;*/
  }
</style>
