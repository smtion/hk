<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Mypage extends REST_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_logged_on()) {
      $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
    }
    if (!get_user_id()) {
      $this->response(NULL, REST_Controller::HTTP_FORBIDDEN);
    }

    $this->user_id = get_user_id();
  }

  //-----------------------------------------------------------------------
  // Info
  //-----------------------------------------------------------------------
  public function info_get()
  {
    $item = $this->db->select('u.name, d.name dept_name, p.name position_name, u.email, u.tel, u.phone')
            ->from('HK_users u')->join('HK_depts d', 'u.dept_id = d.id', 'left outer')
            ->join('HK_positions p', 'u.position_id = p.id', 'left outer')
            ->where('u.id', get_user_id())->get()->row_array();

    $response = [
      'item' => $item
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function info_patch()
  {
    $data = $this->patch();

    unset($date['dept_name']);
    unset($date['position_name']);
    $user = $this->db->where('id', $this->user_id)->get('HK_users')->row_array();
    if (hash('sha256', $data['password']) != $user['password']) {
      $this->response(NULL, REST_Controller::HTTP_FORBIDDEN);
    }

    if (isset($data['new_password'])) {
      $data['password'] = hash('sha256', $data['new_password']);
      unset($data['new_password']);
    }
    $result = $this->db->where('id', $this->user_id)->update('HK_company_info', $data);


    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // User list
  //-----------------------------------------------------------------------
  public function user_list_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->from('HK_users u')->join('HK_depts d', 'u.dept_id = d.id', 'left outer')->join('HK_positions p', 'u.position_id = p.id', 'left outer');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('u.name', $keyword);
      } elseif ($search == 'team') {
        $this->db->like('u.team', $keyword);
      } elseif ($search == 'dept') {
        $this->db->like('d.name', $keyword);
      } elseif ($search == 'name') {
        $this->db->like('p.position', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->select('u.id, u.name, u.dept_id, u.team, u.position_id, u.admin, u.email, u.tel, u.phone, u.permission, u.created_at,
            d.name dept_name, p.name position_name')->order_by('u.id desc')->get(null, $limit, $offset)->result_array();

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
}
