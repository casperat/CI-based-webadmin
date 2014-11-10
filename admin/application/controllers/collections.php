<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Collections extends CI_Controller {

    public function __construct() {

        parent::__construct();

        if ((int) $this->session->userdata("admin_id") <= 0)
            redirect("users/login");

        $this->load->model("collections_model", "collections");
        $this->load->library("image_lib");

        $this->views = array(
                        'list'  => "collections/list_view.tpl",
                        'add'   => "collections/add_record.tpl",
                        'edit'  => "collections/add_record.tpl"
                        );
    }

    public function view($id) {

        $this->load->library("paginator");

        $collection = $this->collections->get($id);

        $setup = array("page" => max((int) $this->input->get("p"), 1));
        $sq = $this->input->get("q");
        if ($sq)
            $setup['where']['like'] = array($collection['name_equiv_for_pk'], $sq);

        $collection['records'] = $this->collections->get_records($collection, $setup);
        $collection['page'] = $setup['page'];
        $collection['controller'] = (! strlen($collection['custom_controller']) ? "collections" : $collection['custom_controller']);

        $setup['collection_name'] = $collection['name'];
        $pg_config = array(
                        "page" => $collection['page'],
                        "total_pages" => ceil($this->collections->count_all_results($setup) / $collection['per_page']),
                        "link" => $collection['controller'] ."/view/". $collection['id']);
        $collection['pagination'] = $this->paginator->render_with_get($pg_config);

        $page['content'] = $this->load->view($this->views['list'], $collection, true);

        $page['collections'] = $this->collections->get_collections_menu();
        $page['selected_id'] = $collection['id'];

        $headers = array(
                    "meta_title" => "View {$collection['caption']} | Admin",
                    "meta_description" => $this->config->item("site_title"),
                    "meta_keywords" => "");

        $this->load->view('headers.tpl', $headers);

        $page['body_class'] = "index";
        $this->load->view('index.tpl', $page);
    }

    public function add($collection_id) {

        $collection = $this->collections->get($collection_id);

        if (is_null($collection))
            show_404();

        $collection['controller'] = (! strlen($collection['custom_controller']) ? "collections" : $collection['custom_controller']);
        $collection['form_action'] = $collection['controller'] ."/add_send/{$collection['id']}";

        $collection['fields'] = $this->collections->get_fields($collection_id);
        $collection['tabs'] = $this->collections->get_tabs($collection_id);

        $page['content'] = $this->load->view($this->views['add'], $collection, true);

        $page['collections'] = $this->collections->get_collections_menu();
        $page['selected_id'] = $collection['id'];

        $headers = array(
                    "meta_title" => "Add {$collection['caption']} | Admin",
                    "meta_description" => $this->config->item("site_title"),
                    "meta_keywords" => "");

        $this->load->view('headers.tpl', $headers);

        $page['body_class'] = "index";
        $this->load->view('index.tpl', $page);
    }

    public function add_send($collection_id) {

        $collection = $this->collections->get($collection_id);

        if (is_null($collection))
            show_404();

        $collection['controller'] = (! strlen($collection['custom_controller']) ? "collections" : $collection['custom_controller']);

        $fields = $this->collections->get_fields($collection_id);

        $data = array();
        foreach ($fields as $field) {
            if (! strlen(trim($this->input->post($field['name']))) || $field['visible'] == 'No')
                continue ;

            $data[$field['name']] = $this->input->post($field['name']);
            if ($field['type'] == "file" || $field['type'] == "image") {

                $data[$field['name']] = move_upload_file($field, $data[$field['name']]);
            }
        }

        $this->db->insert($collection['name'], $data);
        $row_id = $this->db->insert_id();

        // make the programming for the rest of the tabs
        foreach ($this->input->post("tabs") as $tid => $tab) {

            $tab_collection = $this->collections->get($tid);
            $fields = $this->collections->get_fields($tid);

            $tab_details = $this->collections->get_tab_details($collection_id, $tid);

            $insert = array();

            foreach ($tab as $id => $record) {

                if ($id == "dummy")
                    continue ;

                $data = array();
                foreach ($fields as $field) {

                    if ($field['is_pk'] == 'Yes' || $field['visible'] == 'No')
                        continue ;

                    switch ($field['type']) {

                        case "file" :
                        case "image" :  $data[$field['name']] = move_upload_file($field, $record[$field['name']]);
                                        break;

                        case "fk" :     $data[$field['name']] = ($tab_details['tab_fk'] == $field['name'] ? $row_id : $record[$field['name']]);
                                        break;

                        default : $data[$field['name']] = $record[$field['name']];
                    }
                }

                $insert[] = $data;
            }

            if (is_array($insert) && count($insert))
                $this->db->insert_batch($tab_collection['name'], $insert);
        }

        if ($this->input->post("save_ret"))
            redirect("{$collection['controller']}/view/{$collection['id']}");
        elseif ($this->input->post("save_add"))
            redirect("{$collection['controller']}/add/{$collection['id']}");
        else
            redirect("{$collection['controller']}/edit/{$collection['id']}/$row_id");
    }

    public function edit($collection_id, $row_id) {

        $collection = $this->collections->get($collection_id);

        if (is_null($collection))
            show_404();

        $row = $this->collections->get_record($collection, $row_id);

        if (is_null($row))
            show_404();

        $collection['row_id'] = $row['id'];

        $collection['controller'] = (! strlen($collection['custom_controller']) ? "collections" : $collection['custom_controller']);
        $collection['form_action'] = $collection['controller'] ."/edit_send/{$collection['id']}/{$row['id']}";

        $collection['fields'] = $this->collections->get_fields($collection_id, $row);
        $collection['tabs'] = $this->collections->get_tabs($collection_id, $row['id']);

        $page['content'] = $this->load->view($this->views['edit'], $collection, true);

        $page['collections'] = $this->collections->get_collections_menu();
        $page['selected_id'] = $collection['id'];

        $headers = array(
                    "meta_title" => "Edit {$collection['caption']} | Admin",
                    "meta_description" => $this->config->item("site_title"),
                    "meta_keywords" => "");

        $this->load->view('headers.tpl', $headers);

        $page['body_class'] = "index";
        $this->load->view('index.tpl', $page);
    }

    public function edit_send($collection_id, $row_id) {

        $collection = $this->collections->get($collection_id);

        $row = $this->collections->get_record($collection, $row_id);

        if (is_null($collection) || is_null($row))
            show_404();

        $collection['controller'] = (! strlen($collection['custom_controller']) ? "collections" : $collection['custom_controller']);

        $fields = $this->collections->get_fields($collection_id);
        $data = array();

        foreach ($fields as $field) {
            if ($field['is_pk'] === 'Yes' || $this->input->post($field['name']) === NULL || $field['visible'] == 'No')
                continue ;

            $data[$field['name']] = $this->input->post($field['name']);

            if (($field['type'] == "file" || $field['type'] == "image") && $row[$field['name']] != $data[$field['name']]) {
                if ($this->input->post($field['name'] ."_fs") == "Yes")
                    $data[$field['name']] = move_upload_file($field, $data[$field['name']]);
            }
        }

        $this->db->update($collection['name'], $data, array($collection['pk'] => $row[$collection['pk']]));
        $row_id = $row[$collection['pk']];

        // make the programming for the rest of the tabs
        foreach ($this->input->post("tabs") as $tid => $tab) {

            $update_ids = $insert = $update = array();
            $tab_collection = $this->collections->get($tid);
            $fields = $this->collections->get_fields($tid);

            $tab_details = $this->collections->get_tab_details($collection_id, $tid);

            foreach ($tab as $id => $record) {

                if ($id == (int) $id)
                    $tab_row = $this->collections->get_record($tab_collection, $id);

                $data = array();

                foreach ($fields as $field) {

                    if ($field['is_pk'] == 'Yes' || $field['visible'] == 'No')
                        continue ;
                    elseif ($tab_details['tab_fk'] == $field['name'])
                        $fk_field = $field;

                    switch ($field['type']) {

                        case "file" :
                        case "image" :  if ($tab_row[$field['name']] != $record[$field['name']] && $record[$field['name'] ."_fs"] == "Yes")
                                            $data[$field['name']] = move_upload_file($field, $record[$field['name']]);
                                        else
                                            $data[$field['name']] = $record[$field['name']];
                                        break;

                        case "fk" :     $data[$field['name']] = ($tab_details['tab_fk'] == $field['name'] ? $row_id : $record[$field['name']]);
                                        break;

                        default : $data[$field['name']] = $record[$field['name']];
                    }
                }

                if ($id == "dummy")
                    continue ;

                if (substr($id, 0, 3) == "fk_") {
                    $insert[] = $data;
                }
                else {
                    $update_ids[] = $id;
                    $update[$id] = $data;
                    //$this->db->update($tab_collection['name'], $data, array($tab_collection['pk'] => $id));
                }
            }

            // delete unassociated records
            $this->db->where($fk_field['name'], $row_id);
            if (count($update_ids)) {
                $this->db->where_not_in($tab_collection['pk'], $update_ids);
            }
            $this->db->delete($tab_collection['name']);

            // update records
            if (is_array($update) && count($update))
                foreach ($update as $id => $data)
                    $this->db->update($tab_collection['name'], $data, array($tab_collection['pk'] => $id));

            // and insert new ones
            if (is_array($insert) && count($insert))
                $this->db->insert_batch($tab_collection['name'], $insert);
        }

        if ($this->input->post("save_ret"))
            redirect("{$collection['controller']}/view/{$collection['id']}");
        elseif ($this->input->post("save_add"))
            redirect("{$collection['controller']}/add/{$collection['id']}");
        else
            redirect("{$collection['controller']}/edit/{$collection['id']}/$row_id");
    }

    public function delete($collection_id, $record_id) {

        $collection = $this->collections->get($collection_id);

        if (is_null($collection))
            show_404();

        $record = $this->collections->get_record($collection, $record_id);

        if (is_null($record))
            show_404();

        $this->db->where($collection['pk'], $record[$collection['pk']]);
        $this->db->delete($collection['name']);

        redirect($this->input->server('HTTP_REFERER'));
    }

    public function upload($col_id) {

        $field = $this->collections->get_field($col_id);

        $ret['files'] = array();

        if (is_null($field)) {
            $ret['error'] = "The file could not be uploaded.";
            echo json_encode($ret);
            return ;
        }

        if (count($_FILES) == 0) {
            $ret['error'] = "You did not select a file for upload.";
            echo json_encode($ret);
            return ;
        }

        $config['upload_path'] = '../files/upload/temp/';

        if ($field['type'] == 'image') {
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']    = '15000';
        }
        else {
            $config['allowed_types'] = 'pdf|zip|mp3|aif|aiff|bmp|gif|jpeg|jpg|png|tiff|tif|txt|text|rtf|mpeg|mpg|mov|avi|doc|docx|mp4|flv';
            $config['max_size']    = '100000';
        }

        $this->load->library('upload', $config);

        foreach ($_FILES as $id => $file_data) {

            if ( ! is_dir($config['upload_path'])) {
                @mkdir ($config['upload_path']);
                @chmod ($config['upload_path'], 0777);
            }

            // upload image
            if ( ! $this->upload->do_upload($id))
            {
                $upload_error = array('error' => $this->upload->display_errors('', ''));
                $ret['error'] = implode("\n", $upload_error);
            }
            else
            {
                $data = $this->upload->data();

                if ($field['type'] == 'image') {
                    $thumb = $this->image_lib->make_thumb($data['full_path'], 75, 75);

                    list($width, $height) = getimagesize($data['full_path']);
                    if (((int) $field['max_height'] > 0 && $field['max_height'] < $height) || ((int) $field['max_width'] > 0 && $field['max_width'] < $width)) {

                        $this->image_lib->clear();
                        $this->image_lib->make_thumb($data['full_path'], (int) $field['max_width'], (int) $field['max_height'], true);
                    }
                }
                else
                    $thumb = $this->collections->get_thumb($data['full_path']);

                $base_url = base_url("files/upload/temp/". $data['file_name']);

                $field['image_rel_path'] = (strlen($field['image_rel_path']) ? $field['image_rel_path'] : "files/");

                $ret['files'][] = array(
                                    "name" => $data['file_name'],
                                    "path" => trim($field['image_rel_path'], "/") ."/",
                                    "size" => $data['file_size'] * 1024,
                                    "url" => $base_url,
                                    "thumbnail_url" => str_replace($this->config->item("site_path"), "../", $thumb),
                                    "delete_url" => $base_url,
                                    "delete_type" => "DELETE");
            }
        }

        echo json_encode($ret);
    }

    public function upload_multiple($col_id) {

        $field = $this->collections->get_field($col_id);

        $ret['files'] = array();

        if (is_null($field)) {
            $ret['error'] = "The file could not be uploaded.";
            echo json_encode($ret);
            return ;
        }

        if (count($_FILES) == 0) {
            $ret['error'] = "You did not select a file for upload.";
            echo json_encode($ret);
            return ;
        }
        else {
            foreach ($_FILES as $input_id => $item) {
                $files[$input_id] = array(
                                        "name" => $item['name'][0],
                                        "type" => $item['type'][0],
                                        "tmp_name" => $item['tmp_name'][0],
                                        "error" => $item['error'][0]);
            }

            $_FILES = $files;
        }

        $config['upload_path'] = '../files/upload/temp/';

        if ($field['type'] == 'image') {
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size']    = '10000';
        }
        else {
            $config['allowed_types'] = 'pdf|zip|mp3|aif|aiff|bmp|gif|jpeg|jpg|png|tiff|tif|txt|text|rtf|mpeg|mpg|mov|avi|doc|docx|mp4|flv';
            $config['max_size']    = '100000';
        }

        $this->load->library('upload', $config);

        foreach ($_FILES as $id => $file_data) {

            if ( ! is_dir($config['upload_path'])) {
                @mkdir ($config['upload_path']);
                @chmod ($config['upload_path'], 0777);
            }

            // upload image
            if ( ! $this->upload->do_upload($id))
            {
                $upload_error = array('error' => $this->upload->display_errors('', ''));
                $ret['error'] = implode("\n", $upload_error);
            }
            else
            {
                $data = $this->upload->data();

                if ($field['type'] == 'image') {
                    $thumb = $this->image_lib->make_thumb($data['full_path'], 75, 75);

                    list($width, $height) = getimagesize($data['full_path']);
                    if (((int) $field['max_height'] > 0 && $field['max_height'] < $height) || ((int) $field['max_width'] > 0 && $field['max_width'] < $width)) {

                        $this->image_lib->clear();
                        $this->image_lib->make_thumb($data['full_path'], (int) $field['max_width'], (int) $field['max_height'], true);
                    }
                }
                else
                    $thumb = $this->collections->get_thumb($data['full_path']);

                $base_url = base_url("files/upload/temp/". $data['file_name']);

                $field['image_rel_path'] = (strlen($field['image_rel_path']) ? $field['image_rel_path'] : "files/");

                $ret['files'][] = array(
                                    "name" => $data['file_name'],
                                    "field_name" => $field['name'],
                                    "path" => trim($field['image_rel_path'], "/") ."/",
                                    "size" => $data['file_size'] * 1024,
                                    "url" => $base_url,
                                    "thumbnail_url" => str_replace($this->config->item("site_path"), "../", $thumb),
                                    "delete_url" => $base_url,
                                    "delete_type" => "DELETE");
            }
        }

        echo json_encode($ret);
    }

    public function upload_custom() {

        $ret['files'] = array();

        if (count($_FILES) == 0) {
            $ret['error'] = "You did not select a file for upload.";
            echo json_encode($ret);
            return ;
        }

        $config['upload_path'] = '../files/upload/temp/';

        //$config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|zip|mp3|aif|aiff|bmp|gif|jpeg|jpg|png|tiff|tif|txt|text|rtf|mpeg|mpg|mov|avi|doc|docx|mp4|flv|csv';
        $config['allowed_types'] = "*";
        $config['max_size']    = '100000';

        $this->load->library('upload', $config);

        foreach ($_FILES as $id => $file_data) {

            if ( ! is_dir($config['upload_path'])) {
                @mkdir ($config['upload_path']);
                @chmod ($config['upload_path'], 0777);
            }

            // upload image
            if ( ! $this->upload->do_upload($id))
            {
                $upload_error = array('error' => $this->upload->display_errors('', ''));
                $ret['error'] = implode("\n", $upload_error);
            }
            else
            {
                $data = $this->upload->data();

                $thumb = $this->collections->get_thumb($data['full_path']);

                $base_url = base_url("files/upload/temp/". $data['file_name']);

                $ret['files'][] = array(
                                    "name" => $data['file_name'],
                                    "path" => "files/",
                                    "size" => $data['file_size'] * 1024,
                                    "url" => $base_url,
                                    "thumbnail_url" => str_replace($this->config->item("site_path"), "../", $thumb),
                                    "delete_url" => $base_url,
                                    "delete_type" => "DELETE");
            }
        }

        echo json_encode($ret);
    }
}

/* End of file collections.php */
/* Location: ./admin/application/controllers/collections.php */