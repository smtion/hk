<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Admin extends REST_Controller {

  function __construct()
  {
    parent::__construct();

    if (!is_logged_on()) {
      $this->response(NULL, REST_Controller::HTTP_UNAUTHORIZED);
    }
    if (!has_permission('admin')) {
      $this->response(NULL, REST_Controller::HTTP_FORBIDDEN);
    }
  }

  //-----------------------------------------------------------------------
  // User
  //-----------------------------------------------------------------------
  public function user_get()
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

    $list = array_map(function ($item) {
      $item['permission'] = json_decode($item['permission']);
      return $item;
    }, $list);

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function user_dept_get()
  {
    $list = $this->db->get('HK_depts')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function user_position_get()
  {
    $list = $this->db->get('HK_positions')->result_array();

    $response = [
      'list' => $list
    ];

    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function user_post()
  {
    $data = $this->post();
    $data['password'] = hash('sha256', $data['password']);
    $data['permission'] = json_encode($data['permission'], JSON_UNESCAPED_UNICODE);
    $this->db->insert('HK_users', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function user_patch()
  {
    $data = $this->patch();
    if (isset($data['password'])) {
      $data['password'] = hash('sha256', $data['password']);
    }
    $data['permission'] = json_encode($data['permission'], JSON_UNESCAPED_UNICODE);
    $result = $this->db->where('id', $data['id'])->update('HK_users', $data);

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Dept
  //-----------------------------------------------------------------------
  public function dept_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_depts');
    $list = $this->db->order_by('id desc')->get('HK_depts', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function dept_post()
  {
    $data = $this->post();
    $this->db->insert('HK_depts', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function dept_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_depts', $data);

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Position
  //-----------------------------------------------------------------------
  public function position_get()
  {
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      $this->db->start_cache();
      $this->db->like($search, $keyword);
      $this->db->stop_cache();
    }

    $total = $this->db->count_all_results('HK_positions');
    $list = $this->db->order_by('id desc')->get('HK_positions', $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
  }

  public function position_post()
  {
    $data = $this->post();
    $this->db->insert('HK_positions', $data);

    activity_log();
    if ($this->db->insert_id()) $this->response(NULL, REST_Controller::HTTP_CREATED);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  public function position_patch()
  {
    $data = $this->patch();
    $result = $this->db->where('id', $data['id'])->update('HK_positions', $data);

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }

  //-----------------------------------------------------------------------
  // Login history
  //-----------------------------------------------------------------------
  public function login_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->from('HK_login_history h')->join('HK_users u', 'h.user_id = u.id')
    ->join('HK_depts d', 'u.dept_id = d.id', 'left outer')->join('HK_positions p', 'u.position_id = p.id', 'left outer');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('u.name', $keyword);
      } elseif ($search == 'dept') {
        $this->db->like('d.name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->select('h.*, u.name, d.name dept_name, p.name position_name')->order_by('h.id desc')->get(null, $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  //-----------------------------------------------------------------------
  // Activity log
  //-----------------------------------------------------------------------
  public function activity_get()
	{
    $page = $this->get('page');
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $this->db->start_cache();
    $this->db->from('HK_activity_log h')->join('HK_users u', 'h.user_id = u.id')
    ->join('HK_depts d', 'u.dept_id = d.id', 'left outer')->join('HK_positions p', 'u.position_id = p.id', 'left outer');
    if (($search = $this->get('search')) && ($keyword = $this->get('keyword'))) {
      if ($search == 'name') {
        $this->db->like('u.name', $keyword);
      } elseif ($search == 'dept') {
        $this->db->like('d.name', $keyword);
      }
    }
    $this->db->stop_cache();

    $total = $this->db->count_all_results();
    $list = $this->db->select('h.*, u.name, d.name dept_name, p.name position_name')->order_by('h.id desc')->get(null, $limit, $offset)->result_array();

    $response = [
      'paginate' => [
        'total' => $total,
        'page' => $page,
        'limit' => $limit
      ],
      'list' => $list
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  //-----------------------------------------------------------------------
  // Company
  //-----------------------------------------------------------------------
  public function company_get()
	{
    $item = $this->db->get('HK_company_info')->row_array();

    $response = [
      'item' => $item
    ];

    activity_log();
    $this->response($response, REST_Controller::HTTP_OK);
	}

  public function company_put()
  {
    $data = $this->put();

    if (isset($data['id']) && $data['id']) {
      $result = $this->db->where('id', $data['id'])->update('HK_company_info', $data);
    } else {
      $this->db->insert('HK_company_info', $data);
      $result = $this->db->insert_id();
    }

    activity_log();
    if ($result) $this->response(NULL, REST_Controller::HTTP_OK);
    else $this->response(NULL, REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
  }
}
