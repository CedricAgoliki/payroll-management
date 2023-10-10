<template>
  <div>
   <h2>Types de congés</h2>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button>
      <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />
    <el-row>
      
    <!-- add Dialogs -->
    <el-dialog title="Ajouter un type de congés" :visible.sync="addDialog">
      <el-form :model="leaveTypeForm">
	<el-form-item label="Nom*">
	  <el-input v-model='leaveTypeForm.name'></el-input>
	</el-form-item>

	<el-form-item label="Deduire">
	  <el-switch v-model='leaveTypeForm.deduct'
	          active-color="#13ce66"
	          inactive-color="#ff4949"
		  active-text="Oui"
		  inactive-text="Non"></el-switch>
	</el-form-item>

	<el-form-item label="Description*">
	  <el-input type="textarea" v-model='leaveTypeForm.description'></el-input>
	</el-form-item>

	<el-button type="primary" @click="add">Ajouter</el-button>
      </el-form>
    </el-dialog>


    <!-- modify Dialogs -->
    <el-dialog title="Modifier une prime" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
        <el-form :model="leaveTypeForm">
	  <el-form-item label="Nom*">
	    <el-input v-model='toChange.label'></el-input>
	  </el-form-item>

	<el-form-item label="Deduire">
	  <el-switch v-model='toChange.deduct'
	          active-color="#13ce66"
	          inactive-color="#ff4949"
		  active-text="Oui"
		  inactive-text="Non"></el-switch>
	</el-form-item>

	  <el-form-item label="Description*">
	    <el-input type="textarea" v-model='toChange.description'></el-input>
	  </el-form-item>

	  <el-button type="primary" @click="modify">Valider</el-button>
        </el-form>
      </div>

    </el-dialog>


  <el-table :data="list" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column min-width="300px" label="Libellé">
      <template slot-scope="scope">
        <span>{{ scope.row.label }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column width="100px" label="Deductible">
      <template slot-scope="scope">
        <span v-if="scope.row.deduct">Oui</span>
        <span v-else>Non</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Description">
      <template slot-scope="scope">
	<span>{{ scope.row.description == null ? noDescription : scope.row.description }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="primary" 
		@click="toChange = JSON.parse(JSON.stringify(scope.row)); modifyDialog = true" >
	  Modifier
	</el-button>
	<el-button size="mini" type="danger" @click="deleteSingle(scope.row)">Supprimer</el-button>
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
      noDescription: 'Aucune description',
      modifyDialog: false,
      value: '',
      leaveTypeForm: {
        name: '',
        description: '',
        deduct: false
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('leavetype/list', 'GET').then((res) => {
        console.log(res)
	for (let i = 0; i < res.data.length; i++) {
          if (res.data[i].deduct === 1) {
            res.data[i].deduct = true
          } else {
            res.data[i].deduct = false
          }
	}
        this.list = res.data
      })
    },
    add() {
      this.addDialog = false
      const type = {
        'label': this.leaveTypeForm.name,
        'description': this.leaveTypeForm.description,
        'deduct': this.leaveTypeForm.deduct
      }
      query('leavetype/new', 'POST', type).then((res) => {
        this.leaveTypeForm.name = ''
        this.leaveTypeForm.description = ''
        this.leaveTypeForm.deduct = true
        if (res.data.success) this.getAll()
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    modify() {
      this.modifyDialog = false
      console.log(this.toChange)
      query('leavetype/modify/' + this.toChange.id, 'POST', this.toChange).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    deleteSelected() {
      const data = { ids: [] }
      for (const property of this.selected) data.ids.push(property.id)
      data.ids = JSON.stringify(data.ids)
      query('leavetype/massdelete', 'POST', data).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    deleteSingle(row) {
      query('leavetype/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAll()
      })
    }
  }
}
</script>

