<div id="purchase-aluminum">
  <div class="title">알루미늄 가격</div>

  <div class="row">
    <div class="col-sm-2">
      <select class="form-control" v-model="year">
        <option v-for="item in years" :value="item.value">{{ item.text }}</option>
      </select>
    </div>
    <div class="col-sm-2">
      <select class="form-control" v-model="month">
        <option v-for="item in months" :value="item.value">{{ item.text }}</option>
      </select>
    </div>
    <div class="col-sm-2">
      <button class="btn btn-primary" @click="get()">조회</button>
    </div>
    <canvas id="myChart" width="400" height="100"></canvas>
  </div>


  <div class="margin-top-2">
    <div class="clearfix">
      <p class="pull-left">
        현대 자동차 입찰 가격
      </p>
      <p class="pull-right">
        단위 : 원/톤
      </p>
    </div>
  </div>
  <table class="table table-striped table-bordered">
    <thead>
      <th></th>
      <th><?=date('m월 d일', strtotime('-2 days', time()))?></th>
      <th><?=date('m월 d일', strtotime('-1 days', time()))?></th>
      <th><?=date('m월 d일')?></th>
    </thead>
    <tbody>
      <tr>
        <td>시세</td>
        <td v-for="d in dates">{{ list[d] ? list[d]['price'] : '' }}</td>
        <td><input type="number" class="form-control" v-model="data.price"></td>
      </tr>
      <tr>
        <td>매입가</td>
        <td v-for="d in dates">{{ list[d] ? list[d]['buy_price'] : '' }}</td>
        <td><input type="number" class="form-control" v-model="data.buy_price"></td>
      </tr>
    </tbody>
  </table>

  <div class="text-center">
    <button class="btn btn-primary" @click="save()">저장</button>
  </div>
</div>

<script>
var vm = new Vue({
  el: '#purchase-aluminum',
  data: {
    years: [],
    months: [],
    dates: [],
    data: {},
    labels: [],
    data1: [],
    data2: [],
    year: '',
    month: '',
    list: [],
    aluminum: {},
    chart: {},
  },
  methods: {
    init: function() {
      vm.make();
      vm.dates.push(vm.$options.filters['jsdate'](-2));
      vm.dates.push(vm.$options.filters['jsdate'](-1));
      // vm.data.date = new Date().toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, '');
      // vm.dates.push(new Date(new Date().setDate(new Date().getDate() - 2)).toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, ''));
      // vm.dates.push(new Date(new Date().setDate(new Date().getDate() - 1)).toLocaleDateString('ko-KR').replace(/\. /g, '-').replace(/\./g, ''));
      vm.draw();
      vm.get();
      vm.get2();
    },
    make: function () {
      vm.year = new Date().getFullYear();
      vm.month = new Date().getMonth() + 1;

      for (i = vm.year; i >= 2017; i--) {
        vm.years.push({value: i, text: i + '년'});
      }
      for (i = 1; i <= 12; i++) {
        vm.months.push({value: i, text: i + '월'});
      }
    },
    draw: function () {
      var ctx = document.getElementById("myChart");
      vm.chart = new Chart(ctx, {
        type: 'bar',
        data: {
          labels: [],//vm.labels,
          datasets: [{
            label: '시세',
            data: [],//vm.data1,
            fill: false,
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255,99,132,1)',
            borderWidth: 1
          }, {
            label: '매입가',
            data: [],//vm.data2,
            fill: false,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          tooltips: {
            callbacks: {
              title: function (tooltipItem, data) {
                // console.log(data.datasets[tooltipItem[0].datasetIndex].label);
                // return tooltipItem[0].xLabel + ' : ' + tooltipItem[0].yLabel;
                return data.datasets[tooltipItem[0].datasetIndex].label + ' : ' + vm.$options.filters['number'](tooltipItem[0].yLabel);
              },
              label: function (tooltipItem, data) {
                return false;
              }
            }
          },
          scales: {
            xAxes: [{
              // display: false,
              // barThickness : 30,
              barPercentage: 0.9,
            }],
            yAxes: [{
              ticks: {
                beginAtZero: true,
                callback: function(value, index, values) {
                  if (Math.floor(value) === value) {
                    return vm.$options.filters['number'](value);
                  }
                }
              }
            }]
          }
        }
      });
    },
    redraw: function () {
      vm.chart.data.labels = vm.labels;
      vm.chart.data.datasets[0].data = vm.data1;
      vm.chart.data.datasets[1].data = vm.data2;
      vm.chart.data.labels = vm.labels;
      vm.chart.update();
    },
    get: function () {
      var params = makeParams({
        year: vm.year,
        month: vm.month,
      });

      axios.get('/api/purchase/aluminum?' + params).then(function (response) {
        if (response.status == 200) {
          vm.labels = response.data.labels;
          vm.data1 = response.data.data1;
          vm.data2 = response.data.data2;
          vm.redraw();
        }
      });
    },
    get2: function () {
      axios.get('/api/purchase/aluminum2').then(function (response) {
        if (response.status == 200) {
          vm.list = response.data.list;
          vm.data = response.data.data ? response.data.data : {};
        }
      });
    },
    save: function () {
      vm.data.date = vm.$options.filters['jsdate']();
      axios.put('/api/purchase/aluminum', vm.data).then(function (response) {
        if (response.status == 200) {
          alert('저장되었습니다.')
        }
      });
    }
  }
});
vm.init();
</script>
