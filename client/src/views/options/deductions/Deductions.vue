<template>
  <div>
   <h2>Autres retenues</h2>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button>
      <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />
    <el-row>
      
    <!-- add Dialogs -->
    <el-dialog title="Ajouter une nouvelle retenue" :visible.sync="addDialog">
      <el-form :model="elementForm">
	<el-form-item label="Nom*">
	  <el-input v-model='elementForm.name'></el-input>
	</el-form-item>

	<el-form-item label="Calcul au prorata">
	  <el-switch v-model='elementForm.prorata'
	          active-color="#13ce66"
	          inactive-color="#ff4949"
		  active-text="Oui"
		  inactive-text="Non"></el-switch>
	</el-form-item>

	<el-form-item label="Description*">
	  <el-input type="textarea" v-model='elementForm.description'></el-input>
	</el-form-item>

	<el-button type="primary" @click="add">Ajouter</el-button>
      </el-form>
    </el-dialog>


    <!-- modify Dialogs -->
    <el-dialog title="Modifier une retenue" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
        <el-form :model="elementForm">
	  <el-form-item label="Nom*">
	    <el-input v-model='toChange.name'></el-input>
	  </el-form-item>

	<el-form-item label="Calcul au prorata">
	  <el-switch v-model='toChange.prorata'
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

    <el-table-column min-width="300px" label="Calculé au prorata">
      <template slot-scope="scope">
	<span>{{ scope.row.prorata ? "OUI" : "NON" }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="primary" @click="changeSingle(scope.row)">Modifier</el-button>
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
      elementForm: {
        name: '',
        description: '',
        prorata: true
      }
    }
  },
  created() {
    [].dfs
    this.getAll()
  },
  methods: {
    getAll() { 
      query('element/deductions', 'GET').then((res) => {
        console.log(res)
        this.list = res.data
        // this.list = res.data
      })
    },
    add() {
      this.addDialog = false
      const element = {
        'name': this.elementForm.name,
        'description': this.elementForm.description,
        'prorata': this.elementForm.prorata,
        'type': '-'
      }
      query('element/new', 'POST', element).then((res) => {
        this.elementForm.name = ''
        this.elementForm.description = ''
        if (res.data.success) this.getAll()
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    modify(row) {
      this.modifyDialog = false
      query('element/modify/' + this.toChange.id, 'POST', this.toChange).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    deleteSelected() {
      const data = { ids: [] }
      for (const property of this.selected) data.ids.push(property.id)
      data.ids = JSON.stringify(data.ids)
      query('element/massDelete', 'POST', data).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    changeSingle(row) {
      this.toChange = JSON.parse(JSON.stringify(row))
      this.toChange.prorata = this.toChange.prorata == 1 ? true: false
      this.modifyDialog = true
    },
    deleteSingle(row) {
      query('element/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAll()
      })
    }
  }
}
</script>

