<?php

class ModelExtensionExtensionNotFoundReport extends Model {

	public function getTotalPages() {

			$sql = "select count(*) as total from " . DB_PREFIX . "404s_report a
					inner join
					(select link, max(date) as maxdate from " . DB_PREFIX . "404s_report group by link) b on a.link = b.link and a.date = b.maxdate";

			$query = $this->db->query($sql);

			return $query->row['total'];

	}

	public function getPages($data) {

			$sql = "select a.* from " . DB_PREFIX . "404s_report a
					inner join
					(select link, max(date) as maxdate from " . DB_PREFIX . "404s_report group by link) b on a.link = b.link and a.date = b.maxdate order by b.maxdate desc";

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
