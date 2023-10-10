<template>
  <div> 
    <Properties v-if="type == 'PROP'"></Properties>
    <Primes v-if="type == 'PRIM'"></Primes>
    <Retenues v-if="type == 'RET'"></Retenues>
    <Settings v-if="type == 'SET'"></Settings>
    <Leaves v-if="type == 'CON'"></Leaves>
    <Others v-if="type == 'AUT'"></Others>
  </div>
</template>

<script>

import Properties from '../properties/Properties'
import Primes from '../primes/Primes'
import Retenues from '../deductions/Deductions'
import Settings from '../settings/Settings'
import Leaves from '../leaves/Leaves'
import Others from '../others/Others'

export default {
  components: { Properties, Primes, Retenues, Settings, Leaves, Others },
  props: {
    type: {
      type: String,
      default: 'FON'
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
  methods: {
  }
}
</script>

