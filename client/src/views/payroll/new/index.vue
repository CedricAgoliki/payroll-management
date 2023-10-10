<template>
  <div style="padding: 30px">
    <el-row>
      <el-form :model="dateForm" label-position="top">
        <el-form-item label="Selectionnez le mois">
	  <el-col :span="6">
            <el-date-picker v-model="dateForm.date" :picker-options="dateOptions" type="month">
	    </el-date-picker>
	  </el-col>
	  <el-col :span="11">
            <el-button type="primary" @click="loadPayroll">valider</el-button>
	  </el-col>
	</el-form-item>
      </el-form>
    </el-row>
    <el-row>
      <el-button type="success">imprimer les fiches</el-button>
      <!-- <el-button type="primary">valider</el-button> -->
    </el-row>
    <br />
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog v-if="toChange !== null" 
	    :title="'Fiche de paie de ' + toChange.last_name + ' ' + toChange.first_name" 
	    :visible.sync="modifyDialog">
      <div class="paySheetTable">
        <el-form :model="propertyForm" size="mini">
          <table>
            <tr>
              <th>Element</th>
              <th>Base de calcul</th>
              <th>Nb/Taux</th>
              <th>Retenues</th>
              <th>Avoirs</th>
	    </tr>
	    <tr v-for="prime in primes">
              <td>{{ prime.name }}</td>
              <td>
	        <el-form-item>
	          <el-input v-model="primesModel[prime.id]"></el-input>
	        </el-form-item>
	      </td>
              <td></td>
              <td></td>
	      <td>{{ primesModel[prime.id] }}</td>
	    </tr>
	    <tr v-for="deduction in deductions">
              <td>{{ deduction.name }}</td>
              <td></td>
              <td></td>
              <td>
	        <el-form-item>
	          <el-input v-model="deductionsModel[deduction.id]"></el-input>
	        </el-form-item>
	      </td>
              <td></td>
	    </tr>
	    <tr>
              <td colspan="3">Totaux</td>
              <td>0</td>
              <td>0</td>
	    </tr>
	    <tr>
              <td>salaire brut</td>
              <td>retenue obligatoire</td>
              <td>salaire net</td>
              <td>Autres retenues</td>
              <td>Net à payer</td>
	    </tr>
	    <tr>
              <td>{{ toChange.base_salary }}</td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
	    </tr>
	  </table>
	  <el-button type="primary" @click="saveSalary">Valider</el-button>
	  <el-button type="danger" @click="modifyDialog = false">retour</el-button>
        </el-form>
      </div>

    </el-dialog>


  <el-table :data="list" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column min-width="300px" label="Nom">
      <template slot-scope="scope">
        <span>{{scope.row.last_name}}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Prenoms">
      <template slot-scope="scope">
        <span>{{scope.row.first_name}}</span>
      </template>
    </el-table-column>


    <el-table-column min-width="50px" label="etat">
      <template slot-scope="scope">
        <span v-if= 'scope.row.salaries[0].reviewed'><i class="el-icon-check"></i></span>
        <span v-if= '!scope.row.salaries[0].reviewed'><i class="el-icon-close"></i></span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="primary" 
		@click="toChange = JSON.parse(JSON.stringify(scope.row)); modifyDialog = true" >
	  consulter
	</el-button>
      </template>
    </el-table-column>

  </el-table>
    </el-row>
  </div>
</template>

<script>
import { query } from '@/api/api'

export default {
  data() {
    return {
      list: null,
      primes: [],
      deductions: [],
      primesModel: [],
      deductionsModel: [],
      selected: [],
      toChange: null,
      loading: false,
      addDialog: false,
      modifyDialog: false,
      value: '',
      dateOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now()
        }
      },
      dateForm: {
        date: null
      },
      propertyForm: {
        name: '',
        description: '',
        regex: ''
      }
    }
  },
  created() {
    this.getElements()
  },
  methods: {
    getEmployees() {
      if (this.dateForm.date !== null) {
        const year = this.dateForm.date.getFullYear()
        const month = this.dateForm.date.getMonth() + 1
        console.log(this.dateForm.date, ' - ', year, ' - ', month)
        query('employee/allwithsalary/' + month + '/' + year, 'GET').then((res) => {
          this.list = res.data
        })
      }
    },
    getElements() {
      query('element/primes', 'GET').then((res) => {
        this.primes = res.data
        for (const p of this.primes) this.primesModel[p.id] = 0
        console.log('primes model ', this.primesModel)
        console.log(res.data)
      })
      query('element/deductions', 'GET').then((res) => {
        for (const d of this.deductions) this.deductionsModel[d.id] = 0
        this.deductions = res.data
        console.log(res.data)
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    loadPayroll() {
      if (this.dateForm.date !== null) {
        const year = this.dateForm.date.getFullYear()
        const month = this.dateForm.date.getMonth() + 1
        console.log(this.dateForm.date, ' - ', year, ' - ', month)
        query('payroll/get/' + month + '/' + year, 'GET').then(() => {
          query('employee/allwithsalary/' + month + '/' + year, 'GET').then((res) => {
            this.list = res.data
            console.log(res)
          })
        })
      }
    },
    saveSalary() {
      console.log(this.toChange)
    }
  }
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.paySheetTable table, th, td  {
  border: 1px solid black;
  border-collapse: collapse;
}

.paySheetTable th, td {
  text-align: center;
}
</style>
