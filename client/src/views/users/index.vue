<template>
  <div style="padding: 30px">
      <!--<el-row>-->
      <!-- <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button> -->
      <!--<el-button type="danger" icon="el-icon-delete" @click="deleteSelected"></el-button>
    </el-row>
    <br />-->
    <h1>Utilisateurs et permissions</h1>
    <el-row>
      <el-button type="primary" icon="el-icon-plus" @click="addDialog = true"></el-button>
    </el-row>
    <br />
    <el-row>

    <!-- add Dialogs -->
    <el-dialog title="Ajouter un utilisateur" :visible.sync="addDialog">
      <b>*Tout les champs sont obligatoire</b>
      <el-form :model="userForm" label-position="top">

         <el-form-item label="Nom">
	    <el-input v-model="userForm.name"></el-input>
	  </el-form-item>

         <el-form-item label="Email">
	    <el-input v-model="userForm.email"></el-input>
	  </el-form-item>
 
         <el-form-item label="Permissions">
	   <el-checkbox-group v-model="userForm.roles">
	    <el-checkbox v-for ="r in roles" :label="r" :key="r"></el-checkbox><br />
	  </el-checkbox-group>
	</el-form-item>

	<el-button type="danger" @click="saveUser">Valider</el-button>
	<el-button type="primary" @click="addDialog = false">Annuler</el-button>
      </el-form>
    </el-dialog>

    <!-- modify Dialogs -->
    <el-dialog title="Modifier un utilisateur" :visible.sync="modifyDialog">
      <div v-if="toChange !== null">
	<b>*Tout les champs sont obligatoire</b>
	<el-form :model="userForm" label-position="top">

	   <el-form-item label="Nom">
	      <el-input v-model="toChange.name" :disabled="true"></el-input>
	    </el-form-item>

	   <el-form-item label="Email">
	      <el-input v-model="toChange.email" :disabled="true"></el-input>
	    </el-form-item>
   
	   <el-form-item label="Permissions">
	     <el-checkbox-group v-model="toChange.roles">
	      <el-checkbox v-for ="r in roles" :label="r" :key="r"></el-checkbox><br />
	    </el-checkbox-group>
	  </el-form-item>

	  <el-button type="danger" @click="modifyUser">Valider</el-button>
	  <el-button type="primary" @click="modifyDialog = false">Annuler</el-button>
	</el-form>
      </div>
    </el-dialog>



  <el-table :data="list" border fit highlight-current-row 
	  @selection-change="handleSelection" style="width:100%">

    <el-table-column type="selection" align="center" label="ID" width="50">
    </el-table-column>

    <el-table-column label="Nom">
      <template slot-scope="scope">
        <span>{{ scope.row.name}}</span>
      </template>
    </el-table-column>

    <el-table-column label="Email">
      <template slot-scope="scope">
        <span>{{ scope.row.email}}</span>
      </template>
    </el-table-column>

    <el-table-column label="Permissions">
      <template slot-scope="scope">
	<span>{{ scope.row.roles }}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="300px" label="Opérations">
      <template slot-scope="scope">
	<el-button size="mini" type="primary" @click="toChange = scope.row; modifyDialog = true">modifier</el-button>
	<el-button size="mini" type="danger" @click="deleteUser(scope.row)">supprimer</el-button>
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
      roles: ['admin', 'conges', 'avances', 'prets'],
      list: [],
      selected: [],
      toChange: null,
      loading: false,
      addDialog: false,
      modifyDialog: false,
      confirmDialog: false,
      value: '',
      userForm: {
        name: null,
        email: '',
	roles: []
      }
    }
  },
  created() {
    this.getAllUsers()
  },
  methods: {
    getAllUsers() {
      query('user/list', 'GET').then((res) => {
        console.log(res.data)
        this.list = res.data
	for (let i = 0; i < this.list.length; i++) {
	  this.list[i].roles = JSON.parse(this.list[i].roles)
	}
      })
    },
    handleSelection(rows) {
      console.log(rows)
      this.selected = rows
    },
    saveUser() {
      const data = {
	name: this.userForm.name,
	email: this.userForm.email,
	roles: JSON.stringify(this.userForm.roles)
      }
      console.log(data)
      query('user/new', 'POST', data).then((res) => {
	this.addDialog = false
        if (res.data.success) {
	  this.getAllUsers()
          Message({
	    message: "Utilisateur bien inscrit",
            type: 'success',
            duration: 5000
          })
        } else {
          Message({
            message: "Utilisateur non inscrit, une erreur s'est produite",
            type: 'error',
            duration: 5000
          })
        }
      })
    },
    modifyUser() {
      this.modifyDialog = false
      console.log("to change : ", JSON.stringify(this.toChange));
      const data = {
	'roles': JSON.stringify(this.toChange.roles)
      }
      query('user/modify/' + this.toChange.id, 'POST', data).then((res) => {
        if (res.data.success) this.getAllUsers()
	
      })

    },
    deleteUser(row) {
      query('user/delete/' + row.id, 'GET').then((res) => {
        if (res.data.success) this.getAllUsers()
      })
    }
  }
}
</script>

