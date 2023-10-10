<template>
  <div style="padding: 30px">
    <h2>Groupes de paie</h2>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="openAddDialog"></el-button>
    </el-row>


    <!-- new group Dialogs -->
    <el-dialog title="Nouveau groupe de paie" :visible.sync="addDialog" width="75%">
      <el-form :model="groupForm">
	<el-tabs tab-position="left">
	  <el-tab-pane label="Informations">
	    <el-form-item label="Nom du groupe">
	      <el-input v-model="groupForm.name"></el-input>
	    </el-form-item>
	    <el-form-item>
	      <el-row :gutter="4">
	      <el-col :span="12">
		<el-table border :data="employeeList" fit highlight-current-row style="width:100%" 
		  height="250">
		  <el-table-column align="center" label="Employée sans groupe">
		    <template slot-scope="scope">
		      <span>{{scope.row.last_name + ' ' + scope.row.first_name}}</span>
		    </template>
		  </el-table-column>
		  <el-table-column align="center" label="O">
		    <template slot-scope="scope">
		      <el-button type="success" @click="addMember(scope.$index)">+</el-button>
		    </template>
		  </el-table-column>
		</el-table>
	      </el-col>
	      <el-col :span="12">
		<el-table border :data="groupMembers" fit highlight-current-row style="width:100%" 
		  height="250">
		  <el-table-column align="center" label="Membres du groupe">
		    <template slot-scope="scope">
		      <span>{{scope.row.last_name + ' ' + scope.row.first_name}}</span>
		    </template>
		  </el-table-column>
		  <el-table-column align="center" label="">
		    <template slot-scope="scope">
		      <el-button type="danger" @click="removeMember(scope.$index)">-</el-button>
		    </template>
		  </el-table-column>
		</el-table>
	      </el-col>
	      </el-row>
	    </el-form-item>
	  </el-tab-pane>
	  <el-tab-pane label="Fiche">

	    <div class="paySheetTable">
	      <table>
		<tr>
		  <th>Accordés</th>
		  <th>Variable</th>
		  <th>Element</th>
		  <th>Base de calcul</th>
		  <th>Nb/Taux</th>
		  <th>Retenues</th>
		  <th>Avoirs</th>
		</tr>
		<tr v-for="prime in primes">
		  <td><el-switch v-model="allowedModel[prime.id]"></el-switch></td>
		  <td><el-switch v-model="variableModel[prime.id]"></el-switch></td>
		  <td>{{ prime.name.toUpperCase() }}</td>
		  <td>
		    <el-form-item>
		      <el-input-number :min="0" :max="100000" :disabled="!allowedModel[prime.id]"
			      controls-position="right" v-model="primesModel[prime.id]"></el-input-number>
		    </el-form-item>
		  </td>
		  <td>1</td>
		  <td></td>
		  <td>{{ primesModel[prime.id] }}</td>
		</tr>
		<tr>
		  <td><el-switch v-model="allowedModel['seniority']"></el-switch></td>
		  <td></td>
		  <td>PRIME D'ANCIENNETÉ</td>
		  <td></td>
		  <td>1</td>
		  <td></td>
		  <td></td>
		</tr>
		<tr v-for="deduction in deductions">
		  <td><el-switch v-model="allowedModel[deduction.id]"></el-switch></td>
		  <td><el-switch v-model="variableModel[deduction.id]"></el-switch></td>
		  <td>{{ deduction.name.toUpperCase() }}</td>
		  <td>
		    <el-form-item>
		      <el-input-number :min="0" :max="1000000"
			      controls-position="right" :disabled="!allowedModel[deduction.id]"
			      v-model="deductionsModel[deduction.id]"></el-input-number>
		    </el-form-item>
		  </td>
		  <td>1</td>
		  <td>{{ deductionsModel[deduction.id] }}</td>
		  <td></td>
		</tr>
		<tr>
		  <td colspan="4">Totaux</td>
		  <td>{{ totalDeductions }}</td>
		  <td>{{ totalPrimes }}</td>
		</tr>
	      </table>
	      </div>

	  </el-tab-pane>
	</el-tabs>
	<el-button type="success" @click="addGroup">Valider</el-button>
	<el-button type="danger" @click="addDialog = false">Annuler</el-button>
      </el-form>
    </el-dialog>


    <!-- update group Dialogs -->
    <el-dialog title="Modifier un groupe de paie" :visible.sync="updateDialog" width="75%">
      <el-form :model="groupForm" v-if="toChange != null" >
	<el-tabs tab-position="left">
	  <el-tab-pane label="Informations">
	    <el-form-item label="Nom du groupe">
	      <el-input v-model="toChange.label"></el-input>
	    </el-form-item>
	    <el-form-item>
	      <el-row :gutter="4">
	      <el-col :span="12">
		<el-table border :data="employeeList" fit highlight-current-row style="width:100%" 
		  height="250">
		  <el-table-column align="center" label="Employée sans groupe">
		    <template slot-scope="scope">
		      <span>{{scope.row.last_name + ' ' + scope.row.first_name}}</span>
		    </template>
		  </el-table-column>
		  <el-table-column align="center" label="O">
		    <template slot-scope="scope">
		      <el-button type="success" @click="addMember(scope.$index)">+</el-button>
		    </template>
		  </el-table-column>
		</el-table>
	      </el-col>
	      <el-col :span="12">
		<el-table border :data="groupMembers" fit highlight-current-row style="width:100%" 
		  height="250">
		  <el-table-column align="center" label="Membres du groupe">
		    <template slot-scope="scope">
		      <span>{{scope.row.last_name + ' ' + scope.row.first_name}}</span>
		    </template>
		  </el-table-column>
		  <el-table-column align="center" label="">
		    <template slot-scope="scope">
		      <el-button type="danger" @click="removeMember(scope.$index)">-</el-button>
		    </template>
		  </el-table-column>
		</el-table>
	      </el-col>
	      </el-row>
	    </el-form-item>
	  </el-tab-pane>
	  <el-tab-pane label="Fiche">

	    <div class="paySheetTable">
	      <table>
		<tr>
		  <th>Accordé</th>
		  <th>Variable</th>
		  <th>Element</th>
		  <th>Base de calcul</th>
		  <th>Nb/Taux</th>
		  <th>Retenues</th>
		  <th>Avoirs</th>
		</tr>
		<tr v-for="prime in primes">
		  <td><el-switch v-model="allowedModel[prime.id]"></el-switch></td>
		  <td><el-switch v-model="variableModel[prime.id]"></el-switch></td>
		  <td>{{ prime.name.toUpperCase() }}</td>
		  <td>
		    <el-form-item>
		      <el-input-number :min="0" :max="100000" :disabled="!allowedModel[prime.id]"
			      controls-position="right" v-model="primesModel[prime.id]"></el-input-number>
		    </el-form-item>
		  </td>
		  <td>1</td>
		  <td></td>
		  <td>{{ primesModel[prime.id] }}</td>
		</tr>
		<tr>
		  <td><el-switch v-model="allowedModel['seniority']"></el-switch></td>
		  <td></td>
		  <td>PRIME D'ANCIENNETÉ</td>
		  <td></td>
		  <td>1</td>
		  <td></td>
		  <td></td>
		</tr>
		<tr v-for="deduction in deductions">
		  <td><el-switch v-model="allowedModel[deduction.id]"></el-switch></td>
		  <td><el-switch v-model="variableModel[deduction.id]"></el-switch></td>
		  <td>{{ deduction.name.toUpperCase() }}</td>
		  <td>
		    <el-form-item>
		      <el-input-number :min="0" :max="1000000"
			      controls-position="right" :disabled="!allowedModel[deduction.id]"
			      v-model="deductionsModel[deduction.id]"></el-input-number>
		    </el-form-item>
		  </td>
		  <td>1</td>
		  <td>{{ deductionsModel[deduction.id] }}</td>
		  <td></td>
		</tr>
		<tr>
		  <td colspan="4">Totaux</td>
		  <td>{{ totalDeductions }}</td>
		  <td>{{ totalPrimes }}</td>
		</tr>
	      </table>
	      </div>

	  </el-tab-pane>
	</el-tabs>
	<el-button type="success" @click="updateGroup">Modifier</el-button>
	<el-button type="danger" @click="updateDialog = false">Annuler</el-button>
      </el-form>
    </el-dialog>



 
    <el-table :data="groups" border fit highlight-current-row style="width:100%">

      <el-table-column type="selection" align="center" label="ID" width="50">
      </el-table-column>

      <el-table-column min-width="300px" label="Nom">
	<template slot-scope="scope">
	  <span>{{ scope.row.label }}</span>
	</template>
      </el-table-column>
       
      <el-table-column min-width="300px" label="Opérations">
	<template slot-scope="scope">
	  <el-button size="mini" type="primary" @click="openUpdateDialog(scope.$index)">Modifier</el-button>
	  <el-button size="mini" type="danger" @click="deleteGroup(scope.$index)">Supprimer</el-button>
	</template>
      </el-table-column>

    </el-table>
  </div>
