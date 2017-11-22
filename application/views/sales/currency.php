<div id="sales-currency">
  <div class="title">환율 관리</div>

  <div class="clearfix">
    <p class="pull-left">
      오늘의 날짜 : <?=date('Y년 m월 d일 ') . dow()?>
    </p>
    <p class="pull-right">
      단위 : 원
    </p>
  </div>
  <table class="table table-striped table-bordered">
    <thead>
      <th>통화명</th>
      <th><?=date('m월 d일', strtotime('-2 days', time()))?></th>
      <th><?=date('m월 d일', strtotime('-1 days', time()))?></th>
      <th><?=date('m월 d일')?></th>
    </thead>
    <tbody>
      <tr v-for="(v, k) in list">
        <th>{{ k | currency }}</th>
        <td v-for="d in dates">{{ v[d] }}</td>
        <td><input type="number" class="form-control" v-model="data.currency[k]"></td>
      </tr>
    </tbody>
  </table>

  <div class="text-center">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#sales-currency',
  data: {
    years: [],
    months: [],
    dates: [],
    data: {
      currency: {}
    },
    list: {},
    prev1: {},
    prev2: {}
  },
  filters: {
    currency: function (value) {
      if (value == 'cny') return '중국 CNY';
      if (value == 'jpy') return '일본 JPY';
      if (value == 'usd') return '미국 USD';
      if (value == 'eur') return '유로 EUR';
      return value;
    }
  },
  methods: {
    init: function() {
      vm.data.date = new Date().toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, '');
      vm.dates.push(new Date(new Date().setDate(new Date().getDate() - 2)).toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, ''));
      vm.dates.push(new Date(new Date().setDate(new Date().getDate() - 1)).toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, ''));
      vm.search();
    },
    search: function () {
      axios.get('/api/sales/currency').then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.data.currency = response.data.data;
        }
      });
    },
    save: function () {
      axios.put('/api/sales/currency', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('저장되었습니다.')
        }
      });
    }
  }
});
vm.init();
</script>
