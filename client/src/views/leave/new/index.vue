<template>
  <div class="app-container">
    <h2>Prise de congé</h2>
    <el-form>

        <el-form-item label="Type">
          <template>
            <el-select filterable remote v-model="leaveForm.leaveType">
              <el-option v-for="type in leaveTypes"
		      :value="type.id" 
		      :label="type.label" 
		      :key="type.id">
                <span>{{ type.label }}</span>
	      </el-option>
            </el-select>
	  </template>
	</el-form-item>

        <el-form-item label="Employee">
          <template>
            <el-select filterable remote v-model="leaveForm.employee">
              <el-option v-for="emp in employees"
		      :value="emp.id" 
		      :label="emp.last_name + ' ' + emp.first_name" 
		      :key="emp.id">
                <span>{{ emp.id }}</span>
                <span>{{ emp.last_name }}</span>
                <span>{{ emp.first_name }}</span>
	      </el-option>
            </el-select>
	  </template>
	</el-form-item>

      <el-form-item label="Date de debut et de fin du congé" prop="identite">
	<el-col :span="7">
	  <el-date-picker type="date" 
			  v-model="leaveForm.start" 
			  placeholder="Date de debut du congé"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-col>
	<el-col class="line" :span="2">.</el-col>
	<el-col :span="7">
	  <el-date-picker type="date" 
			  v-model="leaveForm.end" 
			  placeholder="Date de fin de congé"
			  format="dd-MM-yyyy"
			  value-format="yyyy-MM-dd"></el-date-picker>
	</el-col>
      </el-form-item>

      <el-form-item :label-width="labelWidth" label="Nombre de jour de congé" prop="matricule">
        <el-input v-model="leaveForm.nb_days"></el-input>
      </el-form-item>

      <el-form-item :label-width="labelWidth" label="Raison du congé" prop="matricule">
        <el-input v-model="leaveForm.reason"></el-input>
      </el-form-item>

      <el-form-item :label-width="labelWidth" label="Commentaire" prop="matricule">
        <el-input type="textarea" v-model="leaveForm.commentary"></el-input>
      </el-form-item>

      <el-button type="success" @click="addLeave">Ajouter</el-button>

    </el-form>
  </div>
</template>
<script>
import { query } from '@/api/api'
import { Message } from 'element-ui'

export default {
  // name: 'documentation',
  // components: { DropdownMenu },
  data() {
    return {
      newEmployeeDialogVisible: false,
      employees: [],
      leaveTypes: [],
      leaveForm: {
        leaveType: '',
        employee: '',
        start: '',
        end: '',
        commentary: '',
        reason: '',
        nb_days: ''
      }
    }
  },
  created() {
    this.loadEmployees()
    this.loadleaveTypes()
  },
  methods: {
    loadEmployees() {
      query('employee/all', 'GET').then((res) => {
        console.log('employees : ', res.data)
        this.employees = res.data
      })
    },
    loadleaveTypes() {
      query('leavetype/list', 'GET').then((res) =>{
        console.log('leaveTypes : ',res.data)
        this.leaveTypes = res.data
      })
    },
    addLeave() {
      const leave = {
        employee: this.leaveForm.employee,
        start: this.leaveForm.start,
        end: this.leaveForm.end,
        reason: this.leaveForm.reason,
        nbDays: this.leaveForm.nb_days,
        commentary: this.leaveForm.commentary,
        type: this.leaveForm.leaveType
      }
      console.log(leave)
      query('leave/new', 'POST', leave).then((res) => {
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
