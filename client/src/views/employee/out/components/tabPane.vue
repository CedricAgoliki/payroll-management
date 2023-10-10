<template>
  <div>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialogVisible = true"></el-button>
      <el-button type="danger" icon="el-icon-delete"></el-button>
    </el-row>
    <br />
    <el-row>
      
    <!-- Dialogs -->
    <el-dialog title="Ajouter" :visible.sync="addDialogVisible">
      <el-form>
	<el-form-item label="Nouveau">
	  <el-input v-model='value'></el-input>
	</el-form-item>
	<el-form-item>
	  <el-button type="primary" @click="addRessource">Ajouter</el-button>
	</el-form-item>
      </el-form>
    </el-dialog>


  <el-table :data="list" border fit highlight-current-row style="width: 100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column min-width="300px" label="Title">
      <template slot-scope="scope">
        <span>{{scope.row.title}}</span>
        <el-tag>{{scope.row.libelle}}</el-tag>
      </template>
    </el-table-column>

  </el-table>
    </el-row>
  </div>
</template>

<script>
import { getAll, createResource } from '@/api/api'

export default {
  props: {
    type: {
      type: String,
      default: 'FON'
    },
    api: {
      type: String
    }
  },
  data() {
    return {
      list: null,
      listQuery: {
        page: 1,
        limit: 5,
        type: this.type,
        sort: '+id'
      },
      loading: false,
      addDialogVisible: false,
      value: ''
    }
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: 'success',
        draft: 'info',
        deleted: 'danger'
      }
      return statusMap[status]
    }
  },
  created() {
    console.log(this.api)
    this.getList()
  },
  methods: {
    getList() {
      getAll(this.api).then(response => {
        this.list = response.data
        this.loading = false
      })
    },
    addRessource() {
      const data = { 'libelle': this.value }
      console.log(data)
      createResource(this.api, data).then(() => {
        console.log('sending')
        this.addDialogVisible = false
        this.value = ''
        this.getList()
      })
    }
  }
}
</script>

