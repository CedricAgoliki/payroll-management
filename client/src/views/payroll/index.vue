<template>
  <div style="padding: 30px">
    <el-row>
      <el-form :model="dateForm" label-position="top">
	<el-row>
	<el-col :span="6">
	  <el-form-item label="Date de debut">
            <el-date-picker v-model="dateForm.startDate" 
			    format="dd-MM-yyyy"
			    value-format="yyyy-MM-dd"
			    :picker-options="dateOptions">
	    </el-date-picker>
	  </el-form-item>
	</el-col>
	<el-col :span="6">
	  <el-form-item label="Date de fin">
            <el-date-picker v-model="dateForm.endDate"
			    format="dd-MM-yyyy"
			    value-format="yyyy-MM-dd"
			    :picker-options="dateOptions">
	    </el-date-picker>
	  </el-form-item>
	</el-col>
	<el-col :span="8">
          <el-form-item label="Selectionnez les jours fériés">
	    <v-date-picker mode="multiple" v-model="dateForm.holidays"></v-date-picker>
	  </el-form-item>
	</el-col>
	</el-row>
        <el-button type="primary" @click="loadPayroll">valider</el-button>
      </el-form>
    </el-row>
    <el-row v-if="list !== null">
	    <!--<el-button type="success">Imprimer les fiches</el-button>-->
      <a class="el-button el-button--success el-button--medium" :href="downloadAllLink" target="_blank">
	      Imprimer les fiches
      </a>
      <el-button type="primary" @click="recompute">Recalculer les fiches</el-button><br />
      <el-col>
	<h3>Jours non pris en compte</h3>
	<ul v-if="list && list.length > 0">
	  <li v-for="date in JSON.parse(list[0].salaries[0].holidays)">
	    {{ date | moment('DD-MM-YYYY') }}
	  </li>
	</ul>
      </el-col>
      <el-col>
	<h3>Nombre de jours de travail</h3>
	<p>{{ list[0].salaries[0].openDays }}</p>
      </el-col>
    </el-row>
    <br />
    <el-row>


    <!-- show Dialogs -->
    <el-dialog v-if="toShow !== null && deductionsModel !== null && primesModel !== null" 
	    :title="'Fiche de paie de ' + list[toShow].last_name + ' ' + list[toShow].first_name" 
	    :visible.sync="showDialog00"
	    width="75%">
      <div class="paySheetTable">
          <table>
            <tr style="background: #aaa;">
              <th>ELEMENT</th>
              <th>BASE DE CALCUL</th>
              <th>NB/TAUX</th>
              <th>RETENUES</th>
              <th>AVOIRS</th>
	    </tr>
	    <tr>
              <td style="text-align: justify">SALAIRE DE BASE</td>
	      <td>{{ list[toShow].salaries[0].base_salary }}</td>
              <td></td>
	      <td></td>
              <td>{{ list[toShow].salaries[0].base_salary }}</td>
	    </tr>
	    <tr v-for="prime in primes" v-if="allowedModel[prime.id]" :key="prime.id">
              <td style="text-align: justify">{{ prime.name.toUpperCase() }}</td>
	      <td>{{ primesModel[prime.id] }}</td>
              <td>30.0</td>
              <td></td>
	      <td>{{ primesModel[prime.id] }}</td>
	    </tr>
	    <tr v-for="deduction in deductions" v-if="allowedModel[deduction.id]" v:key="deduction.id">
              <td style="text-align: left">{{ deduction.name.toUpperCase() }}</td>
              <td>{{ deductionsModel[deduction.id] }}</td>
              <td></td>
              <td>{{ deductionsModel[deduction.id] }}</td>
              <td></td>
	    </tr>
	    <tr v-if="settings">
              <td style="text-align: left">COTISATION CNSS</td>
	      <td>0</td>
              <td></td>
	      <td>{{ list[toShow].salaries[0].settings.employee_contribution_rate }}</td>
              <td></td>
	    </tr>
	    <tr>
              <td style="text-align: justify">IMPOT SUR LE REVENU</td>
	      <td>0</td>
              <td></td>
	      <td>0</td>
              <td></td>
	    </tr>
	    <tr v-if="settings">
              <td style="text-align: left">TCS</td>
	      <td>{{ /*JSON.parse(list[toShow].salaries[0].settings.payroll_tax).irpp*/ 0 }}</td>
              <td></td>
	      <td>{{ list[toShow].salaries[0].payroll_tax }}</td>
              <td></td>
	    </tr>
	    <tr>
              <td style="text-align: left">AVANCE SUR SALAIRE</td>
	      <td>{{ advances }}</td>
              <td></td>
	      <td>{{ advances }}</td>
              <td></td>
	    </tr>
	    <tr>
              <td style="text-align: left">CREDIT AU PERSONNEL</td>
	      <td>{{ list[toShow].loans.length > 0 ? list[toShow].loans[0].amount : 0 }}</td>
              <td></td>
	      <td>{{ list[toShow].loans.length > 0 ? list[toShow].loans[0].amount : 0 }}</td>
              <td></td>
	    </tr>
	    <tr>
              <td colspan="3"><b>TOTAUX</b></td>
	      <td>{{ totalDeductions }}</td>
	      <td>{{ totalPrimes + parseInt(list[toShow].salaries[0].base_salary) }}</td>
	    </tr>
	    <tr style="background: #aaa;">
              <td><b>SALAIRE BRUT</b></td>
              <td><b>RETENUE OBLIGATOIRE</b></td>
              <td><b>SALAIRE NET</b></td>
              <td><b>AUTRES RETENUES</b></td>
              <td><b>NET A PAYER</b></td>
	    </tr>
	    <tr>
              <td>{{ list[toShow].salaries[0].base_salary }}</td>
              <td>{{ mandatoryDeductions }}</td>
	      <td>{{ netSalary ? netSalary : '-' }}</td>
              <td>{{ otherDeductions }}</td>
	      <td>{{ netToPay }}</td>
	    </tr>
	  </table>
	  <el-button type="success" @click="validate()">valider</el-button>
	  <el-button type="primary" @click="showDialog = false">retour</el-button>
      </div>

    </el-dialog>

    <!-- show Dialogs 2-->
    <el-dialog v-if="toShow !== null && deductionsModel !== null && primesModel !== null" 
	    :title="'Fiche de paie de ' + list[toShow].last_name + ' ' + list[toShow].first_name" 
	    :visible.sync="showDialog"
	    :before-close="handleCloseShow"
	    width="75%">
      <div style="width:100%; height: 100vh">
	      <iframe :src="printSingleUrlWeb(toShow)" 
	  allowTransparency="true" 
	  scrolling="yes" 
	  frameborder="no"
	  width="100%"
	  height="100%"
	  ></iframe>
	</div>
	  <el-button type="success" @click="validate()">valider</el-button>
	  <el-button type="primary" @click="showDialog = false">retour</el-button>

    </el-dialog>

    <!-- modify Dialogs -->
    <el-dialog v-if="toChange !== null" 
	    :title="'Fiche de paie de ' + list[toChange].last_name + ' ' + list[toChange].first_name" 
	    :visible.sync="modifyDialog"
	    width="75%">
      <div class="paySheetTable">
        <el-form :model="propertyForm" size="mini">
          <table>
            <tr style="background: #aaa;">
	      <th>ACCORDÉS</th>
	      <th>VARIABLE</th>
              <th>ELEMENT</th>
              <th>BASE DE CALCUL</th>
              <th>NB/TAUX</th>
              <th>RETENUES</th>
              <th>AVOIRS</th>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
              <td style="text-align: justify">SALAIRE DE BASE</td>
	      <td>
		{{list[toChange].salaries[0].base_salary % 1 != 0 ? list[toChange].salaries[0].base_salary : parseInt(list[toChange].salaries[0].base_salary) }}
	      </td>
              <td></td>
	      <td></td>
              <td>
		{{list[toChange].salaries[0].base_salary % 1 != 0 ? list[toChange].salaries[0].base_salary : parseInt(list[toChange].salaries[0].base_salary) }}
	      </td>
	    </tr>
	    <tr v-for="prime in primes">
	      <td><el-switch v-model="allowedModel[prime.id]"></el-switch></td>
	      <td><el-switch v-model="variableModel[prime.id]"></el-switch></td>
              <td style="text-align: left">{{ prime.name.toUpperCase() }}</td>
              <td>
	        <el-form-item>
	          <el-input-number :min="0" :max="100000"
			  controls-position="right" v-model="primesModel[prime.id]"></el-input-number>
	        </el-form-item>
	      </td>
              <td></td>
              <td></td>
	      <td>{{ primesModel[prime.id] }}</td>
	    </tr>
	    <tr v-for="deduction in deductions">
	      <td><el-switch v-model="allowedModel[deduction.id]"></el-switch></td>
	      <td><el-switch v-model="variableModel[deduction.id]"></el-switch></td>
              <td style="text-align: left">{{ deduction.name.toUpperCase() }}</td>
              <td>
	        <el-form-item>
	          <el-input-number :min="0" :max="1000000"
			  controls-position="right" v-model="deductionsModel[deduction.id]"></el-input-number>
	        </el-form-item>
	      </td>
              <td></td>
              <td>{{ deductionsModel[deduction.id] }}</td>
              <td></td>
	    </tr>
	   <!-- <tr v-if="settings">
	      <td></td>
	      <td></td>
              <td style="text-align: left">COTISATION CNSS</td>
	      <td>{{ list[toChange].salaries[0].employee_contribution_rate }}</td>
              <td></td>
	      <td>{{ list[toChange].salaries[0].employee_contribution_rate }}</td>
              <td></td>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
              <td>IMPOT SUR LE REVENU</td>
	      <td>0</td>
              <td></td>
	      <td>0</td>
              <td></td>
	    </tr>
	    <tr v-if="settings">
	      <td></td>
	      <td></td>
              <td style="text-align: left">TCS</td>
	      <td>{{ list[toChange].salaries[0].payroll_tax }}</td>
              <td></td>
	      <td>{{ list[toChange].salaries[0].payroll_tax }}</td>
              <td></td>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
              <td style="text-align: left">AVANCE SUR SALAIRE</td>
	      <td>{{ advances }}</td>
              <td></td>
	      <td>{{ advances }}</td>
              <td></td>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
              <td style="text-align: left">CREDIT AU PERSONNEL</td>
	      <td>0</td>
              <td></td>
	      <td>0</td>
              <td></td>
	    </tr>
	    <tr>
              <td colspan="5"><b>TOTAUX</b></td>
	      <td>{{ totalDeductions }}</td>
	      <td>{{ totalPrimes + parseInt(list[toChange].salaries[0].base_salary) }}</td>
	    </tr>
	    <tr style="background: #aaa;">
              <td></td>
              <td></td>
              <td><b>SALAIRE BRUT</b></td>
              <td><b>RETENUES OBLIGATOIRES</b></td>
              <td><b>SALAIRE NET</b></td>
              <td><b>AUTRES RETENUES</b></td>
              <td><b>NET A PAYER</b></td>
	    </tr>
	    <tr>
	      <td></td>
	      <td></td>
              <td>{{ list[toChange].base_salary }}</td>
	      <td>{{ mandatoryDeductions }}</td>
	      <td>{{ netSalary ? netSalary : '-' }}</td>
	      <td>{{ otherDeductions }}</td>
	      <td>{{ netToPay }}</td>
	    </tr>-->
	  </table>
	  <el-form-item label="Raison de la modification (oblibatoire)">
	    <el-input type="textarea" v-model="modificationReason"></el-input>
	  </el-form-item>
	  <el-button type="primary" @click="modifySalary">Modifier</el-button>
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


    <el-table-column min-width="70px" label="vérifié">
      <template slot-scope="scope">
	      <div style="text-align: center">
        <span v-if= 'scope.row.salaries[0].reviewed'><i class="el-icon-check"></i></span>
        <span v-if= '!scope.row.salaries[0].reviewed'><i class="el-icon-close"></i></span>
	</div>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="success" @click="openShowDialog(scope.$index)">
	  consulter
	</el-button>
	<el-button size="mini" type="danger" 
		@click="openModifyDialog(scope.$index)">
	  modifier
	</el-button>
	<a class="el-button el-button--primary el-button--mini" :href="printSingleUrl(scope.$index)" 
           target="_blank">
	  imprimer
	</a>
	</el-button>
      </template>
    </el-table-column>

  </el-table>
    </el-row>
  </div>
