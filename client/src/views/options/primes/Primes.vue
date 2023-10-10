<template>
  <div>
   <h2>Autres primes</h2>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button>
      <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />
    <el-row>
      
    <!-- add Dialogs -->
    <el-dialog title="Ajouter une nouvelle prime" :visible.sync="addDialog">
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

	<el-form-item label="Inclure dans le calcul de l'IRPP">
	  <el-switch v-model='elementForm.irpp'
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
    <el-dialog title="Modifier une prime" :visible.sync="modifyDialog">
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

	<el-form-item label="Inclure dans le calcul de l'IRPP">
	  <el-switch v-model='toChange.irpp'
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
        <span>{{ scope.row.name }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Description">
      <template slot-scope="scope">
	<span>{{ scope.row.description == null ? noDescription : scope.row.description }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Calculé au prorata">
      <template slot-scope="scope">
	<span>{{ scope.row.prorata ? "OUI" : "NON" }}</span>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Inclus dans le Calcul du IRPP">
      <template slot-scope="scope">
	<span>{{ scope.row.irpp ? "OUI" : "NON" }}</span>
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
    <el-row>
      <h2>Prime d'ancienneté</h2>
	  <!-- add to the scale dialog -->
	  <el-dialog title="Interval de la prime d'ancienneté" :visible.sync="addScaleDialog">
	    <div>
	      <el-form :model="scaleForm" label-position="top">
	        <el-form-item label="intervalle d'année">
		  <el-col :span="11">
		    <el-input  v-model="scaleForm.from" placeholder="de"></el-input>
		  </el-col>

		  <el-col :span="2">-</el-col>

	          <el-col :span="11">
		    <el-input v-model="scaleForm.to" placeholder="à"></el-input>
		  </el-col>
	        </el-form-item>

	       <el-form-item label="Type">
		  <el-select v-model="scaleForm.type">
		    <el-option label="Fixe" value="fixed"></el-option>
		    <el-option label="Incrementiel" value="incremental"></el-option>
		  </el-select>
	       </el-form-item>

	       <el-form-item label="Taux">
		  <el-input v-model="scaleForm.rate">
		    <template slot="append">%</template>
		  </el-input>
	       </el-form-item>


	      <el-button type="success" @click="addScale">Ajouter</el-button>
	      <el-button type="primary" @click="addScaleDialog = false">Annuler</el-button>
	      </el-form>
	    </div>
	  </el-dialog>
          <div>
            <el-button type="primary" @click="addScaleDialog = true">Ajouter</el-button>
	  </div>
          <el-table :data="scaleList">
            <el-table-column label="de">
	      <template slot-scope="scope">
		<span>{{ scope.row.from }}</span>
	      </template>
	    </el-table-column>

            <el-table-column label="à">
	      <template slot-scope="scope">
		<span>{{ scope.row.to }}</span>
	      </template>
	    </el-table-column>

            <el-table-column label="Taux">
	      <template slot-scope="scope">
		<span>{{ scope.row.rate + ' %' }}</span>
	      </template>
	    </el-table-column>

            <el-table-column label="Type">
	      <template slot-scope="scope">
		<span v-if="scope.row.type === 'fixed'">Fixe</span>
		<span v-if="scope.row.type === 'incremental'">Incrementiel</span>
	      </template>
	    </el-table-column>

            <el-table-column label="Opérations">
	      <template slot-scope="scope">
		<el-button size="mini" type="danger" @click="deleteScale(scope.$index)">supprimer</el-button>
		<el-button size="mini" type="success" icon="el-icon-arrow-up"
		  v-if="scope.$index > 0"
		  @click="scaleUp(scope.$index)"></el-button>
		<el-button size="mini" type="primary" icon="el-icon-arrow-down"
		  v-if="scope.$index < scaleList.length - 1"
		  @click="scaleDown(scope.$index)"></el-button>
		<!-- <el-tag>{{scope.row.name}}</el-tag> -->
	      </template>
	    </el-table-column>

	  </el-table>
          <el-button type="primary" @click="modifyBonus">Enregistrer la prime d'ancienneté</el-button>
    </el-row>
  </div>
</template>

<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  data() {
    return {
      list: null,
      selected: [],
      toChange: null,
      loading: false,
      addDialog: false,
      addScaleDialog: false,
      noDescription: 'Aucune description',
      modifyDialog: false,
      value: '',
      scaleList: [],
      scaleForm: {
	from: '',
	to: '',
	rate: '',
	type: ''
      },
      elementForm: {
        name: '',
        description: '',
        prorata: true,
        irpp: true
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('element/primes', 'GET').then((res) => {
        console.log(res)
        this.list = res.data
        // this.list = res.data
      })
      query('settings', 'GET').then((res) => {
        console.log(res)
	this.scaleList = JSON.parse(res.data.seniority_bonus)
      })
    },
    add() {
      this.addDialog = false
      const element = {
        'name': this.elementForm.name,
        'description': this.elementForm.description,
        'prorata': this.elementForm.prorata,
        'irpp': this.elementForm.irpp,
        'type': '+'
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
      console.log(JSON.stringify(this.toChange))
      query('element/modify/' + this.toChange.id, 'POST', this.toChange).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    changeSingle(row) {
      this.toChange = JSON.parse(JSON.stringify(row))
      this.toChange.prorata = this.toChange.prorata == 1 ? true: false
      this.modifyDialog = true

    },
    deleteSelected() {
      const data = { ids: [] }
      for (const property of this.selected) data.ids.push(property.id)
      data.ids = JSON.stringify(data.ids)
      query('element/massDelete', 'POST', data).then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    deleteSingle(row) {
      query('element/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAll()
      })
    },
    addScale() {
      const scale = {
	from : this.scaleForm.from,
	to : this.scaleForm.to,
	rate : this.scaleForm.rate,
	type : this.scaleForm.type
      }
      if (this.scaleList === null) this.scaleList = []
      this.scaleList.push(scale)
      this.addScaleDialog = false
    },
    deleteScale(index) {
      this.scaleList.splice(index, 1)
    },
    scaleUp(index) {
      const tmp = this.scaleList[index]
      this.scaleList.splice(index,1,this.scaleList[index - 1])
      this.scaleList.splice(index - 1,1, tmp)
    },
    scaleDown(index) {
      const tmp = this.scaleList[index]
      this.scaleList.splice(index,1,this.scaleList[index + 1])
      this.scaleList.splice(index + 1,1, tmp)
    },
    modifyBonus() {
      const data = JSON.stringify(this.scaleList)
      query('settings/seniority', 'POST', {'sen' : data}).then((res) => {
        if (res.data.success) {
	  this.getAll()
          Message({
	    message: "Bareme de la prime d'ancienneté bien enregistré",
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: "Bareme non enregisté, une erreur s'est produite",
            type: 'error',
            duration: 5000
          })
        }
      })
      console.log("saving")
    }
  }
}
</script>

