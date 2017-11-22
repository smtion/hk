<div id="purchase-aluminum">
  <div class="title">알루미늄 가격</div>

  <div class="row">
    <div class="col-sm-2">
      <select class="form-control" v-model="data.year">
        <option v-for="item in years" :value="item.value">{{ item.text }}</option>
      </select>
    </div>
    <div class="col-sm-2">
      <select class="form-control" v-model="data.month">
        <option v-for="item in months" :value="item.value">{{ item.text }}</option>
      </select>
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary" @click="search()">검색</button>
    </div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#purchase-aluminum',
  data: {
    years: [],
    months: [],
    data: {},
  },
  methods: {
    init: function() {
      vm.make();
    },
    make: function () {
      vm.data.year = new Date().getFullYear();
      vm.data.month = new Date().getMonth() + 1;

      for (i = vm.data.year; i >= 2017; i--) {
        vm.years.push({value: i, text: i + '년'});
      }
      for (i = 1; i <= 12; i++) {
        vm.months.push({value: i, text: i + '월'});
      }
    },
    search: function () {

    }
  }
});
vm.init();
</script>
