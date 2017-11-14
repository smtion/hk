<div id="purchase-aluminum">
  <h4>알루미늄 가격</h4>

  <div class="row">
    <div class="col-sm-4">
      <select name="" class="form-control" v-model="type" @change="selectType()">
        <option value="day">Day</option>
        <option value="week">Week</option>
        <option value="month">Month</option>
        <option value="year">Year</option>
      </select>
    </div>
    <div class="col-sm-4">
      <select name="" class="form-control" v-model="value">
        <option v-for="item in items" :value="item.value">{{ item.text }}</option>
      </select>
    </div>
    <div class="col-sm-4"></div>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#purchase-aluminum',
  data: {
    type: 'month',
    value: '',
    items: []
  },
  methods: {
    init: function() {
      vm.makeItems();
      vm.value = new Date().getMonth() + 1;
    },
    selectType: function () {
      console.log(vm.type);
      vm.makeItems();
    },
    makeItems: function () {
      var y = new Date().getFullYear();
      var m = new Date().getFullYear();

      if (vm.type == 'year') {
        for (i = y; i > 2010; i--) {
          vm.items.push({value: i, text: i + '년'});
        }
      } else if (vm.type == 'month') {
        for (i = 1; i <= 12; i++) {
          vm.items.push({value: i, text: i + '월'});
        }
      } else if (vm.type == 'day') {
        for (i = 1; i <= 12; i++) {
          vm.items.push({value: i, text: i + '월'});
        }
      }

      console.log(vm.items)
    }
  }
});
vm.init();
</script>
