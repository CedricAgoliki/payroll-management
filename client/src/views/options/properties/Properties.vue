<template>
  <div>
   <h2>Propriété</h2>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button>
      <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />
    <el-row>
      
    <!-- add Dialogs -->
    <el-dialog title="Ajouter une nouvelle propriété" :visible.sync="addDialog">
      <el-form :model="propertyForm">
	<el-form-item label="Nom*">
	  <el-input v-model='propertyForm.name'></el-input>
	</el-form-item>

	<el-form-item label="Description*">
	  <el-input type="textarea" v-model='propertyForm.description'></el-input>
	</el-form-item>

	<el-button type="primary" @click="add">Ajouter</el-button>
      </el-form>
    </el-dialog>


    <!-- modify Dialogs -->
    <el-dialog title="Modifier une propriété" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
        <el-form :model="propertyForm">
	  <el-form-item label="Nom*">
	    <el-input v-model='toChange.name'></el-input>
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
        <span>{{scope.row.name}}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Description">
      <template slot-scope="scope">
        <span>{{scope.row.description}}</span>
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
    this.getAllProperties()
  },
  methods: {
    getAllProperties() {
      query('property/all', 'GET').then((res) => {
        this.list = res.data
      })
    },
    add() {
      this.addDialog = false
      const property = {
        'name': this.propertyForm.name,
        'description': this.propertyForm.description,
      }
      query('property/new', 'POST', property).then((res) => {
        this.propertyForm.name = ''
        this.propertyForm.description = ''
        this.propertyForm.regex = ''
        if (res.data.success) this.getAllProperties()
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    modify(row) {
      this.modifyDialog = false
      query('property/modify/' + this.toChange.id, 'POST', this.toChange).then((res) => {
        if (res.data.success) this.getAllProperties()
      })
    },
    deleteSelected() {
      const data = { ids: [] }
      for (const property of this.selected) data.ids.push(property.id)
      data.ids = JSON.stringify(data.ids)
      query('property/massDelete', 'POST', data).then((res) => {
        if (res.data.success) this.getAllProperties()
      })
    },
    deleteSingle(row) {
      query('property/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAllProperties()
      })
    }
  }
}
</script>

