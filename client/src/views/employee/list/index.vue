<template>
  <div style="padding: 30px">
      <!-- <el-row> -->
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <!-- <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
      </el-row> -->
      <el-row v-if="false">
	<el-col :span="6">
          <el-form>
	    <el-form-item>
              <el-input type="search" placeholder="Chercher un employé" prefix-icon="el-icon-search"></el-input>
	    </el-form-item>
          </el-form>
	</el-col>
      </el-row>

    <br />
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog title="Modifier une propriété" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
        <el-form :model="employeeForm">
	<el-tabs>
	  <el-tab-pane label="Informations personnelles">
	      <el-form-item label="Nom">
	        <el-input v-model='employeeForm.lastName'></el-input>
	      </el-form-item>

	      <el-form-item label="Prenom">
	        <el-input v-model='employeeForm.firstName'></el-input>
	      </el-form-item>

	      <el-form-item label="Congés par mois">
	        <el-input v-model='employeeForm.leavesPerMonth'></el-input>
	      </el-form-item>

	      <el-form-item label="Personnes à charge">
	        <el-input v-model='employeeForm.dependants'></el-input>
	      </el-form-item>

	      <el-form-item label="Salaire de base">
	        <el-input v-model='employeeForm.baseSalary'></el-input>
	      </el-form-item>

	      <el-form-item label="Mode de paiement">
	        <el-input v-model='employeeForm.paymentMethod'></el-input>
	      </el-form-item>

	      <el-form-item label="Matricule">
	        <el-input v-model='employeeForm.registrationNumber'></el-input>
	      </el-form-item>

	      <el-form-item label="Fonction">
	        <el-input v-model='employeeForm.office'></el-input>
	      </el-form-item>

	      <el-form-item label="Catégorie">
	        <el-input v-model='employeeForm.category'></el-input>
	      </el-form-item>

	      <el-form-item label="Compte bancaire">
	        <el-input v-model='employeeForm.bankAccount'></el-input>
	      </el-form-item>

	      <el-form-item label="Numero de sécurité sociale">
	        <el-input v-model='employeeForm.socialSecurityNumber'></el-input>
	      </el-form-item>
	  </el-tab-pane>

	  <el-tab-pane label="Autres propriétés">
      	    <el-form-item v-for="p in properties" :label="p.name" :prop="p.name">
              <el-input v-model="employeeForm.properties[p.id]"></el-input>
            </el-form-item>
	  </el-tab-pane>
	  <el-tab-pane label="Informations sur le contrat">

	      <el-form-item label="Debut du contrat">
	        <el-date-picker v-model='employeeForm.contractStart'
				format="dd-MM-yyyy"
				value-format="yyyy-MM-dd"></el-date-picker>
	      </el-form-item>

	      <el-form-item label="Fin du contrat">
	        <el-date-picker v-model='employeeForm.contractEnd'
				format="dd-MM-yyyy"
				value-format="yyyy-MM-dd"></el-date-picker>
	      </el-form-item>

	      <el-form-item label="Commentaire ">
	        <el-input type="textarea" v-model='employeeForm.contractCommentary'></el-input>
	      </el-form-item>
	  </el-tab-pane>
	</el-tabs>
	<el-button type="success" @click="modify">Valider</el-button>
	<el-button type="danger" @click="modifyDialog = false">Annuler</el-button>
        </el-form>
      </div>

    </el-dialog>


    <!-- out Dialogs -->
    <el-dialog title="Sortie de l'employé" 
	:visible.sync="outDialog" 
	:before-close="closeOutDialog">
      <div v-if="toChange !== null">
        <el-form :model="employeeOutForm" label-position="top">
	  <h3>Sortie de {{ toChange.last_name + " " + toChange.first_name }}</h3>
	  <el-form-item label="Date de sortie">
	    <el-date-picker v-model='employeeOutForm.outDate'></el-date-picker>
	  </el-form-item>
	  <el-form-item label="Raison de la sortie">
	    <el-input type="textarea" v-model='employeeOutForm.outReason'></el-input>
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
	<el-button size="mini" type="primary" @click="modifyValues(scope.row)">
	  Modifier
	</el-button>
	<el-button size="mini" type="danger" @click="outEmployee(scope.row)">Sortie</el-button>
	<!-- <el-tag>{{scope.row.name}}</el-tag> -->
      </template>
    </el-table-column>

  </el-table>

    <div class="block" style="width: 100%; text-align: center; margin-top: 10px">
      <el-pagination layout="total, sizes, prev, pager, next, jumper" 
		     :total="pagination.maxPage" 
		     :page-size="pagination.perPage"
		     :current-page.sync="pagination.curPage"
		     :disabled="pagination.disabled"
		     @current-change="handlePaginationCurrentChange"
		     @size-change="handlePaginationSizeChange"
		     ></el-pagination>
    </div>

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
      outDialog: false,
      value: '',
      pagination: {
        curPage: 1,
        perPage: 10,
        maxPage: 0,
        disabled: true
      },
      employeeOutForm: {
        outDate: '',
        outReason: ''
      },
      employeeForm: {
        firstName: '',
        lastName: '',
        baseSalary: '',
        leavesPerMonth: '',
        dependants: '',
        contractStart: '',
        contractEnd: '',
        contractCommentary: '',
        socialSecurityNumber: '',
        registrationNumber: '',
        office: '',
        category: '',
        paymentMethod: '',
        bankAccount: '',
        properties: {}
      }
    }
  },
  created() {
    this.getAll()
    this.getProperties()
  },
  methods: {
    getAll() {
      query('employee/paginated/' + this.pagination.perPage + '?page=' + this.pagination.curPage, 'GET')
        .then((res) => {
	  console.log(res.data)
          this.list = res.data.data
          this.pagination.maxPage = res.data.total
          this.pagination.curPage = res.data.current_page
          this.pagination.perPage = parseInt(res.data.per_page)
          this.pagination.disabled = false
          console.log('per page ', this.pagination.perPage)
        })
    },
    updatePagination(curPage, perPage) {
      query('employee/paginated/' + perPage + '?page=' + curPage, 'GET')
        .then((res) => {
	  console.log(res.data)
          this.list = res.data.data
          this.pagination.maxPage = res.data.total
          this.pagination.curPage = res.data.current_page
          this.pagination.perPage = parseInt(res.data.per_page)
          this.pagination.disabled = false
          console.log('per page ', this.pagination.perPage)
        })
    },
    handlePaginationCurrentChange(curPage) {
      this.updatePagination(curPage, this.pagination.perPage)
    },
    handlePaginationSizeChange(perPage) {
      this.updatePagination(this.pagination.curPage, perPage)
    },
    getProperties() {
      query('property/all', 'GET').then(res => {
        this.properties = res.data
        for (p in this.properties) {
          this.employeForm.properties[p.id] = ''
        }
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

      this.employeeForm.socialSecurityNumber = this.toChange.social_security_number
      this.employeeForm.bankAccount = this.toChange.bank_account
      this.employeeForm.category = this.toChange.category
      this.employeeForm.office = this.toChange.office
      this.employeeForm.registrationNumber = this.toChange.registration_number
      this.employeeForm.paymentMethod = this.toChange.payment_method

      this.employeeForm.contractStart = this.toChange.contracts[0].start
      this.employeeForm.contractEnd = this.toChange.contracts[0].end
      this.employeeForm.contractCommentary = this.toChange.contracts[0].commentary

      console.log(JSON.stringify(this.employeeForm))

      console.log('properties : ')
      for (const p of this.toChange.properties) {
        console.log(JSON.stringify(p))
        this.employeeForm.properties[p.pivot.property_id] = p.pivot.value
      }
    },
    outEmployee(row) {
      this.toChange = JSON.parse(JSON.stringify(row))
      this.outDialog = true
    },
    closeOutDialog() {
      this.employeeOutForm.outDate = ''
      this.employeeOutForm.outReason = ''
      this.outDialog = false
    },
    modify(row) {
      this.modifyDialog = false
      const emp = {
        'firstName': this.employeeForm.firstName,
        'lastName': this.employeeForm.lastName,
        'socialSecurityNumber': this.employeeForm.socialSecurityNumber,
        'bankAccount': this.employeeForm.bankAccount,
        'registrationNumber': this.employeeForm.registrationNumber,
        'contractStart': this.employeeForm.contractStart,
        'contractEnd': this.employeeForm.contractEnd,
        'contractId': this.toChange.contracts[0].id,
        'contractCommentary': this.employeeForm.contractCommentary,
        'baseSalary': this.employeeForm.baseSalary,
        'dependants': this.employeeForm.dependants,
        'leavesPerMonth': this.employeeForm.leavesPerMonth,
        'office': this.employeeForm.office,
        'category': this.employeeForm.category,
        'paymentMethod': this.employeeForm.paymentMethod,
        'properties': this.employeeForm.properties
      }
      console.log(data)
      const data = { 'employee': JSON.stringify(emp) }
      query('employee/update/' + this.toChange.id, 'POST', data).then((res) => {
        console.log(res.data)
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

