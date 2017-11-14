<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Purchase extends REST_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_logged_on()) {
      $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
    }
  }

  public function option_detail_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
      // if ($search == 'name') $this->db->like('name', $keyword);
      // if ($search == 'code') $this->db->like('code', $keyword);
      // if ($search == 'type') $this->db->like('type', $keyword);
    }

    $total = $this->db->count_all_results('HK_options');
    $list = $this->db->get('HK_options', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function option_name_get()
  {
    $list = $this->db->select('name')->group_by('name')->get('HK_options')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_detail_post()
  {
    $data = $this->post();
    // var_dump($data);
    // $type = $this->post('type');
    // $desc = $this->post('desc');
    // $this->db->where('id', )->get('HK_options');

    $new = [
      'name' => isset($data['option']['name']) ? $data['option']['name'] : $data['name'],
      'type' => $data['type'],
      'values' => json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE),
    ];
    // var_dump($new);
    $this->db->insert('HK_options', $new);
    $new_id = $this->db->insert_id();

    $code = str_pad($new_id, 6, '0', STR_PAD_LEFT);
    $this->db->where('id', $new_id)->update('HK_options', ['code' => $code]);

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_detail_patch()
  {
    $data = $this->patch();

    $data['values'] = json_encode(array_filter($data['values'], function ($v) { if (trim($v) ) return $v; }), JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_options', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //
  public function option_list_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
      // if ($search == 'name') $this->db->like('name', $keyword);
      // if ($search == 'code') $this->db->like('code', $keyword);
      // if ($search == 'type') $this->db->like('type', $keyword);
    }

    $total = $this->db->count_all_results('HK_option_list');
    $list = $this->db->get('HK_option_list', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_detail2_get()
  {
    $name = $this->get('name');
    $list = $this->db->where('name', $name)->get('HK_options')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function option_list_post()
  {
    $data = $this->post();

    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    // var_dump($new);
    $this->db->insert('HK_option_list', $data);
    $new_id = $this->db->insert_id();

    if ($new_id) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function option_list_patch()
  {
    $data = $this->patch();

    $data['details'] = json_encode($data['details'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_option_list', $data);

    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
