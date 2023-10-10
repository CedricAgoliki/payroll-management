<template>
  <div style="padding: 30px">
    <el-row>

  <h1>Congés restant par employé</h1>
  <el-table :data="listSlice" border fit highlight-current-row
	    :default-sort = "{ order: 'descending' }"
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column label="Nom" sortable>
      <template slot-scope="scope">
        <span>{{ scope.row.employee.last_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Prenoms" sortable>
      <template slot-scope="scope">
        <span>{{ scope.row.employee.first_name }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Jours de congés pris" sortable>
      <template slot-scope="scope">
        <span>{{ scope.row.takenLeaves }}</span>
      </template>
    </el-table-column>

    <el-table-column label="Jours de congés restants" sortable>
      <template slot-scope="scope">
        <span>{{ scope.row.remainingLeaves }}</span>
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
      listCurrent: null,
      listSlice: null,
      loading: false,
      value: '',
      pagination: {
        curPage: 1,
        perPage: 10,
        maxPage: 0,
        disabled: true
      }
    }
  },
  created() {
    this.getAll()
  },
  methods: {
    getAll() {
      query('leave/remainings', 'GET').then((res) => {
        console.log(res.data)
        this.listCurrent = res.data
        this.listSlice = this.listCurrent.slice(0, this.pagination.perPage)
        this.pagination.maxPage = this.listCurrent.length
        this.pagination.curPage = 1
        this.pagination.disabled = false
      })
    },
    handlePaginationCurrentChange(curPage) {
      this.updatePagination(curPage, this.pagination.perPage)
    },
    handlePaginationSizeChange(perPage) {
      this.updatePagination(this.pagination.curPage, perPage)
    },
    updatePagination(curPage, perPage) {
      this.pagination.maxPage = this.listCurrent.length
      this.pagination.curPage = curPage
      this.pagination.perPage = perPage
      const start = (curPage - 1) * perPage
      const end = start + perPage
      this.listSlice = this.listCurrent.slice(start, end)
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    }
  }
}
</script>

