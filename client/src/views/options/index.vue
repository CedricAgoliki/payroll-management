<template>
  <div class="tab-container">
    <!--<el-tag>mounted times ：{{createdTimes}}</el-tag>
    <el-alert style="width:200px;display:inline-block;vertical-align: middle;margin-left:30px;" title="Tab with keep-alive" type="success" :closable="false">
    </el-alert>-->
    <el-tabs style='margin-top:15px;' v-model="activeName" tab-position="top">
      <el-tab-pane v-for="item in tabMapOptions" :label="item.label" :key='item.key' :name="item.key">
        <keep-alive>
          <tab-pane v-if='activeName==item.key' :type='item.key' @create='showCreatedTimes'></tab-pane>
        </keep-alive>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import tabPane from './components/tabPane'

export default {
  name: 'tab',
  components: { tabPane },
  data() {
    return {
      tabMapOptions: [
        { label: 'Propriétés des employées', key: 'PROP' },
        { label: 'Primes', key: 'PRIM' },
        { label: 'Retenues', key: 'RET' },
        { label: 'Impots', key: 'SET' },
        { label: 'Congés', key: 'CON' },
        { label: 'Autres', key: 'AUT' }
      ],
      activeName: 'FON',
      createdTimes: 0
    }
  },
  methods: {
    showCreatedTimes() {
      this.createdTimes = this.createdTimes + 1
    }
  }
}
</script>

<style scoped>
  .tab-container{
    margin: 30px;
  }
</style>
