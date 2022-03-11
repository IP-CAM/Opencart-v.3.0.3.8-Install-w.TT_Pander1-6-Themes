<?php
class ModelExtensionModuleTestimonialsTestimonials extends Model {
    
    public function getTModuleStatus() {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE code = 'testimonials' AND `key` = 'testimonials_status'");
        return $query->row;
    }

    // Check if email exists already or not
    public function checkEmail_or_UrlDuplicate($email = null, $website_url = null) {

        $is_duplicate = false;
        $sql = "SELECT * from " . DB_PREFIX . "extendons_testimonials WHERE ";

        if (!empty($email) && $email != '') {

            $sql .= "`contact_email` = '{$this->db->escape($email)}'";

        } elseif (!empty($website_url) && $website_url != '') {

            $sql .= "`contact_website` = '{$this->db->escape($website_url)}'";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            //duplicate confirmed return true
            $is_duplicate = true;
        }

        return $is_duplicate;
    }

    public function getTestimonials($data = array()) {

        $query = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials t"
                . " INNER JOIN " . DB_PREFIX . "extendons_testimonials_store ts"
                . " ON(t.testimonials_id = ts.testimonials_id) "
                . "WHERE ts.store_id = " . $this->config->get('config_store_id')." AND t.curr_status = 1";

        if (isset($data['filters']) && !is_null($data['filters'])) {

            foreach ($data['filters'] as $k => $v) {

                if ($v == NULL) {
                    continue;
                }

                $query .= " AND t." . $k . " LIKE '" . $v . "'";
            }
        }

       // if (isset($data['primary_id']) && !is_null($data['primary_id'])) {
       //     $query .= " AND t.testimonials_id = '" . (int) $data['primary_id'] . "'";
       // }

        if (isset($data['status']) && !is_null($data['status'])) {
            $query .= " AND t.status = '" . (int) $data['status'] . "'";
        }

        $query .= " GROUP BY t.testimonials_id";

        $sort_data = array(
            't.contact_name',
            't.sort_order',
            't.cotact_company',
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $query .= " ORDER BY " . $data['sort'];
        } else {
            // $query .= " ORDER BY t.contact_name";
            $query .= " ORDER BY t.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $query .= " DESC";
        } else {
            $query .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $query .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $q = $this->db->query($query);
        // echo "<pre>";
        // print_r($q->rows);exit;
        return $q->rows;
    }

    public function getTotal($data = array()) {

        $sql = "SELECT COUNT(DISTINCT t.testimonials_id) AS total FROM " . DB_PREFIX . "extendons_testimonials t "
                . "LEFT JOIN " . DB_PREFIX . "extendons_testimonials_store ts "
                . "ON (t.testimonials_id = ts.testimonials_id) "
                . "WHERE ts.store_id = " . $this->config->get('config_store_id')." AND t.curr_status = 1";


        foreach ($data['filters'] as $k => $v) {

            if ($v == NULL) {
                continue;
            }

            $sql .= " AND t." . $k . "= '" . $v . "'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTestimonial($data = array()) {

        $sql = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials t"
                . " INNER JOIN " . DB_PREFIX . "extendons_testimonials_store ts"
                . " ON(t.testimonials_id = ts.testimonials_id) "
                . "WHERE ts.store_id = " . $this->config->get('config_store_id');

        if (isset($data['filters']) && !is_null($data['filters'])) {
            foreach ($data['filters'] as $k => $v) {
                $sql .= " AND t.$k ='" . $v . "'";
            }
        }
        //echo $sql;exit;
        $query = $this->db->query($sql);

        return $query->row;
    }

    public function isExistRatingByUser($testimonials_id, $data) {

        $sql = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials_ratings_data r "
                . "INNER JOIN " . DB_PREFIX . "extendons_testimonials_store ts "
                . "ON (r.testimonials_id=ts.testimonials_id) "
                . "WHERE ts.store_id=" . $this->config->get('config_store_id') . " "
                . "AND r.testimonials_id='" . $testimonials_id . "'";

        foreach ($data as $k => $v) {
            if ($v == null) {
                continue;
            }

            $sql .= " AND r." . $k . "='" . $v . "'";
        }

        $query = $this->db->query($sql);
        $row = $query->row;

        return $row;
    }

    public function isExistRatingByIP($testimonials_id, $data) {

        $sql = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials_ratings_data r "
                . "INNER JOIN " . DB_PREFIX . "extendons_testimonials_store ts "
                . "ON (r.testimonials_id=ts.testimonials_id) "
                . "WHERE ts.store_id=" . $this->config->get('config_store_id') . " "
                . "AND r.testimonials_id='" . $testimonials_id . "'";

        $sql .= " AND visitor_ip = '". $data['visitor_ip'] ."'";

        $query = $this->db->query($sql);
        $row = $query->row;

        return $row;
    }
    public function addRatings($data) {

        $data_table = DB_PREFIX . "extendons_testimonials_ratings_data";
        $ratings_table = DB_PREFIX . "extendons_testimonials_ratings";

        try {
            $sql = "INSERT INTO " . $data_table . " SET";

            foreach ($data as $k => $v) {
                if ($v == null) {
                    continue;
                }
                $sql .= " `" . $k . "`='" . $v . "',";
            }

            $sql = substr($sql, 0, -1);

            $this->db->query($sql);

            $last_id = $this->db->getLastId();


            $sqlr = "INSERT INTO " . $ratings_table;

            $sqlr .= " SET `total_points`='" . $data['votes'] . "',"
                    . " `testimonials_id`='" . $data['testimonials_id'] . "',"
                    . " `number_votes`='1'"
                    . " ON DUPLICATE KEY UPDATE"
                    . " total_points=total_points+" . $data['votes'] . ","
                    . " number_votes=number_votes+1,"
                    . " testimonials_id='" . $data['testimonials_id'] . "'";

            $this->db->query($sqlr);

            $rating_rec = $this->isRatingExist($data['testimonials_id']);

            if ((bool) $rating_rec) {
                $dec_avg = round($rating_rec['total_points'] / $rating_rec['number_votes'], 1);
                $whole_avg = round($dec_avg);

                $query = $this->db->query(
                        "UPDATE " . $ratings_table . " SET"
                        . " dec_avg='" . $dec_avg . "',"
                        . " whole_avg='" . $whole_avg . "'"
                        . " WHERE testimonials_id='" . $data['testimonials_id'] . "'"
                );
            }

            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function isRatingExist($testimonials_id) {

        $ratings_table = DB_PREFIX . "extendons_testimonials_ratings";

        $sql = "SELECT * FROM " . $ratings_table . " "
                . "WHERE testimonials_id='" . $testimonials_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }

    public function addTestimonial($data) {
        try {
            $sql = "INSERT INTO " . DB_PREFIX . "extendons_testimonials ";

            $sql .= "SET `contact_name` = '{$this->db->escape($data['contact_name'])}',"
                    . "`contact_email` = '{$this->db->escape($data['contact_email'])}',"
                    . "`contact_website` = '{$this->db->escape($data['contact_website'])}',"
                    . "`short_desc` = '{$this->db->escape($data['short_desc'])}',"
                    . "`testimonial_desc` = '{$this->db->escape($data['testimonial_desc'])}',"
                    . "`curr_status` = '".$this->config->get('conf_admin_approval')."',"
                    . "`time_created` = now()";


            $this->db->query($sql);


            $last_id = $this->db->getLastId(); //echo $last_event_id;exit;
            if (isset($data['avatar']) && !empty($data['avatar'])) {
                $this->db->query("UPDATE " . DB_PREFIX . "extendons_testimonials SET avatar = 'catalog/" . $this->db->escape(html_entity_decode($data['avatar'], ENT_QUOTES, 'UTF-8')) . "' WHERE testimonials_id = '" . (int) $last_id . "'");
            }


            if (isset($data['testimonials_store'])) {
                foreach ($data['testimonials_store'] as $store_id) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "extendons_testimonials_store SET testimonials_id = '" . (int) $last_id . "', store_id = '" . (int) $store_id . "'");
                }
            }
            if (isset($data['rating'])) {
                return $last_id; // return last id for the purpose of rating. store testimonial last id with rating value in testimonial rating table.
            }
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

}


