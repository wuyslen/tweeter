<?php
	class Tweets extends CI_Model
	{
		public function countByUser($id_usuario)
		{
			return $this->db->where('codigo_usuario', $id_usuario)->count_all_results('tweets');
		}

		public function insert($dados)				//criado ##########
		{
			$this->db->insert('tweets', $dados);
			return $this->db->insert_id();
		}

		public function getByCodigo($codigo)
		{
			return $this->db->where('codigo', $codigo)->get('tweets')->row();
		}

		public function getByTexto($texto)
		{
			return $this->db->where('texto', $texto)->get('tweets')->row();
		}

		public function getByData($data)
		{
			return $this->db->where('data_hora_postagem', $data)->get('tweets')->row();
		}

	}

/* End of file tweets.php */