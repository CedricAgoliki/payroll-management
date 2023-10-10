<template>
  <div style="padding: 30px">
      <!-- <el-row> -->
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <!-- <el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
      </el-row> -->
      <el-row>
	<el-col :span="6">
          <el-form>
	    <el-form-item>
              <el-input type="search" placeholder="Chercher un employé" prefix-icon="el-icon-search">
	      </el-input>
	    </el-form-item>
          </el-form>
	</el-col>
      </el-row>

    <br />
    <el-row>

    <!-- modify Dialogs -->
    <el-dialog title="Informations sur le nouveau contrat" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
	<el-form :model="contractForm">
	  <!-- <el-form-item label="Debut du contrat">
	    <el-date-picker v-model='contractForm.contractStart'></el-date-picker>
	  </el-form-item> -->
	  
	  <el-form-item label="Fin du contrat">
	    <el-date-picker v-model='contractForm.contractEnd'></el-date-picker>
	  </el-form-item>
	  
	  <el-form-item label="Commentaire">
	    <el-input type="textarea" v-model='contractForm.contractCommentary'></el-input>
	  </el-form-item>
	  
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

    <el-table-column min-width="200px" label="Nom">
      <template slot-scope="scope">
        <span>{{scope.row.last_name}}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="200px" label="Prenoms">
      <template slot-scope="scope">
        <span>{{scope.row.first_name}}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="200px" label="Fin du contrat">
      <template slot-scope="scope">
	<span>{{scope.row.contracts[0].end | moment("DD MMMM YYYY") }}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="success" @click="modifyValues(scope.row)">
	  Renouveler le contrat
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
      contractForm: {
        contractStart: '',
        contractEnd: '',
        contractCommentary: '',
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
      this.modifyDialog = true
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
      const contract = {
        // 'contractStart': this.employeeForm.contractStart,
        'contractEnd': this.employeeForm.contractEnd,
        'contractCommentary': this.employeeForm.contractCommentary
      }
      query('employee/renewcontract/' + this.toChange.id, 'POST', contract).then((res) => {
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