</template>

<script>
import { Message } from 'element-ui'
import { query } from '@/api/api'
import { requestUrl } from '@/utils/helpers'
import Vue from 'vue'

export default {
  data() {
    return {
      list: null,
      primes: [],
      deductions: [],
      primesModel: {},
      deductionsModel: {},
      allowedModel: {},
      variableModel: {},
      selected: [],
      toChange: null, // index of the element to modify
      toShow: null, // index of the element to show
      settings: null,
      loading: false,
      addDialog: false,
      modifyDialog: false,
      downloadAllLink: '',
      showDialog: false,
      netToPay: 0,
      totalAvoir: 0,
      value: '',
      month: '',
      year: '',
      modificationReason: '',
      dateOptions: {
        disabledDate(time) {
          return time.getTime() > Date.now()
        }
      },
      dateForm: {
        date: null,
        startdDate: null,
        endDate: null,
        nbOpenDays: 0,
        holidays: []
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
    this.getSettings()
  },
  computed: {
    totalPrimes() {
      let total = 0
      for (const p of this.primes) {
        total += parseInt(this.primesModel[p.id])
      }
      return total
    },
    totalDeductions() {
      let total = 0
      for (const d of this.deductions) {
        total += parseInt(this.deductionsModel[d.id])
      }
      return total
    },
    netSalary() {
      let baseSalary = 0
      if (this.toChange == null) baseSalary = parseFloat(this.list[this.toShow].base_salary)
      else baseSalary = parseFloat(this.list[this.toChange].base_salary)
      return baseSalary + this.totalPrimes - this.totalDeductions
    },
    advances() {
      let total = 0
      let emp = this.list[this.toShow === null ? this.toChange : this.toShow]
      for (let a of emp.advances) {
        total += parseInt(a.amount);
      }
      return total
    },
    mandatoryDeductions() {
      let emp = this.list[this.toShow === null ? this.toChange : this.toShow]
      return emp.payroll_tax
    },
    otherDeductions() {
      let emp = this.list[this.toShow === null ? this.toChange : this.toShow]
      return this.advances
    }
  },
  methods: {
    /* getEmployees() {
      if (this.dateForm.date !== null) {
        this.year = this.dateForm.date.getFullYear()
        this.month = this.dateForm.date.getMonth() + 1
        query('employee/allwithsalary/' + this.month + '/' + this.year, 'GET').then((res) => {
          this.list = res.data
        })
      }
    }, */
    getSettings() {
      query('settings', 'GET').then((res) => {
        console.log(res.data)
        this.settings = res.data
      })
    },
    getElements() {
      query('element/primes', 'GET').then((res) => {
        this.primes = res.data
        for (const p of this.primes) {
          Vue.set(this.primesModel, p.id, 0)
          Vue.set(this.allowedModel, p.id, 0)
        }
        console.log('primes model ', this.primesModel)
        console.log(res.data)
      })
      query('element/deductions', 'GET').then((res) => {
        this.deductions = res.data
        for (const d of this.deductions) {
          Vue.set(this.deductionsModel, d.id, 0)
          Vue.set(this.allowedModel, p.id, 0)
        }
        console.log(res.data)
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    initializeElementsModel() { 
      for (const p of this.primes) {
        this.primesModel[p.id] = 0
      }
      for (const d of this.deductions) {
        this.deductionsModel[d.id] = 0
      }
    },
    handleCloseShow(done) {
      this.toShow = null
      done()
    },
    openShowDialog(index) {
      this.toChange = null
      this.toShow = index
      const toShow = this.list[index]
      const elements = toShow.salaries[0].elements
      console.log('elements : ', elements)
      this.initializeElementsModel() 
      for (const e of elements) {
        console.log('looping')
        if (e.type === '+') {
          this.primesModel[e.id] = e.pivot.value
        } else if (e.type === '-') {
          this.deductionsModel[e.id] = e.pivot.value
        }
        Vue.set(this.allowedModel, e.id, e.pivot.allowed)
        Vue.set(this.variableModel, e.id, e.pivot.variable)
      }
      this.showDialog = true
    },
    openModifyDialog(index) {
      this.toShow = null
      this.toChange = index
      const toChange = JSON.parse(JSON.stringify(this.list[index]))
      const elements = toChange.salaries[0].elements
      this.initializeElementsModel() 
      for (const e of elements) {
        console.log('looping', e)
        if (e.type === '+') {
          this.primesModel[e.id] = e.pivot.value
        } else if (e.type === '-') {
          this.deductionsModel[e.id] = e.pivot.value
        }
        this.allowedModel[e.id] = e.pivot.allowed == 1 ? true : false
        this.variableModel[e.id] = e.pivot.variable == 1 ? true : false
      }
      console.log("ALLOWED: ", JSON.stringify(this.allowedModel))
      console.log("VARIABLE: ", JSON.stringify(this.variableModel))
      this.modifyDialog = true
    },
    printSingleUrl(index) {
      // Message({ message: 'impression en cours', type: 'success', duration: 5000 })
      const id = this.list[index].id 
      const url = 'payroll/print/' + id + '/' + this.dateForm.startDate + '/' + this.dateForm.endDate
      return requestUrl(url)
    },
    printSingleUrlWeb(index) {
      // Message({ message: 'impression en cours', type: 'success', duration: 5000 })
      const id = this.list[index].id 
      const url = 'payroll/printweb/' + id + '/' + this.dateForm.startDate + '/' + this.dateForm.endDate
      return requestUrl(url)
    },
    loadPayroll() {
      //console.log('holidays: ', this.holidays)
      const data = {
	'start': this.dateForm.startDate,
	'end': this.dateForm.endDate,
	'holidays': this.dateForm.holidays
      }
      console.log('data: ', JSON.stringify(data))
      // console.log("data : ", JSON.stringify(data))
      if (this.dateForm.startDate !== null && this.dateForm.endDate !== null) {
	this.downloadAllLink = requestUrl('payroll/printall/' + data.start + '/' + data.end)
        query('payroll/get', 'POST' , data).then((res) => {
	  console.log(res)
	  query('employee/allwithsalary/' + data.start + '/' + data.end, 'GET').then((res) => {
            this.list = res.data
            console.log(res)
          })
        })
      }
    },
    validate() {
      const toShow = this.list[this.toShow]
      if (!toShow.salaries[0].reviewed) {
        query('payroll/validate/' + toShow.salaries[0].id, 'GET').then((res) => {
	  this.showDialog = false
          if (res.data.success) {
            console.log('validated')
            toShow.salaries[0].reviewed = true
	  } else {
            console.log(res.data.message)
	  }
        })
      }
    },
    recompute() {
      const data = {
	'start': this.dateForm.startDate,
	'end': this.dateForm.endDate,
	'holidays': this.dateForm.holidays
      }
      query('payroll/recompute', 'POST', data).then((res) => {
        this.loadPayroll()
      });
    },
    modifySalary() {
      const data = {
        "primes": this.primesModel,
        "deductions": this.deductionsModel,
	"variable": this.variableModel,
	"allowed": this.allowedModel,

	"reason": this.modificationReason
      }
      const id = this.list[this.toChange].salaries[0].id
      console.log(JSON.stringify(data))
      query('payroll/modify/' + id, 'POST', { "data": JSON.stringify(data) }).then((res) => {
	const ans = res.data[0]
        if ('success' in ans) {
          console.log(ans)
	} else {
          // update modified element
	  Vue.set(this.list[this.toChange].salaries, 0, ans)
	}
        console.log(res)
      })
    }
  }
}
</script>
<style rel="stylesheet/scss" lang="scss" scoped>
.paySheetTable {
  margin: 5px;
  color: #000;
}

.paySheetTable table, th, td  {
  border: 1px solid black;
  border-collapse: collapse;
  margin: auto;
}

.paySheetTable th, td {
  text-align: center;
}
</style>
