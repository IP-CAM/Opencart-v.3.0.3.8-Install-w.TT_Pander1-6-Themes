<?php
class ModelExtensionTestimonialsTestimonials extends Model {

    public function createTables() {

        $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "extendons_testimonials ("
                . "testimonials_id int(111) AUTO_INCREMENT, "
                . "identifier varchar(250), "
                . "contact_name varchar(250), "
                . "contact_email varchar(250), "
                . "contact_website varchar(250), "
                . "contact_company varchar(250), "
                . "short_desc text, "
                . "testimonial_desc text, "
                . "is_featured smallint, "
                . "avatar text, "
                . "sort_order int(111), "
                . "meta_title varchar(250), "
                . "meta_keywords text, "
                . "meta_desc text, "
                . "curr_status smallint, "
                . "time_created DATETIME, "
                . "time_modified DATETIME, "
                . "PRIMARY KEY (testimonials_id))"
                . "ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;"
        );
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "extendons_testimonials_layout ("
                . "testimonials_id int(111) NOT NULL,"
                . "store_id int(11) NOT NULL,"
                . "layout_id int(11) NOT NULL,"
                . "PRIMARY KEY (testimonials_id, store_id)"
                . ") ENGINE=MyISAM DEFAULT CHARSET=utf8;"
        );

        $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "extendons_testimonials_store ("
                . "testimonials_id int(111) NOT NULL,"
                . "store_id int(11) NOT NULL,"
                . "PRIMARY KEY (testimonials_id, store_id)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=utf8;"
        );
        
        $this->db->query(
                "CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "extendons_testimonials_ratings_data ("
                . "`ratings_id` int(111) NOT NULL AUTO_INCREMENT,"
                . "`ratings_value` float DEFAULT NULL,"
                . "`votes` int(111) DEFAULT NULL,"
                . "`total_votes` int(111) DEFAULT NULL,"
                . "`user_id` int(111) DEFAULT NULL,"
                . "`session_id` varchar(250) DEFAULT NULL,"
                . "`store_id` int(111) DEFAULT NULL,"
                . "`testimonials_id` int(111) DEFAULT NULL,"
                . "`visitor_ip` varchar(200) DEFAULT NULL,"
                . "PRIMARY KEY (`ratings_id`)"
                . ") ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;"
        );
        
        $this->db->query(
                "CREATE TABLE " . DB_PREFIX . "extendons_testimonials_ratings ("
                . "`testimonials_id` int(111) NOT NULL,"
                . "`total_points` float DEFAULT NULL,"
                . "`number_votes` int(111) DEFAULT NULL,"
                . "`dec_avg` float DEFAULT NULL,"
                . "`whole_avg` float DEFAULT NULL,"
                . "PRIMARY KEY (`testimonials_id`)"
                . ") ENGINE=InnoDB DEFAULT CHARSET=latin1;"
        );
        /**
         * after all tables created
         * */
    }

    public function deleteTables() {
        
        $this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX ."extendons_testimonials;");
        $this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX ."extendons_testimonials_layout;");
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "extendons_testimonials_store;");
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "extendons_testimonials_ratings_data;");
        $this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "extendons_testimonials_ratings;");
    }
    
    public function editSettings($group, $data, $store_id = 0) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `code` = '" . $this->db->escape($group) . "'");

            foreach ($data as $key => $value) {
                    if (!is_array($value)) {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
                    } else {
                            $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `code` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
                    }
            }
    }

    public function getStores($testimonials_id) {
        $data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extendons_testimonials_store WHERE testimonials_id = '" . (int) $testimonials_id . "'");

        foreach ($query->rows as $result) {
            $data[] = $result['store_id'];
        }

        return $data;
    }

    public function getModuleLayouts($testimonials_id) {
        $data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extendons_testimonials_layout WHERE testimonials_id = '" . (int) $testimonials_id . "'");

        foreach ($query->rows as $result) {
            $data[$result['store_id']] = $result['layout_id'];
        }

        return $data;
    }

    public function isModuleInstalled($code) {
        $query = $this->db->query("SELECT extension_id FROM " . DB_PREFIX . "extension WHERE code='" . $code . "'");
        return $query->row;
    }
    
    public function getModuleStatus() {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE code = 'testimonials' AND `key` = 'testimonials_status'");
        return $query->row;
    }

    public function getTotal($data = array()) {

        $sql = "SELECT COUNT(DISTINCT t.testimonials_id) AS total FROM " . DB_PREFIX . "extendons_testimonials t";

        if (!isset($data['filters'])) {
            return;
        }
        
        $i = 1;
        foreach ($data['filters'] as $k => $v) {
           

            if ($v == NULL) {
                continue;
            }

            if ($i == 1) {
                $sql .= " WHERE t." . $k . "= '" . $v . "'";
            } else {
                $sql .= " AND t." . $k . "= '" . $v . "'";
            }

            $i++;
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getTestimonials($data = array()) {

        $query = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials t";

        if (isset($data['filters']) && !is_null($data['filters'])) {

            $i = 1;
            foreach ($data['filters'] as $k => $v) {

                if ($v == NULL) {
                    
                    continue; 
                }

                if ($i == 1) {
                    $query .= " WHERE t." . $k . " LIKE '" . $v . "'";
                } else {
                    $query .= " AND t." . $k . " LIKE '" . $v . "'";
                }

                $i++;
            }
        }

        if (isset($data['curr_status']) && !is_null($data['curr_status'])) {
            $query .= " AND t.curr_status = '" . (int) $data['curr_status'] . "'";
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
            $query .= " ORDER BY t.contact_name";
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

        return $q->rows;
    }

    public function getTestimonial($testimonials_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "extendons_testimonials t "
                . "WHERE t.testimonials_id = '" . (int) $testimonials_id . "'");

        return $query->row;
    }

    /**
     * check duplicate of the input in table
     * @param mix $input check duplicate value
     * @param string $table check for the table
     * @param string $column check for duplicate against the column
     * @param int $primary_id edit mode check
     * @param int $id current record id
     * @return bool $is_duplicate true if duplicate else false
     * */
    public function checkDuplicate($input, $table, $column, $primary_id = null, $id = null) {

        $is_duplicate = false;
        $sql = "SELECT * from " . DB_PREFIX . "" . $table . "
        WHERE " . $column . " = '" . $input . "'";

        if ($id != null) {
            $sql .= " AND " . $primary_id . " !=" . $id . "";
        }

        $query = $this->db->query($sql);

        if ($query->num_rows > 0) {
            //duplicate confirmed return true
            $is_duplicate = true;
        }

        return $is_duplicate;
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

    public function addTestimonial($data) {

        $sql = "INSERT INTO " . DB_PREFIX . "extendons_testimonials ";

        $sql .= "SET `contact_name` = '{$this->db->escape($data['contact_name'])}', 
            `contact_email` = '{$this->db->escape($data['contact_email'])}',
            `identifier` = '{$this->db->escape($data['identifier'])}',
            `contact_company` = '{$this->db->escape($data['contact_company'])}',
            `contact_website` = '{$this->db->escape($data['contact_website'])}',
            `short_desc` = '{$this->db->escape($data['short_desc'])}',"
                . "`testimonial_desc` = '{$this->db->escape($data['testimonial_desc'])}',"
                . "`is_featured` = '0',"
                . "`sort_order` = '{$this->db->escape($data['sort_order'])}',"
                . "`meta_title` = '{$this->db->escape($data['meta_title'])}',"
                . "`meta_desc` = '{$this->db->escape($data['meta_desc'])}',"
                . "`meta_keywords` = '{$this->db->escape($data['meta_keywords'])}',"
                . "`curr_status` = '{$this->db->escape($data['curr_status'])}',"
                . "`time_created` = now()";



        $this->db->query($sql);
        $last_id = $this->db->getLastId(); //echo $last_event_id;exit;

        if (isset($data['avatar'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "extendons_testimonials SET avatar = '" . $this->db->escape(html_entity_decode($data['avatar'], ENT_QUOTES, 'UTF-8')) . "' WHERE testimonials_id = '" . (int) $last_id . "'");
        }

        if (isset($data['testimonials_store'])) {
            foreach ($data['testimonials_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "extendons_testimonials_store SET testimonials_id = '" . (int) $last_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        // Set which layout to use with this category
        if (isset($data['module_layout'])) {
            foreach ($data['module_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "extendons_testimonials_layout SET testimonials_id = '" . (int) $last_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }
        if (isset($data['rating'])) {
            return $last_id;
        }
    }

    public function editTestimonials($testimonials_id, $data) {

        $sql = "UPDATE " . DB_PREFIX . "extendons_testimonials SET
                    `contact_name` = '{$this->db->escape($data['contact_name'])}', 
                    `contact_email` = '{$this->db->escape($data['contact_email'])}',
                    `identifier` = '{$this->db->escape($data['identifier'])}',
                    `contact_company` = '{$this->db->escape($data['contact_company'])}',
                    `contact_website` = '{$this->db->escape($data['contact_website'])}',
                    `short_desc` = '{$this->db->escape($data['short_desc'])}',"
                . "`testimonial_desc` = '{$this->db->escape($data['testimonial_desc'])}',"
                . "`is_featured` = '0',"
                . "`sort_order` = '{$this->db->escape($data['sort_order'])}',"
                . "`meta_title` = '{$this->db->escape($data['meta_title'])}',"
                . "`meta_desc` = '{$this->db->escape($data['meta_desc'])}',"
                . "`meta_keywords` = '{$this->db->escape($data['meta_keywords'])}',"
                . "`curr_status` = '{$this->db->escape($data['curr_status'])}',";



        $sql .= " `time_modified`= NOW() WHERE testimonials_id = '" . (int) $testimonials_id . "'";


        $this->db->query($sql);


        if (isset($data['avatar'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "extendons_testimonials SET avatar = '" . $this->db->escape(html_entity_decode($data['avatar'], ENT_QUOTES, 'UTF-8')) . "' WHERE testimonials_id = '" . (int) $testimonials_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "extendons_testimonials_store WHERE testimonials_id = '" . (int) $testimonials_id . "'");

        if (isset($data['testimonials_store'])) {
            foreach ($data['testimonials_store'] as $store_id) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "extendons_testimonials_store SET testimonials_id = '" . (int) $testimonials_id . "', store_id = '" . (int) $store_id . "'");
            }
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "extendons_testimonials_layout WHERE testimonials_id = '" . (int) $testimonials_id . "'");

        if (isset($data['module_layout'])) {
            foreach ($data['module_layout'] as $store_id => $layout) {
                if ($layout['layout_id']) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "extendons_testimonials_layout SET testimonials_id = '" . (int) $testimonials_id . "', store_id = '" . (int) $store_id . "', layout_id = '" . (int) $layout['layout_id'] . "'");
                }
            }
        }

        $this->cache->delete('testimonials');
    }

    public function delete($testimonials_id) {
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "extendons_testimonials WHERE testimonials_id = '" . (int) $testimonials_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "extendons_testimonials_store WHERE testimonials_id = '" . (int) $testimonials_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "extendons_testimonials_layout WHERE testimonials_id = '" . (int) $testimonials_id . "'");

        $this->cache->delete('testimonials');
    }

    // Check if user have already rate this testimonial
    public function isExistRatingByUser($testimonials_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "extendons_testimonials_ratings_data r "
                . "INNER JOIN " . DB_PREFIX . "extendons_testimonials_store ts "
                . "ON (r.testimonials_id=ts.testimonials_id) "
                . "WHERE ts.store_id=" . (($this->config->get('config_store_id')) ? $this->config->get('config_store_id') : 0) . " "
                . "AND r.testimonials_id='" . $testimonials_id . "'";

        // foreach ($data as $k => $v) {
        //     if ($v == null) {
        //         continue;
        //     }

        //     $sql .= " AND r." . $k . "='" . $v . "'";
        // }

        $query = $this->db->query($sql);
        $row = $query->row;

        return $row;
    }

    // Add rating against new testimonial
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
    // Add rating against new testimonial
    public function updateRatings($data) {

        $data_table = DB_PREFIX . "extendons_testimonials_ratings_data";
        $ratings_table = DB_PREFIX . "extendons_testimonials_ratings";

        try {

            $rating_rec = $this->isRatingExist($data['testimonials_id']);

            if ((bool) $rating_rec) {

                $query = $this->db->query(
                        "UPDATE " . $ratings_table . " SET"
                        . " total_points='" . $data['votes'] . "',"
                        . " dec_avg='" . $data['votes'] . "',"
                        . " whole_avg='" . $data['votes'] . "'"
                        . " WHERE testimonials_id='" . $data['testimonials_id'] . "'"
                );
                // $query = $this->db->query(
                //         "UPDATE " . $data_table . " SET"
                //         . " votes='" . $data['votes'] . "',"
                //         . " WHERE testimonials_id='" . $data['testimonials_id'] . "'"
                // );
            }

            return true;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    // Rating exist or not against this testimonial id
    public function isRatingExist($testimonials_id) {

        $ratings_table = DB_PREFIX . "extendons_testimonials_ratings";

        $sql = "SELECT * FROM " . $ratings_table . " "
                . "WHERE testimonials_id='" . $testimonials_id . "'";

        $query = $this->db->query($sql);

        return $query->row;
    }
}


