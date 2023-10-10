<template>
  <div class="tab-container">
    <el-row>
      <el-button type="primary">Ajouter</el-button>
      <el-button type="danger">Supprimer</el-button>
    </el-row>
    <el-row>
      <el-table stripe :data="employeeList" @selection-change="handleSelectionChange">
	<el-table-column label="#" type="selection" width="50"></el-table-column>
	<el-table-column label="Nom" prop="nom">
      	  <template slot-scope="scope">
            <span>{{ scope.row.last_name }}</span>
          </template>
	</el-table-column>
	<el-table-column label="Prenoms" prop="prenoms">
      	  <template slot-scope="scope">
            <span>{{ scope.row.first_name }}</span>
          </template>
	</el-table-column>
	<el-table-column label="Fiches" prop="fiche">
	  <template slot-scope="scope">
	    <el-button type="primary" @click="voirFiche(scope.$index)">voir la fiche</el-button>
	  </template>
	</el-table-column>
      </el-table>
    </el-row>

    <el-dialog title="Ajouter" :visible.sync="ficheDialogVisible">
      <div v-if="active >= 0">
        <table class="ficheTable">
          <tr>
            <th>MATRICULE</th>
            <th>NOM ET PRENOMS</th>
            <th>CATEGORIE</th>
          </tr>
          <tr>
            <th>{{ listeEmploye[active].matricule }}</th>
            <th>{{ listeEmploye[active].nom + " " + listeEmploye[active].prenoms }}</th>
            <th>{{ listeEmploye[active].categorie.libelle }}</th>
          </tr>
          <tr>
            <th>Date CDD/CDI/Stage</th>
            <th>NUMERO SECURITE SOCIALE</th>
            <th>MODE DE PAIEMENT</th>
          </tr>
          <tr>
            <th>{{ listeEmploye[active].date_contrat }}</th>
            <th>{{ listeEmploye[active].numero_securite_sociale }}</th>
            <th>{{ listeEmploye[active].mode_de_payement.libelle }}</th>
          </tr>
          <tr>
            <th colspan="2">FONCTION</th>
            <th>NUMERO DE COMPTE</th>
          </tr>
          <tr>
            <th colspan="2">{{ listeEmploye[active].fonction.libelle }}</th>
            <th>{{ listeEmploye[active].numero_compte_bancaire }}</th>
          </tr>
        </table>
        <!--<el-tabs>
          <el-tab-pane label="Paie"></el-tab-pane>
          <el-tab-pane label="Avances"></el-tab-pane>
          <el-tab-pane label="Paie"></el-tab-pane>
          <el-tab-pane label="Paie"></el-tab-pane>
          <el-tab-pane label="Paie"></el-tab-pane>
	</el-tabs>-->
      </div>
    </el-dialog>



  </div>
</template>

<script>
import { query } from '@/api/api'

export default {
  name: 'tab',
  data() {
    return {
      employeeList: [],
      selectedEmploye: [],
      ficheDialogVisible: false,
      active: -1
    }
  },
  created() {
    this.getListeEmploye()
  },
  methods: {
    getListeEmploye() {
      query('employee/all', 'GET').then(response => {
        this.employeeList = response.data
      })
    },
    voirFiche(index) {
      this.ficheDialogVisible = true
      this.active = index
    },
    handleSelectionChange(val) {
      this.selectedEmploye = val
      console.log(this.selectedEmploye)
    }
  }
}
</script>

<style scoped>
  .tab-container{
    margin: 30px;
  }
  .ficheTable{
    width: 100%;
    border: 1px solid black;
    border-collapse: collapse;
  }

  .ficheTable th, td {
    border: 1px solid black;
    border-collapse: collapse;
  }

  .ficheTable tr:nth-child(odd) {
    background-color: gray;
    color: #fff;
  }
</style>
