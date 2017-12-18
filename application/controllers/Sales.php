<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends EX_Controller {

  function __construct()
  {
    parent::__construct();
    $this->cdata->sidebar = 'sales/_sidebar';
  }

	public function index()
	{
    redirect('sales/currency');
	}

  public function currency()
	{
    $this->cdata->template = 'sales/currency';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function option()
	{
    $this->cdata->template = 'sales/option';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function material()
	{
    $this->cdata->template = 'sales/material';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function cost()
	{
    $this->cdata->template = 'sales/cost';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function product()
	{
    $this->cdata->template = 'sales/product';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function customer()
	{
    $this->cdata->template = 'sales/customer';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function project($id = 0)
	{
    $this->cdata->id = $id;
    $this->cdata->template = $id ? 'sales/project_view' : 'sales/project';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function project_create()
	{
    $this->cdata->template = 'sales/project_create';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function quotation($id = 0)
	{
    $this->cdata->id = $id;
    $this->cdata->template = $id ? 'sales/quotation_view' : 'sales/quotation';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function quotation_create($id = 0)
	{
    if (!$id) {
      redirect('/sales/quotation');
    }

    $this->cdata->id = $id;
    $this->cdata->template = 'sales/quotation_create';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function po()
	{
    $this->cdata->template = 'sales/po';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function po_create($id = 0)
	{
    if (!$id) {
      redirect('/sales/po');
    }

    $this->cdata->id = $id;
    $this->cdata->template = 'sales/po_create';
    $this->load->view(LAYOUT, $this->cdata);
	}

  public function request()
	{
    $this->cdata->template = 'sales/request';
    $this->load->view(LAYOUT, $this->cdata);
	}



  public function quotation_detail_download($id = 0)
  {
    $item = $this->db->select('qd.*, q.*, p.name proj_name, p.customer_id')->from('HK_quotation_detail qd')->join('HK_quotations q', 'qd.quotation_id = q.id')
            ->join('HK_projects p', 'q.project_id = p.id')->where('qd.id', $id)->get()->row_array();

    $sets = $this->db->where('qd_id', $id)->get('HK_quotation_sets')->result_array();
    $tmp = [];
    $index = 0;
    foreach ($sets as $set) {
      $coms = $this->db->where('qs_id', $set['id'])->where('index', $set['index'])->get('HK_quotation_set_components')->result_array();

      foreach ($coms as $com) {
        $lists = $this->db->where('qsc_id', $com['id'])->get('HK_quotation_set_component_list')->result_array();
        $tmp[$index][$com['type']] = $com;
        $tmp[$index][$com['type']]['list'] = $lists;

        $j = 0;
        foreach ($lists as $list) {
          $rel = $this->db->where('id', $list['rel_id'])->get("HK_{$list['type']}s")->row_array();
          if (isset($rel['details'])) {
            $rel['details'] = json_decode($rel['details']);
          }
          $tmp[$index][$com['type']]['list'][$j]['rel'] = $rel;
          $j++;
        }
      }

      $index++;
    }
    $item['set'] = $tmp;

    $project = $this->db->where('id', $item['project_id'])->get('HK_projects')->row_array();
    $company = $this->db->get('HK_company_info')->row_array();
    $customer = $this->db->where('id', $item['customer_id'])->get('HK_customers')->row_array();
    $user = $this->db->where('id', $item['user_id'])->get('HK_users')->row_array();

    $currency = [
      'kwn' => '대한민국(원)',
      'cny' => '중국 CNY',
      'jpy' => '일본 JPY',
      'usd' => '미국 USD',
      'eur' => '유로 EUR',
    ];
    $type = [
      '1' => 'Per\'f',
      '2' => 'Grating',
      '3' => 'Blind'
    ];

    // Excel
    $this->load->library('PHPExcel');
    $rowIndex = 1;
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '수신자')
      ->setCellValue('C' . $rowIndex, '공급자');
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '회사명')
      ->setCellValue('B' . $rowIndex, $customer['corp_name'])
      ->setCellValue('C' . $rowIndex, '회사명')
      ->setCellValue('D' . $rowIndex, $company['corp_name']);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '담당자')
      ->setCellValue('B' . $rowIndex, $customer['name'])
      ->setCellValue('C' . $rowIndex, '대표자')
      ->setCellValue('D' . $rowIndex, $company['name']);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '전화번호')
      ->setCellValue('B' . $rowIndex, $customer['tel'])
      ->setCellValue('C' . $rowIndex, '사업자등록번호')
      ->setCellValue('D' . $rowIndex, $company['reg_no']);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '이메일주소')
      ->setCellValue('B' . $rowIndex, $customer['email'])
      ->setCellValue('C' . $rowIndex, '담당자')
      ->setCellValue('D' . $rowIndex, $user['name']);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('C' . $rowIndex, '담당자 연락처')
      ->setCellValue('D' . $rowIndex, $user['phone']);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('C' . $rowIndex, '담당자 이메일')
      ->setCellValue('D' . $rowIndex, $user['email']);
    $rowIndex++;
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '견적번호')
      ->setCellValue('B' . $rowIndex, $item['code'])
      ->setCellValue('C' . $rowIndex, 'Payment Term')
      ->setCellValue('D' . $rowIndex, $item['payment_term'] . '일');
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '프로젝트 이름')
      ->setCellValue('B' . $rowIndex, $item['proj_name'])
      ->setCellValue('C' . $rowIndex, '견적서 유효기간')
      ->setCellValue('D' . $rowIndex, '발행 후 ' . $item['expiry_day'] . '일');
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '견적서 발행일')
      ->setCellValue('B' . $rowIndex, $item['publish_date'])
      ->setCellValue('C' . $rowIndex, '적용 통화')
      ->setCellValue('D' . $rowIndex, $currency[$item['currency']]);
    $rowIndex++;
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $rowIndex, '배송 납품기일')
      ->setCellValue('B' . $rowIndex, $item['delivery_term'] . '일')
      ->setCellValue('C' . $rowIndex, '공사 이름')
      ->setCellValue('D' . $rowIndex, $item['construct_name']);

    $i = 0;
    foreach ($item['set'] as $set) {
      if (isset($set['product'])) {
        $rowIndex++;
        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A' . $rowIndex, '제품');
        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('A' . $rowIndex, 'Model')
          ->setCellValue('B' . $rowIndex, 'Type')
          ->setCellValue('C' . $rowIndex, 'Dimension')
          ->setCellValue('D' . $rowIndex, 'Concentrated Load 2mm delfection @ 1/2 edge')
          ->setCellValue('E' . $rowIndex, 'Ultimate Load @ 1/2 edge')
          ->setCellValue('F' . $rowIndex, 'Open Ratio (%)')
          ->setCellValue('G' . $rowIndex, 'Conductivity')
          ->setCellValue('H' . $rowIndex, '면적')
          ->setCellValue('I' . $rowIndex, '수량')
          ->setCellValue('J' . $rowIndex, '단가')
          ->setCellValue('K' . $rowIndex, '금액');

        foreach ($set['product']['list'] as $com) {
          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $rowIndex, $com['rel']['model'])
            ->setCellValue('B' . $rowIndex, $type[$com['rel']['type']])
            ->setCellValue('C' . $rowIndex, $com['rel']['size'])
            ->setCellValue('D' . $rowIndex, $com['rel']['edge_cl'])
            ->setCellValue('E' . $rowIndex, $com['rel']['edge_ul'])
            ->setCellValue('F' . $rowIndex, $com['rel']['ph_ratio'])
            ->setCellValue('G' . $rowIndex, '')
            ->setCellValue('H' . $rowIndex, $com['size'])
            ->setCellValue('I' . $rowIndex, $com['qty'])
            ->setCellValue('J' . $rowIndex, $com['sales_price_dc'] . '원')
            ->setCellValue('K' . $rowIndex, $com['total_dc']. '원');
        }

        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('J' . $rowIndex, '합계')
          ->setCellValue('K' . $rowIndex, $set['product']['total'] . '원');
        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('J' . $rowIndex, '절사')
          ->setCellValue('K' . $rowIndex, $set['product']['rest'] . '원');
        $rowIndex++;
        $objPHPExcel->setActiveSheetIndex(0)
          ->setCellValue('J' . $rowIndex, '최종 합계')
          ->setCellValue('K' . $rowIndex, $set['product']['total_final'] . '원');
      }

      $col = ['B', 'C', 'D', 'E', 'F', 'G', 'H'];
      $loop = ['option', 'material', 'cost'];

      foreach ($loop as $type) {
        if (isset($set[$type])) {
          $rowIndex++;
          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $rowIndex, '옵션');
          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $rowIndex, '옵션명')
            ->setCellValue('B' . $rowIndex, '옵션상세')
            ->setCellValue('I' . $rowIndex, '수량')
            ->setCellValue('J' . $rowIndex, '단가')
            ->setCellValue('K' . $rowIndex, '금액');

          foreach ($set[$type]['list'] as $com) {
            $rowIndex++;
            $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('A' . $rowIndex, $com['rel']['name']);

            $colSeq = 0;
            foreach ($com['rel']['details'] as $k => $v) {
              $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col[$colSeq] . $rowIndex, $k);
              $objPHPExcel->setActiveSheetIndex(0)->setCellValue($col[$colSeq] . ($rowIndex + 1), $v);
            };

            $objPHPExcel->setActiveSheetIndex(0)
              ->setCellValue('I' . $rowIndex, $com['qty'])
              ->setCellValue('J' . $rowIndex, $com['sales_price_dc'] . '원')
              ->setCellValue('K' . $rowIndex, $com['total_dc']. '원');
            $rowIndex++;
          }

          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J' . $rowIndex, '합계')
            ->setCellValue('K' . $rowIndex, $set[$type]['total'] . '원');
          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J' . $rowIndex, '절사')
            ->setCellValue('K' . $rowIndex, $set[$type]['rest'] . '원');
          $rowIndex++;
          $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('J' . $rowIndex, '최종 합계')
            ->setCellValue('K' . $rowIndex, $set[$type]['total_final'] . '원');
        }
      }
    }

    $objPHPExcel->getActiveSheet()->setTitle($item['proj_name'] . '_견적서');
    $objPHPExcel->setActiveSheetIndex(0);
    $filename = iconv('UTF-8', 'EUC-KR', $item['proj_name'] . '_견적서');
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename='.$filename.'.xls');
    header('Cache-Control: max-age=0');
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
  }
}
