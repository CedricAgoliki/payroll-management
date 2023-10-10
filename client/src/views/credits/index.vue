<template>
  <div class="app-container">
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialogVisible = true"></el-button>
      <el-button type="danger" icon="el-icon-delete"></el-button>
    </el-row>
    <br />
    <el-row>

    <el-dialog title="Ajouter" :visible.sync="addDialogVisible">
      <el-form ref="creditForm" :model="creditForm" label-position="right" label-width="200px">
        <el-form-item label="Employé">
	  <!-- <el-input v-model="congeForm.employe"></el-input>-->
	  <el-select v-model="creditForm.employe"
		     filterable
		     remote
		     reserve-keyword
		     placeholder="Employé"
		     :remote-method="filtrerListe"
		     :loading="chargement">
	    <el-option v-for="employe in listeEmploye"
		       :key="employe.id"
		       :label="employe.nom"
		       :value="employe.id"
	      ></el-option>
	  </el-select>
        </el-form-item>
        <el-form-item label="Date de debut du congé">
	  <el-date-picker v-model='creditForm.date' 
			  format="dd-MM-yyyy" 
			  value-format="yyyy-MM-dd"></el-date-picker>
        </el-form-item>
        <el-form-item label="Montant">
	  <el-input-number v-model="creditForm.montant" ></el-input-number>
        </el-form-item>
        <el-form-item label="Taux">
	  <el-input-number v-model="creditForm.taux" ></el-input-number>
        </el-form-item>
        <el-button type="primary" @click="enregistrerCredit">Valider</el-button>
      </el-form>
    </el-dialog>

      <el-table stripe :data="listeEmploye" @selection-change="handleSelectionChange">
	<el-table-column label="#" type="selection" width="50"></el-table-column>
	<el-table-column label="Nom" prop="nom"></el-table-column>
	<el-table-column label="Prenoms" prop="prenoms"></el-table-column>
	<el-table-column label="Credits" prop="fiche">
	  <template slot-scope="scope">
	    <el-button type="primary" @click="voirFiche(scope.$index)">voir les credits</el-button>
	  </template>
	</el-table-column>
      </el-table>
    </el-row>

    <el-dialog title="Fiche des credits" :visible.sync="ficheCreditsVisible">
      <div v-if="active >= 0">
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { getAll, createResource } from '@/api/api'

export default {
  name: 'credits',
  data() {
    return {
      addDialogVisible: false,
      ficheCreditsVisible: false,
      minJour: 1,
      maxJour: 30,
      listeEmploye: [],
      active: -1,
      creditForm: {
        employe: '',
        date: '',
        taux: 0,
        montant: 0
      }
    }
  },
  created() {
    this.getListeEmploye()
  },
  methods: {
    getListeEmploye() {
      getAll('employes').then(response => {
        this.listeEmploye = response.data
      })
    },
    voirFiche(index) {
      this.ficheCreditsVisible = true
      this.active = index
    },
    enregistrerCredit() {
      const credit = {
        'date': this.creditForm.date,
        'taux': this.creditForm.taux,
        'montant': this.creditForm.montant,
        'employe_id': this.creditForm.employe
      }
      console.log(credit)
      createResource('credits', credit).then(() => console.log('credit ok'))
    },
    filtrerListe(req) {
      console.log(req)
    }
  }
}
</script>
