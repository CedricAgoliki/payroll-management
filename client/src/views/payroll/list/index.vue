<template>
  <div style="padding: 30px">
    <el-row>
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog title="Modifier une propriété" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
        <el-form :model="propertyForm">
	  <el-form-item label="Nom">
	    <el-input v-model='toChange.last_name'></el-input>
	  </el-form-item>

	  <el-form-item label="Prenom">
	    <el-input v-model='toChange.first_name'></el-input>
	  </el-form-item>

	  <el-button type="primary" @click="modify">Valider</el-button>
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
      modifyDialog: false,
      value: '',
      propertyForm: {
        name: '',
        description: '',
        regex: ''
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('employee/all', 'GET').then((res) => {
        this.list = res.data
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    modify(row) {
      this.modifyDialog = false
      query('employee/modify/' + this.toChange.id, 'POST', this.toChange).then((res) => {
        if (res.data.success) this.getAll()
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