</template>

<script>
import { query } from '@/api/api'
import Vue from 'vue'

export default {
  name: 'tab',
  data() {
    return {
      labelPosition: 'top',
      labelWidth: '200px',
      addDialog: false,
      updateDialog: false,
      step: 0,
      employeeList: [],
      groups: [],
      groupMembers: [],
      primes: [],
      deductions: [],
      primesModel: {},
      deductionsModel: {},
      allowedModel: {},
      variableModel: {},
      prorataModel: {},
      settings: null,
      toChange: null,
      toShow: null,
      groupForm: {
        name: ''
      },
      properties: []
    }
  },
  computed: {
    totalPrimes() {
      let total = 0
      for (const p of this.primes) {
        total += this.primesModel[p.id]
      }
      return total
    },
    totalDeductions() {
      let total = 0
      for (const d of this.deductions) {
        total += this.deductionsModel[d.id]
      }
      return total
    }
  },
  created() {
    this.getGroups()
    this.getEmployees()
    this.getElements()
  },
  methods: {
    getGroups() {
      query('group/list', 'GET').then(res => {
        this.groups = res.data
      })
    },
    addGroup() {
      const ids = []
      for (const emp of this.groupMembers) {
        ids.push(emp.id)
      }
      const data = {
        'label': this.groupForm.name,
        'members': ids,
        'primes': this.primesModel,
        'deductions': this.deductionsModel,
	'allowed': this.allowedModel,
	'variable': this.variableModel
      }
      console.log('data : ', { 'data': JSON.stringify(data) })
      console.log('saving it')
      query('group/new', 'POST', { 'data': JSON.stringify(data) }).then(res => {
        console.log(res)
        this.addDialog = false
        this.getGroups()
        this.getEmployees()
        this.getElements()
      })
    },
    getEmployees() {
      query('employee/withoutgroup', 'GET').then(res => {
        console.log(res)
        this.employeeList = res.data
      })
    },
    addMember(index) {
      const removed = this.employeeList.splice(index, 1)
      console.log('rem: ', removed[0])
      this.groupMembers.push(removed[0])
    },
    removeMember(index) {
      const removed = this.groupMembers.splice(index, 1)
      console.log('rem: ', removed[0])
      this.employeeList.push(removed[0])
    },
    getElements() {
      query('element/primes', 'GET').then((res) => {
        this.primes = res.data
        for (const p of this.primes) {
	  Vue.set(this.primesModel, p.id, 0)
	  Vue.set(this.allowedModel, p.id, true)
	  Vue.set(this.variableModel, p.id, true)
        }
        console.log('primes model ', this.primesModel)
        console.log(res.data)
      })
      query('element/deductions', 'GET').then((res) => {
        this.deductions = res.data
        for (const d of this.deductions) {
	  Vue.set(this.deductionsModel, d.id, 0)
	  Vue.set(this.allowedModel, d.id, true)
	  Vue.set(this.variableModel, d.id, true)
        }
	Vue.set(this.allowedModel, 'seniority', true)
	Vue.set(this.variableModel, 'seniority', true)
        console.log(res.data)
      })
    },
    refreshGroup(id) {
      query('group/details/' + id, 'GET').then((res) => {
        this.toChange = res.data
        this.groupMembers = this.toChange.employees
        for (const e of this.toChange.elements) {
	  const a = e.pivot.allowed == 1 ? true : false
	  Vue.set(this.allowedModel, e.id, a)
	  const v = e.pivot.variable == 1 ? true : false
	  Vue.set(this.variableModel, e.id, v)
          if (e.type == '+') {
            this.primesModel[e.id] = e.pivot.value
	  } else {
            this.deductionsModel[e.id] = e.pivot.value
	  }
        }
	Vue.set(this.allowedModel, 'seniority', e.seniority_bonus)
      })
    },
    openUpdateDialog(index) {
      this.getEmployees();
      this.toChange = this.groups[index]
      this.refreshGroup(this.toChange.id)
      this.updateDialog = true
    },
    openAddDialog() {
      this.getEmployees()
      this.getElements()
      this.addDialog = true
    },
    deleteGroup(index) {
      const id = this.groups[index].id
      query('group/delete/' + id, 'GET').then((res) => {
        console.log(res)
        this.getGroups()
        this.getEmployees()
        this.getElements()
      })
    },
    updateGroup() {
      console.log('updating')
      const ids = []
      for (const emp of this.groupMembers) {
        ids.push(emp.id)
      }
      const data = {
        'groupId': this.toChange.id,
        'label': this.toChange.label,
        'members': ids,
        'primes': this.primesModel,
        'deductions': this.deductionsModel,
	'allowed': this.allowedModel,  
	'variable': this.variableModel
      }
      console.log('data : ', { 'data': JSON.stringify(data) })
      console.log('saving it')
      query('group/update', 'POST', { 'data': JSON.stringify(data) }).then(res => {
        console.log(res)
        this.getGroups()
        this.getEmployees()
        this.refreshGroup(this.toChange.id)
      })
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

  .paySheetTable {
    margin: 5px;
  }

  .paySheetTable table, th, td  {
    border: 1px solid black;
    border-collapse: collapse;
    margin: 0 auto;
  }

  .paySheetTable th, td {
    text-align: center;
  }
</style>
