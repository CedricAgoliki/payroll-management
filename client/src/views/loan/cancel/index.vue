<template>
  <div class="app-container">
    <el-row>
      <el-button type="primary" @click="newEmployeeDialogVisible = true">Ajouter</el-button>
      <el-button type="danger">Supprimer</el-button>
    </el-row>
    <el-row>
      <el-table stripe>
	<el-table-column label="#" prop="select"></el-table-column>
	<el-table-column label="Nom et prenoms" prop="name"></el-table-column>
	<el-table-column label="Fiches" prop="fiche"></el-table-column>
      </el-table>
    </el-row>
    <!-- Modals -->
    <el-dialog title="Nouveau employe" :visible.sync="newEmployeeDialogVisible">
      <span>Nouveau employé</span>
      <el-form ref="newEmployeeForm" :model="newEmployeeForm">
	<el-form-item label="Nom">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="prenoms">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Numéro de sécurité sociale">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Numéro de compte bancaire">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Numéro matricule">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Date du contrat">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Date de contrat prévue">
	  <el-input></el-input>
	</el-form-item>

	<el-form-item label="Fonction">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>


	<el-form-item label="Status">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>

	<el-form-item label="Categorie">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>


	<el-form-item label="Affectation comptable">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>

	<el-form-item label="Mode de payement">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>

	<el-form-item label="Echelon">
	  <el-select>
	    <el-option label="Test"></el-option label>
	  </el-select>
	</el-form-item>

      </el-form>

    </el-dialog>
  </div>
</template>
<script>
import DropdownMenu from '@/components/Share/dropdownMenu'

export default {
  name: 'documentation',
  components: { DropdownMenu },
  data() {
    return {
      newEmployeeDialogVisible: false,
      articleList: [
        { title: '基础篇', href: 'https://segmentfault.com/a/1190000009275424' },
        { title: '登录权限篇', href: 'https://segmentfault.com/a/1190000009506097' },
        { title: '实战篇', href: 'https://segmentfault.com/a/1190000009762198' },
        { title: 'vueAdmin-template 篇', href: 'https://segmentfault.com/a/1190000010043013' },
        { title: '自行封装 component', href: 'https://segmentfault.com/a/1190000009090836' },
        { title: '优雅的使用 icon', href: 'https://segmentfault.com/a/1190000012213278' }
      ]
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
