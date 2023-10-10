<template>
  <div>
   <h2>Impots et CNSS</h2>
    <el-row>
      <el-tabs>
        <el-tab-pane label="TCS">

          <el-form :model="tcsForm">
            <el-form-item label="IRPP = 0">
              <el-input v-model="tcsForm.noirpp"></el-input>
	    </el-form-item>
	    <el-form-item label="IRPP > 0">
              <el-input v-model="tcsForm.irpp"></el-input>
	    </el-form-item>
	  </el-form>
	<el-button type="primary" @click="modifyTcs">Enregistrer</el-button>

	</el-tab-pane>

        <el-tab-pane label="IRPP">
	<h4>Bareme de l'IRPP</h4>
	  <!-- add to the scale dialog -->
	  <el-dialog title="Ajouter au bareme IRPP" :visible.sync="addDialog">
	    <div>
	      <el-form :model="scaleForm" label-position="top">

	       <el-form-item label="De">
		  <el-input  v-model="scaleForm.from"></el-input>
		</el-form-item>

	       <el-form-item label="A">
		  <el-input v-model="scaleForm.to"></el-input>
		</el-form-item>

	       <el-form-item label="Taux">
		  <el-input v-model="scaleForm.rate">
		    <template slot="append">%</template>
		  </el-input>
		</el-form-item>

	      <el-button type="success" @click="addScale">Ajouter</el-button>
	      <el-button type="primary" @click="addDialog = false">Annuler</el-button>
	      </el-form>
	    </div>
	  </el-dialog>

          <div>
            <el-button type="primary" @click="addDialog = true">Ajouter</el-button>
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
          <el-button type="primary" @click="modifyIrpp">Enregistrer</el-button>
          
	</el-tab-pane>

        <el-tab-pane label="CNSS">
      	  <el-form :model="cnssForm">
	    <el-form-item label="Taux des cotisations patronales">
	      <el-input v-model='cnssForm.employerRate'>
		<template slot="append">%</template>
	      </el-input>
	    </el-form-item>

	    <el-form-item label="Taux des cotisations ouvrieres">
	      <el-input v-model='cnssForm.employeeRate'>
		<template slot="append">%</template>
	      </el-input>
	    </el-form-item>

	    <el-button type="primary" @click="modifyCnss">Enregistrer</el-button>

	  </el-form>
          
	</el-tab-pane>

        <el-tab-pane label="Autres">

      <el-form :model="settingsForm">
	<el-form-item label="Plafond personne à charge">
	  <el-input v-model='settingsForm.maxDependants'></el-input>
	</el-form-item>

	<el-form-item label="Montant mensuelle par personne à charge">
	  <el-input v-model='settingsForm.sumPerDependants'></el-input>
	</el-form-item>

	<el-form-item label="Pourcentage des frais personelles">
	  <el-input v-model='settingsForm.percentagePersonalFees'>
	    <template slot="append">%</template>
	  </el-input>
	</el-form-item>

	<el-form-item label="Seuil">
	  <el-input v-model='settingsForm.threshold'></el-input>
	</el-form-item>


	<el-form-item label="Pourcentage pour montant depassant le seuil">
	  <el-input v-model='settingsForm.percentageAfterThreshold'>
	    <template slot="append">%</template>
	  </el-input>
	</el-form-item>

	<el-button type="primary" @click="modify">Enregistrer</el-button>
      </el-form>
	</el-tab-pane>
      </el-tabs>

    </el-row>
  </div>
</template>

<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  data() {
    return {
      settingsForm: {
        maxDependants: '',
        sumPerDependants: '',
        percentagePersonalFees: '',
        threshold: '',
        percentageAfterThreshold: '',
      },
      tcsForm: {
        irpp: '',
        noirpp: ''
      },
      cnssForm: {
	employerRate: '',
	employeeRate: ''
      },
      scaleForm: {
	from: '',
	to: '',
	rate: '',
      },
      addDialog: false,
      scaleList: []
    }
  },
  created() {
    this.getSettings()
  },
  methods: {
    getSettings() {
      query('settings', 'GET').then((res) => {
        this.settingsForm = {
          maxDependants: Math.trunc(res.data.max_dependants),
          sumPerDependants: Math.trunc(res.data.sum_per_dependants),
          percentagePersonalFees: Math.trunc(res.data.percentage_personal_fees),
          threshold: Math.trunc(res.data.threshold),
          percentageAfterThreshold: Math.trunc(res.data.percentage_after_threshold),
        }
	const tcs = JSON.parse(res.data.payroll_tax)
	this.tcsForm =  {
	  irpp: tcs.irpp,
	  noirpp: tcs.noirpp
	}
	this.cnssForm =  {
	  employerRate: res.data.employer_contribution_rate,
	  employeeRate: res.data.employee_contribution_rate
	}
	this.scaleList = JSON.parse(res.data.personnal_income_tax)
      })
    },
    modify(row) {
      query('settings/modify', 'POST', this.settingsForm).then((res) => {
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: 'Parametres non enregisté, une erreur s\'est produite',
            type: 'error',
            duration: 5000
          })
        }
        this.getSettings()
      })
    },
    modifyTcs() {
      const data = {"tcs" : JSON.stringify(this.tcsForm) }
      console.log(data)
      query('settings/tcs', 'POST', data).then((res) => {
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: 'Parametres non enregisté, une erreur s\'est produite',
            type: 'error',
            duration: 5000
          })
        }
        this.getSettings()
      })
    },
    modifyCnss() {
      const data = {
	"employerRate" : this.cnssForm.employerRate,
	"employeeRate" : this.cnssForm.employeeRate,
      }
      console.log(data)
      query('settings/cnss', 'POST', data).then((res) => {
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: 'Parametres non enregisté, une erreur s\'est produite',
            type: 'error',
            duration: 5000
          })
        }
        this.getSettings()
      });
    },
    addScale() {
      const scale = {
	from : this.scaleForm.from,
	to : this.scaleForm.to,
	rate : this.scaleForm.rate
      }
      if (this.scaleList === null) this.scaleList = []
      this.scaleList.push(scale)
      this.addDialog = false
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
    modifyIrpp() {
      const data = JSON.stringify(this.scaleList)
      query('settings/irpp', 'POST', {"irpp": data}).then((res) => {
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: 'Parametres non enregisté, une erreur s\'est produite',
            type: 'error',
            duration: 5000
          })
        }
      })
    }
  }
}
</script>

