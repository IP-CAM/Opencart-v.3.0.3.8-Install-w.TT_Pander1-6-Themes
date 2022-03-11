<?php

class ModelExtensionExtensionRedirect extends Model {

	public function getTotalPages() {

			$sql = "select count(*) as total from " . DB_PREFIX . "autoredirect";

			$query = $this->db->query($sql);

			return $query->row['total'];

	}

	public function getPages($data) {

			$sql = "select * from " . DB_PREFIX . "autoredirect order by date desc";

			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}

				$sql .= " limit " . (int)$data['start'] . "," . (int)$data['limit'];
			}

			$query = $this->db->query($sql);

			return $query->rows;

	}

}
?>
