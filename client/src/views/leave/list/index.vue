<template>
  <div style="padding: 30px">
      <!--<el-row>-->
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <!--<el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />-->
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog title="Confirmation" :visible.sync="confirmDialog">
      <div v-if="toChange !== null">
	<h3>Etes vous sur?</h3>
        <el-form :model="leaveForm" label-position="top">

         <el-form-item label="Date d'annulation (obligatoire)">
	    <el-date-picker type="date"
		            v-model="leaveForm.date"
			    format="dd-MM-yyyy"
			    value-format="yyyy-MM-dd"></el-date-picker>
	  </el-form-item>

         <el-form-item label="Saisissez la raison de l'annulation (obligatoire)">
	    <el-input type="textarea" v-model="leaveForm.reason"></el-input>
	  </el-form-item>
	<el-button type="danger" @click="modify">Valider</el-button>
	<el-button type="primary" @click="confirmDialog = false">Annuler</el-button>
        </el-form>
      </div>

    </el-dialog>

  <h1>Personnel en congé</h1>
  <el-table :data="listCurrent" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column label="Nom">
      <template slot-scope="scope">
        <span>{{ scope.row.employee.last_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Prenoms">
      <template slot-scope="scope">
        <span>{{ scope.row.employee.first_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Debut">
      <template slot-scope="scope">
        <span>{{ scope.row.start | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Fin">
      <template slot-scope="scope">
        <span>{{ scope.row.end | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Date de reprise">
      <template slot-scope="scope">
        <span>{{ nextBusinessDay(scope.row.end) | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="danger" @click="confirmDialog = true;toChange = scope.row">
	  Annuler
	</el-button>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

  </el-table>


  <h1>Personnel bientot en congé</h1>
  <el-table :data="listFuture" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column label="Nom">
      <template slot-scope="scope">
        <span>{{ scope.row.employee.last_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Prenoms">
      <template slot-scope="scope">
        <span>{{ scope.row.employee.first_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Debut">
      <template slot-scope="scope">
        <span>{{ scope.row.start | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Fin">
      <template slot-scope="scope">
        <span>{{ scope.row.end | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Date de reprise">
      <template slot-scope="scope">
        <span>{{ nextBusinessDay(scope.row.end) | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="danger" @click="confirmDialog = true;toChange = scope.row">
	  Annuler
	</el-button>
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
      listCurrent: null,
      listFuture: null,
      selected: [],
      toChange: null,
      loading: false,
      addDialog: false,
      confirmDialog: false,
      value: '',
      leaveForm: {
        date: null,
        reason: ''
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('leave/current', 'GET').then((res) => {
        console.log(res.data)
        this.listCurrent = res.data
      })
      query('leave/future', 'GET').then((res) => {
        console.log(res.data)
        this.listFuture = res.data
      })
    },
    modify() {
      const data = {
        date: this.leaveForm.date,
        reason: this.leaveForm.reason
      }
      query('leave/delete/' + this.toChange.id, 'POST', data).then((res) => {
	      console.log(res.data)
	if (res.data.success) {
          this.confirmDialog = false
          this.getAll()
	}
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    nextBusinessDay(dateString) {
      const d = new Date(dateString)
      d.setDate(d.getDate() + 1)
      while (d.getDay() == 6 || d.getDay() == 0) {
        d.setDate(d.getDate() + 1)
      }
      return d
    }
  }
}
</script>

