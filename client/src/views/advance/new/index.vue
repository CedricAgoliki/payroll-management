<template>
  <div class="app-container">
    <el-row>
      <el-form :model="advanceForm">

        <el-form-item label="Employee">
          <template>
            <el-select filterable remote v-model="advanceForm.empIndex">
              <el-option v-for="(emp, index) in employees"
		      :value="index" 
		      :label="emp.last_name + ' ' + emp.first_name" 
		      :key="emp.id">
                <span>{{ emp.id }}</span>
                <span>{{ emp.last_name }}</span>
                <span>{{ emp.first_name }}</span>
	      </el-option>
            </el-select>
	  </template>
	</el-form-item>

        <el-form-item label="Type">
          <el-select v-model="advanceForm.type">
            <el-option v-for="type in types" 
	       :value="type"
               :label="type"
               :key="type">{{ type }}</el-option>
	  </el-select>
	</el-form-item>

        <el-form-item label="Montant">
	  <el-input-number v-model="advanceForm.amount" :min="0" :max="maxAdvance">
            <template slot="append">FCFA</template>
	  </el-input-number>
	</el-form-item>

        <el-form-item label="date">
          <el-date-picker v-model="advanceForm.date"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-form-item>

        <el-form-item label="date de paiement">
          <el-date-picker v-model="advanceForm.paymentDate"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-form-item>

        <el-form-item label="Commentaire">
          <el-input type="textarea" v-model="advanceForm.commentary"></el-input>
	</el-form-item>

	<el-button type="success" @click="saveAdvance">Valider</el-button>
      </el-form>
    </el-row>
  </div>
</template>
<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  name: 'documentation',
  data() {
    return {
      newEmployeeDialogVisible: false,
      employees: [],
      types: ['acompte', 'avance'],
      advanceForm: {
        empIndex: null,
        amount: 0,
        date: null,
        paymentDate: null,
        commentary: '',
        type: ''
      }
    }
  },
  computed: {
    maxAdvance() {
      if (this.advanceForm.empIndex === null) return 0
      const emp = this.employees[this.advanceForm.empIndex]
      return parseInt(emp.base_salary)
    }
  },
  created() {
    this.loadEmployees()
  },
  methods: {
    saveAdvance() {
      const advance = {
        employee: this.employees[this.advanceForm.empIndex].id,
        amount: this.advanceForm.amount,
        date: this.advanceForm.date,
	paymentDate: this.advanceForm.paymentDate,
        commentary: this.advanceForm.commentary,
        type: this.advanceForm.type
      }
      query('advance/new', 'POST', advance).then((res) => {
        console.log(res)
        if (res.data.success) {
          Message({
            message: 'Bien enregistré',
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: res.data.message,
            type: 'error',
            duration: 5000
          })
        }
      })
    },
    loadEmployees() {
      query('employee/all', 'GET').then((res) => {
        console.log(res.data)
        this.employees = res.data
        // callback(res.data)
      })
    }
  }
}
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.documentation-container {
  margin: 50px;
  .document-btn {
    float: left;
    margin-left: 50px;
    vertical-align: middle;
    display: block;
    cursor: pointer;
    background: black;
    color: white;
    height: 60px;
    width: 200px;
    line-height: 60px;
    font-size: 20px;
    text-align: center;
  }
}
</style>
