<template>
  <div style="padding: 30px">
      <!--<el-row>-->
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <!--<el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />-->
    <h1>Liste des avances du mois</h1>
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog title="Modifier une propriété" :visible.sync="confirmDialog">
      <div v-if="toChange !== null">
	<h3>Etes vous sur?</h3>
        <el-form :model="cancelForm" label-position="top">

         <el-form-item label="Date d'annulation (obligatoire)">
	    <el-date-picker type="date"
		            v-model="cancelForm.date"
			    format="dd-MM-yyyy"
			    value-format="yyyy-MM-dd"></el-date-picker>
	  </el-form-item>

         <el-form-item label="Saisissez la raison de l'annulation (obligatoire)">
	    <el-input type="textarea" v-model="cancelForm.reason"></el-input>
	  </el-form-item>
	<el-button type="danger" @click="cancel">Valider</el-button>
	<el-button type="primary" @click="confirmDialog = false">Annuler</el-button>
        </el-form>
      </div>
    </el-dialog>


  <el-table :data="list" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column label="Nom">
      <template slot-scope="scope">
        <span>{{scope.row.employee.last_name}}</span>
      </template>
    </el-table-column>

    <el-table-column label="Prenoms">
      <template slot-scope="scope">
        <span>{{scope.row.employee.first_name}}</span>
      </template>
    </el-table-column>

    <el-table-column label="Date">
      <template slot-scope="scope">
        <span>{{scope.row.date | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Montant">
      <template slot-scope="scope">
        <span>{{scope.row.amount}}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="danger" @click="confirmDialog = true;toChange = scope.row">Annuler</el-button>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
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
      selected: [],
      toChange: null,
      loading: false,
      addDialog: false,
      confirmDialog: false,
      value: '',
      cancelForm: {
        date: null,
        reason: '',
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('advance/listactual', 'GET').then((res) => {
        console.log(res.data)
        this.list = res.data
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    modifyValues(row) {
      this.toChange = JSON.parse(JSON.stringify(row))
      console.log('change : ', this.toChange)
      this.modifyDialog = true
      this.employeeForm.firstName = this.toChange.first_name
      this.employeeForm.lastName = this.toChange.last_name
      this.employeeForm.baseSalary = this.toChange.base_salary
      this.employeeForm.leavesPerMonth = this.toChange.leaves_per_month
      this.employeeForm.dependants = this.toChange.dependants
      this.employeeForm.contractStart = this.toChange.contracts[0].start
      this.employeeForm.contractEnd = this.toChange.contracts[0].end
      this.employeeForm.contractCommentary = this.toChange.contracts[0].commentary
      console.log('properties : ')
      for (const p of this.toChange.properties) {
        console.log(JSON.stringify(p))
        this.employeeForm.properties[p.pivot.property_id] = p.pivot.value
      }
    },
    cancel() {
      console.log(this.toChange)
      const data = {
        "date": this.cancelForm.date,
	"reason": this.cancelForm.reason
      }
      console.log(JSON.stringify(this.toChange.id))
      query('advance/delete/' + this.toChange.id, 'POST', data).then((res) => {
	if (res.data.success) {
          this.confirmDialog = false;
          this.getAll()
         }
      })
    },
    deleteSelected() {
      const data = { ids: [] }
      for (const property of this.selected) data.ids.push(property.id)
      data.ids = JSON.stringify(data.ids)
      query('employee/massDelete', 'POST', data).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    deleteSingle(row) {
      query('employee/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAll()
      })
    }
  }
}
</script>

