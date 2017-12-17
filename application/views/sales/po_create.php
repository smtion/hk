<div id="sales-po-create" v-cloak>
  <input id="id" type="hidden" value="<?=$id?>">
  <div class="title">PO 등록</div>

  <div v-for="(set, index) in item.set">
    <div v-show="set.product">
      <br>
      <h4>제품</h4>
      <table class="table table-striped table-bordered">
        <thead>
          <tr>
            <th></th>
            <th>설명</th>
            <th colspan="13">{{ item.desc }}</th>
          </tr>
          <tr>
            <th rowspan="2">선택</th>
            <th rowspan="2">품명</th>
            <th rowspan="2">규격</th>
            <th rowspan="2">단위</th>
            <th rowspan="2">수량</th>
            <th colspan="2">재료비</th>
            <th colspan="2">노무비</th>
            <th colspan="2">경비</th>
            <th colspan="2">합계</th>
            <th rowspan="2">PO</th>
            <th rowspan="2">비고</th>
          </tr>
          <tr>
            <th>단가</th>
            <th>금액</th>
            <th>단가</th>
            <th>금액</th>
            <th>단가</th>
            <th>금액</th>
            <th>단가</th>
            <th>금액</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="com in set.product.list">
            <td><input type="checkbox"></td>
            <td>{{ com.rel.model }}</td>
            <td>{{ com.rel.type | product_type }}</td>
            <td>{{ com.rel.size }}</td>
            <td>{{ com.qty }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <th colspan="5">소계</td>
            <td colspan="10">{{ set.product.total_final | number }}원</div>
          </tr>
        </tbody>
      </table>
    </div><!-- v-for -->
  </div><!-- #pdf-contents -->
  <div class="text-center margin-top-2">
    <button class="btn btn-primary">PO 파일 등록</button>
    <button class="btn btn-primary">PO 파일 변경</button>
    <button class="btn btn-default">PO 파일 삭제</button>
    <button class="btn btn-default" onclick="history.go(-1)">취소</button>
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
  el: '#sales-po-create',
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
