<template>
  <div class="app-container">
    <el-row>
      <el-form :model="loanForm" label-position="top">

        <el-form-item label="Employee">
          <template>
            <el-select filterable remote v-model="loanForm.employee">
              <el-option v-for="emp in employees"
		      :value="emp.id" 
		      :label="emp.last_name + ' ' + emp.first_name" 
		      :key="emp.id">
                <span>{{ emp.id }}</span>
                <span>{{ emp.last_name }}</span>
                <span>{{ emp.first_name }}</span>
	      </el-option>
            </el-select>
	  </template>
	</el-form-item>

        <el-form-item label="Date du prêt">
	  <el-date-picker type="date" 
			  v-model="loanForm.date" 
			  placeholder="Date de debut du congé"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-form-item>

        <el-form-item label="Echeancier">
	  <el-upload type="textarea" 
		  	:file-list="loanForm.fileList" 
	      		:action="uploadUrl"
	      		:on-success="onUploadSuccess"
	      		:multiple="false">
	    <el-button type="primary">Televerser l'écheancier PERFECT</el-button>
	    <div slot="tip" class="el-upload__tip">fichier XML generer avec PERFECT</div>
	  </el-upload>
	</el-form-item>

	<el-form-item v-if="deadlines">
	  <table>
	    <tr>
	      <th>Echéance</th>
	      <th>Interets</th>
	      <th>Capital</th>
	      <th>Epargne</th>
	      <th>Commissions</th>
	      <th>Total écheancier</th>
	      <!-- <th>Capital restant du</th>
	      <th>Solde restant du</th> -->
	    </tr>
	    <tr v-for="element in deadlines.echeances">
	      <td>{{ element.date }}</td>
	      <td>{{ element.interets }}</td>
	      <td>{{ element.capital }}</td>
	      <td>{{ element.epargne }}</td>
	      <td>{{ element.commission }}</td>
	      <td>{{ parseInt(element.capital) + parseInt(element.interets) }}</td>
	      <!-- <td>{{ element.capitalRestantDu }}</td>
	      <td>{{ element.soldeRestantDu }}</td> -->
	    </tr>
	    <tr>
	      <th>Total</th>
	      <th>{{ totalInterest }}</th>
	      <th>{{ totalCapital }}</th>
	      <th>{{ totalEpargne }}</th>
	      <th>{{ totalCommission }}</th>
	      <th>{{ totalEcheancier }}</th>
	      <!-- <th>{{ totalCapitalRestantDu }}</th>
	      <th>{{ totalSoldeRestantDu }}</th> -->
	    </tr>

	  </table>
	</el-form-item>

        <el-form-item label="Commentaire">
          <el-input type="textarea" v-model="loanForm.commentary"></el-input>
	</el-form-item>

	<el-button type="success" @click="saveLoan">Valider</el-button>
      </el-form>
    </el-row>
  </div>
</template>
<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'
import config  from '@/utils/config'

export default {
  name: 'documentation',
  data() {
    return {
      newEmployeeDialogVisible: false,
      employees: [],
      uploadUrl: config.baseUrl + 't',
      deadlines: null,
      loanDate: null,
      totalInterest: 0,
      totalCapital: 0,
      totalEpargne: 0,
      totalCommission: 0,
      totalEcheancier: 0,
      totalCapitalRestantDu: 0,
      totalSoldeRestantDu: 0,
      loanForm: {
        employee: null,
	fileList: [],
        commentary: '',
        date: '',
      }
    }
  },
  created() {
    this.loadEmployees()
  },
  methods: {
    onUploadSuccess(response) {
      this.deadlines = response
      console.log("success ", JSON.stringify(this.deadlines))
      this.totalInterest = 0
      this.totalCapital = 0
      this.totalEpargne = 0
      this.totalCommission = 0
      this.totalEcheancier = 0
      this.totalCapitalRestantDu = 0
      this.totalSoldeRestantDu = 0
      for (let e of this.deadlines.echeances) {
	this.totalInterest += parseFloat(e.interets)
	this.totalCapital += parseFloat(e.capital)
	this.totalEpargne += parseFloat(e.epargne)
	this.totalCommission += parseFloat(e.commission)
	// this.totalCapitalRestantDu += parseFloat(e.capitalRestantDu)
	// this.totalSoldeRestantDu += parseFloat(e.soldeRestantDu)
      }
      this.totalEcheancier = this.totalCapital + this.totalInterest
    },
    saveLoan() {
      const loan = {
	"employee" : this.loanForm.employee,
	"commentary" : this.loanForm.commentary,
	"data" : this.deadlines,
	"totalCapital": this.totalCapital,
	"totalInterest": this.totalInterest,
	"loanDate": this.loanForm.date
      }
      console.log(JSON.stringify(loan))
      query('loan/new', 'POST', loan).then((res) => {
        console.log(res)
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: res.data.message,
            type: 'error',
            duration: 5000
          })
        }
      })
    },
    loadEmployees() {
      query('employee/all', 'GET').then((res) => {
        console.log(res.data)
        this.employees = res.data
        // callback(res.data)
      })
    },
    monthPlus(startDate, toAdd) {
      const start = new Date(startDate)
      const current = new Date(start.setMonth(start.getMonth() + toAdd))
      return current
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.documentation-container {
  margin: 50px;
  .document-btn {
    float: left;
    margin-left: 50px;
    vertical-align: middle;
    display: block;
    cursor: pointer;
    background: black;
    color: white;
    height: 60px;
    width: 200px;
    line-height: 60px;
    font-size: 20px;
    text-align: center;
  }
}
.paymentsTable {
  margin: 5px;
  color: #000;
}

.paymentsTable table, th, td  {
  border: 1px solid black;
  border-collapse: collapse;
  margin: auto;
}

.paymentsTable th, td {
  text-align: center;
}
</style>
