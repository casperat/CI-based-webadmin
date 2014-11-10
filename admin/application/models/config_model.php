<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Config_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    function get_collections() {

        $this->db->order_by("category_id, order");
        $r = $this->db->get("wa_tables");

        if ($r->num_rows()) {

            $collections = array();

            foreach ($r->result_array() as $collection) {

                $fr = $this->db->get_where("wa_cols", array("id_table" => $collection['id']));
                $collection['fields'] = $fr->result_array();

                $collections[] = $collection;
            }

            return $collections;
        }

        return null;
    }

    function get_collection($id) {

        $r = $this->db->get_where("wa_tables", array("id" => $id));

        if ($r->num_rows()) {

            $collection = $r->row_array();

            $this->db->order_by("id_table, order");
            $r = $this->db->get_where("wa_cols", array("id_table" => $id));
            $collection['fields'] = $r->result_array();

            return $collection;
        }

        return null;
    }

    function get_pks() {

        $r = $this->db->query("SELECT a.*, b.`name` as `table_name`
                            FROM `wa_cols` as a
                            INNER JOIN `wa_tables` as b ON a.`id_table`=b.`id`
                            WHERE a.`is_pk`='Yes'");

        if ($r->num_rows()) {

            $pks = array();
            foreach ($r->result_array() as $item)
                $pks[$item['id_table']] = $item['table_name'] .".". $item['name'];

            return $pks;
        }

        return null;
    }

    function get_tables() {

        $r = $this->db->query("SHOW TABLES");

        if ($r->num_rows()) {
            $result = array();

            foreach ($r->result_array() as $row) {
                $result[] = array_pop($row);
            }

            return $result;
        }

        return null;
    }

    function get_fields($table_id = null) {

        if ((int) $table_id)
            $this->db->where("id_table", $table_id);

        $this->db->order_by("id_table, order");
        $r = $this->db->get("wa_cols");

        if ($r->num_rows())
            return $r->result_array();

        return null;
    }

    function get_categories() {

        $this->db->order_by("order");
        $r = $this->db->get("wa_categories");

        if ($r->num_rows())
            return $r->result_array();

        return null;
    }

    function get_tabs($parent_id = null) {

        if ((int) $parent_id > 0)
            $this->db->where("parent_id", $parent_id);

        $this->db->order_by("parent_id, order");
        $r = $this->db->get("wa_tabs");

        if ($r->num_rows())
            return $r->result_array();

        return null;
    }
}

/* End of file config_model.php */
/* Location ./admin/application/models/config_model.php */