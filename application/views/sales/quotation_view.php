<div id="sales-quotation" v-cloak>
  <input id="id" type="hidden" value="<?=$id?>">
  <div class="title">견적서</div>

  <div class="text-right">
    <button class="btn btn-success btn-sm" @click="download()">PDF 다운로드</button>
  </div>
  <br>

  <div id="pdf-contents">
    <table class="table table-striped table-bordered">
      <thead>
        <tr>
          <th colspan="2" class="td-left">수신자</th>
          <th colspan="2" class="td-left">공급자</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>회사명</th>
          <td>{{ customer.corp_name }}</td>
          <th>회사명</th>
          <td>{{ company.corp_name }}</td>
        </tr>
        <tr>
          <th>담당자</th>
          <td>{{ customer.corp_name }}</td>
          <th>대표자</th>
          <td>{{ company.corp_name }}</td>
        </tr>
        <tr>
          <th>전화번호</th>
          <td>{{ customer.tel }}</td>
          <th>사업자등록번호</th>
          <td>{{ company.reg_no }}</td>
        </tr>
        <tr>
          <th>이메일주소</th>
          <td>{{ customer.email }}</td>
          <th>담당자</th>
          <td>{{ user.name }}</td>
        </tr>
        <tr>
          <th></th>
          <td></td>
          <th>담당자 연락처</th>
          <td>{{ user.phone }}</td>
        </tr>
        <tr>
          <th></th>
          <td></td>
          <th>담당자 이메일</th>
          <td>{{ user.email }}</td>
        </tr>
        <tr>
          <td colspan="4"></td>
        </tr>
        <tr>
          <th>견적번호</th>
          <td>{{ item.code }}</td>
          <th>Payment Term</th>
          <td>{{ item.payment_term }}일</td>
        </tr>
        <tr>
          <th>프로젝트 이름</th>
          <td>{{ item.proj_name }}</td>
          <th>견적서 유효기간</th>
          <td>발행 후 {{ item.expiry_day }}일</td>
        </tr>
        <tr>
          <th>견적서 발행일</th>
          <td>{{ item.publish_date }}</td>
          <th>적용 통화</th>
          <td>{{ item.currency | currency }}</td>
        </tr>
        <tr>
          <th>배송 납품기일</th>
          <td>{{ item.delivery_term }}일</td>
          <th>공사 이름</th>
          <td>{{ item.construct_name }}</td>
        </tr>
        <tr>
          <th>첨부 견적서</th>
          <td colspan="3">
            <form name="frm" enctype="multipart/form-data" @submit.prevent>
              <div class="row">
                <div class="col-sm-8">
                  <input id="file" type="file" class="form-control" @change="selectFile($event.target.files)">
                </div>
                <div class="col-sm-4 text-left">
                  <button class="btn btn-success btn-sm" @click="uploadFile()" :disabled="!formData">업로드</button>
                </div>
              </div>
            </form>

            <br>
            <div class="text-left" style="margin-top: 5px;" v-for="(file, index) in files">
              <a style="cursor: pointer;" @click="downloadFile(file.file)">{{ file.file.substring(file.file.lastIndexOf('/')+1) }}</a> <a style="margin-left: 15px; cursor: pointer;" @click="deleteFile(index)">삭제</a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

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
                <div>{{ com.dc_rate }}%</div>
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
                <div>{{ com.dc_rate }}%</div>
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
                <div>{{ com.dc_rate }}%</div>
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
                <div>{{ com.dc_rate }}%</div>
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
    company: {},
    customer: {},
    user: {},
    files: [],
    data: {},
    formData: null,
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
          doc.save(vm.item.proj_name + '_견적서.pdf');
        }
      });
    },
    selectFile: function (file) {
      if (!file.length) return;
      vm.formData = new FormData();
      vm.formData.append('file', file[0], file[0].name);
    },
    uploadFile: function () {
      if (!vm.formData) {
        return;
      }

      axios.post('/api/sales/quotation_attachment/' + vm.id, vm.formData).then(function (response) {
        if (response.status == 201) {
          vm.files = response.data.files;
          $('#file').val('');
          vm.formData = null;
        }
      });
    },
    deleteFile: function (index) {
      axios.delete('/api/sales/quotation_attachment/' + vm.files[index]['id']).then(function (response) {
        if (response.status == 200) {
          vm.files.splice(index, 1);
        }
      });
    },
    downloadFile: function (url) {
      axios.post(url, null, {responseType:'arraybuffer'}).then(function (response) {
        var blob = new Blob([response.data], {type: response.headers['content-type']});
        var link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = url.substring(url.lastIndexOf('/')+1);
        link.click();
      });
    },
    getItem: function () {
      axios.get('/api/sales/quotation_detail/' + vm.id).then(function (response) {
        if (response.status == 200) {
          vm.item = response.data.item;
          vm.company = response.data.company;
          vm.customer = response.data.customer;
          vm.user = response.data.user;
          vm.files = response.data.files;
        }
      });
    },
  }
});
</script>
