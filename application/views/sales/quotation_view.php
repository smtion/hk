<div id="sales-quotation" v-cloak>
  <input id="id" type="hidden" value="<?=$id?>">
  <div class="title">견적서</div>

  <div class="text-right">
    <button class="btn btn-success btn-sm" @click="download()">PDF 다운로드</button>
  </div>

  <div id="pdf-contents">
    <div class="form-horizontal">
      <div class="form-group">
        <label class="col-sm-2 control-label">적용 통화</label>
        <div class="col-sm-10 form-control-static">
          {{ item.currency | currency }}
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">해광 통화</label>
        <div class="col-sm-10 form-control-static">
          {{ item.hk_currency }}
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">견적 적용 환율</label>
        <div class="col-sm-10 form-control-static">
          {{ item.applied_exchange }}
        </div>
      </div>
      <div class="form-group">
        <label class="col-sm-2 control-label">설명</label>
        <div class="col-sm-10 form-control-static">
          {{ item.desc }}
        </div>
      </div>
    </div>

    <div v-for="(set, index) in item.set">
      <div v-show="set.product">
        <br>
        <h4>제품</h4>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Model</th>
              <th>Type</th>
              <th>Dimension</th>
              <th>Concentrated Load 2mm delfection @ 1/2 edge</th>
              <th>Ultimate Load @ 1/2 edge</th>
              <th>Open Ratio(%)</th>
              <th>Conductivity</th>
              <th width="100">면적</th>
              <th width="100">수량</th>
              <th width="150">단가</th>
              <th width="150">금액</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="com in set.product.list">
              <td>{{ com.rel.model }}</td>
              <td>{{ com.rel.type | product_type }}</td>
              <td>{{ com.rel.size }}</td>
              <td>{{ com.rel.edge_cl }}</td>
              <td>{{ com.rel.edge_ul }}</td>
              <td>{{ com.rel.ph_ratio }}</td>
              <td>{{ com.rel.conductivity }}</td>
              <td>{{ com.size }}</td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.qty }}</div>
                <div>할인률 {{ com.dc_rate }}%</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.sales_price | number }}원</div>
                <div>{{ com.sales_price_dc | number }}원</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.total | number }}원</div>
                <div>{{ com.total_dc | number }}원</div>
              </td>
            </tr>
            <tr>
              <td colspan="10">합계</td>
              <td>{{ set.product.total | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">절사</td>
              <td>{{ set.product.rest | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">최종 합계</td>
              <td>{{ set.product.total_final | number }}원</div>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="set.option">
        <br>
        <h4>옵션</h4>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>옵션명</th>
              <th>옵션상세</th>
              <th width="100">수량</th>
              <th width="150">단가</th>
              <th width="150">금액</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="com in set.option.list">
              <td>{{ com.rel.name }}</td>
              <td class="outer-td">
                <div class="clearfix">
                  <div class="pull-left inner-td" v-for="(v, k) in com.rel.details" v-bind:style="{width: 100 / Object.keys(com.rel.details).length + '%'}">
                    <div>{{ k }}</div>
                    <div>{{ v ? v : '&nbsp;' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.qty }}</div>
                <div>할인률 {{ com.dc_rate }}%</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.sales_price | number }}원</div>
                <div>{{ com.sales_price_dc | number }}원</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.total | number }}원</div>
                <div>{{ com.total_dc | number }}원</div>
              </td>
            </tr>
            <tr>
              <td colspan="10">합계</td>
              <td>{{ set.option.total | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">절사</td>
              <td>{{ set.option.rest | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">최종 합계</td>
              <td>{{ set.option.total_final | number }}원</div>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="set.material">
        <br>
        <h4>부자재</h4>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>부자재명</th>
              <th>부자재상세</th>
              <th width="100">수량</th>
              <th width="150">단가</th>
              <th width="150">금액</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="com in set.material.list">
              <td>{{ com.rel.name }}</td>
              <td class="outer-td">
                <div class="clearfix">
                  <div class="pull-left inner-td" v-for="(v, k) in com.rel.details" v-bind:style="{width: 100 / Object.keys(com.rel.details).length + '%'}">
                    <div>{{ k }}</div>
                    <div>{{ v ? v : '&nbsp;' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.qty }}</div>
                <div>할인률 {{ com.dc_rate }}%</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.sales_price | number }}원</div>
                <div>{{ com.sales_price_dc | number }}원</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.total | number }}원</div>
                <div>{{ com.total_dc | number }}원</div>
              </td>
            </tr>
            <tr>
              <td colspan="10">합계</td>
              <td>{{ set.material.total | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">절사</td>
              <td>{{ set.material.rest | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">최종 합계</td>
              <td>{{ set.material.total_final | number }}원</div>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="set.cost">
        <br>
        <h4>간접비</h4>
        <table class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>간접비명</th>
              <th>간접비상세</th>
              <th width="100">수량</th>
              <th width="150">단가</th>
              <th width="150">금액</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="com in set.cost.list">
              <td>{{ com.rel.name }}</td>
              <td class="outer-td">
                <div class="clearfix">
                  <div class="pull-left inner-td" v-for="(v, k) in com.rel.details" v-bind:style="{width: 100 / Object.keys(com.rel.details).length + '%'}">
                    <div>{{ k }}</div>
                    <div>{{ v ? v : '&nbsp;' }}</div>
                  </div>
                </div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.qty }}</div>
                <div>할인률 {{ com.dc_rate }}%</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.sales_price | number }}원</div>
                <div>{{ com.sales_price_dc | number }}원</div>
              </td>
              <td>
                <div style="margin-bottom: 5px;">{{ com.total | number }}원</div>
                <div>{{ com.total_dc | number }}원</div>
              </td>
            </tr>
            <tr>
              <td colspan="10">합계</td>
              <td>{{ set.cost.total | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">절사</td>
              <td>{{ set.cost.rest | number }}원</div>
            </tr>
            <tr>
              <td colspan="10">최종 합계</td>
              <td>{{ set.cost.total_final | number }}원</div>
            </tr>
          </tbody>
        </table>
      </div>
    </div><!-- v-for -->
  </div><!-- #pdf-contents -->
  <div class="text-center margin-top-2">
    <button class="btn btn-primary" onclick="history.go(-1)">목록</button>
  </div>
</div>

<script>
Vue.filter('currency', function (value) {
  var list = {
    'kwn': '대한민국(원)',
    'cny': '중국 CNY',
    'jpy': '일본 JPY',
    'usd': '미국 USD',
    'eur': '유로 EUR',
  };

  return list[value] ? list[value] : '';
});
var vm = new Vue({
  el: '#sales-quotation',
  data: {
    id: $('#id').val(),
    item: {},
  },
  mounted: function () {
    this.$nextTick(function () {
      vm.init();
    });
  },
  methods: {
    init: function () {
      vm.reload();
    },
    reload: function () {
      vm.getItem();
    },
    download: function () {
      var doc = new jsPDF();
      var el = document.getElementById("pdf-contents");
      html2canvas(el, {
        onrendered : function (canvas) {
          var imgData = canvas.toDataURL('image/png');
          vm.pdf = imgData;
          var doc = new jsPDF('p','mm',[297,210]);
          // doc.addImage(imgData, 'PNG', 10,10,el.offsetWidth/10,el.offsetHeight/10);
          doc.addImage(imgData, 'PNG', 10,10,190,el.offsetHeight/(el.offsetWidth/190));
          doc.save('개인지출내역서.pdf');
        }
      });
    },
    getItem: function () {
      axios.get('/api/sales/quotation_detail/' + vm.id).then(function (response) {
        if (response.status == 200) {
          vm.item = response.data.item;
        }
      });
    },
  }
});
</script>
