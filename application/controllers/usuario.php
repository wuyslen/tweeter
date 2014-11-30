<?php
	class Usuario extends CI_Controller
	{
		private $erro;

		public function __construct()
		{
			parent::__construct();

			$this->load->model('Usuarios');
			$this->load->model('Tweets');		//criado ##########
			$this->load->model('Seguidores');	//criado ##########

			$this->erro = '';
		} // function

		public function index()
		{
			// inicializa o vetor de dados
			$dados = array();
			$dados['nome'] = '';
			$dados['email'] = '';
			$dados['login'] = '';
			$dados['erro'] = $this->erro;

			// se dados foram submetidos
			if($this->input->post('nome'))
			{
				// captura dos dados submetidos
				$dados['nome'] = $this->input->post('nome');
				$dados['email'] = $this->input->post('email');
			}

			if ($this->input->post('login'))
			{
				$dados['login'] = $this->input->post('login');
			}

			// carrega a view passando o vetor de dados
			$this->load->view('inicio', $dados);
		} // function

		public function criarconta()
		{
			// regras para validação do formulário
			$this->_set_validation_rules('criarconta');

			// se o a validação do formulário foi bem sucedida
			if ($this->form_validation->run())
			{
				// insere os dados no bd
				$id = $this->Usuarios->insert(
					$this->input->post());

				// monta o link para complementar a conta
				$link = base_url() . 'usuario/completarconta/' .
					$id . '/' . md5($this->input->post('email'));

				// exibe o link
				echo '<a href="' . $link . '">' . $link . '</a>';
			} // if
			else 
			{
				// recarrega o formulário para exibir os erros de validação
				$this->index();
			}
		} // function

		public function autenticar()
		{
			$this->_set_validation_rules('autenticar');

			if($this->form_validation->run())
			{
				log_message('debug', 'Form validated.');
				if(strpos($this->input->post('login'), '@'))
				{
					$usuario = $this->Usuarios->
					getByEmail($this->input->post('login'));
				}
				else 
				{
					$usuario = $this->Usuarios->
					getByLogin($this->input->post('login'));
				}

				if(!$usuario)
				{
					$this->erro = 'Usuário inexistente.';
					$this->index();
					return TRUE;
				}

				if($this->input->post('senha_') != $usuario->senha)
				{
					$this->erro = 'A senha não confere.';
					$this->index();
					return TRUE;
				}

				$this->session->set_userdata('user_id', 
					$usuario->codigo);
				redirect(base_url());
			}
			else 
			{
				$this->index();
			}
		} // function

		public function completarconta($id, $chave)
		{	
			// recupera o registro do bd
			$usuario = $this->Usuarios->get($id);

			// verifica se a chave de segurança confere
			if($chave == md5($usuario->email))
			{
				// monta o vetor de dados
				$dados = array();
				$dados['usuario'] = $usuario;
				// carrega a view
				$this->load->view('criarconta', $dados);
			}
			else 
			{
				// exibe mensagem de erro
				echo "Chave inválida.";
			}
		} // function

		public function gravarcontacompleta()
		{
			$this->_set_validation_rules('gravarcontacompleta');		## Modificado erro

			if ($this->form_validation->run())
			{
				// atualiza os dados (complementa a conta)
				$this->Usuarios->update($this->input->
					post('codigo'), $this->input->post());

				// gravar dados na sessão (session)
				$this->session->set_userdata(
					array(
						'user_id' => $this->input->post('codigo')
						)
					);

				// redirecionar para timeline
				redirect(base_url());
			}
			else {
				// recarrega o formulário para exibir os erros de validação
				$this->criarconta();
			}
		} // function

		private function _set_validation_rules($grupo)
		{
			$rules = array(
				'criarconta' => array(
					array(
						'field' => 'nome',
						'label' => 'Nome',
						'rules' => 'required|min_length[5]'
					),
					array(										## erro pode colocar sem @
						'field' => 'email',
						'label' => 'e-mail',
						'rules' => 
							'required|valid_email|
							is_unique[usuarios.email]'
					),
					array(
						'field' => 'senha',
						'label' => 'Senha',
						'rules' => 'required|min_length[6]'
					)
				),
				'gravarcontacompleta' => array(
					array(
						'field' => 'nome',
						'label' => 'Nome',
						'rules' => 'required|min_length[5]'
					),
					array(										## erro pode colocar sem @
						'field' => 'email',
						'label' => 'e-mail',
						'rules' => 
							'required|valid_email|
							is_unique[usuarios.email]'
					),
					array(
						'field' => 'login',
						'label' => 'Nome de usuário',
						'rules' => 'required|min_length[6]|callback_login_check'
					)
				),	
				'atualizarcontacompleta' => array(
					array(
						'field' => 'nome',
						'label' => 'Nome',
						'rules' => 'required|min_length[5]'
					),
					array(										## erro pode colocar sem @
						'field' => 'email',
						'label' => 'e-mail',
						'rules' => 
							'required|valid_email|
							is_unique[usuarios.email]'
					),
					array(
						'field' => 'login',
						'label' => 'Nome de usuário',
						'rules' => 'required|min_length[6]|callback_login_check'
					),	
					array(
						'field' => 'senha',
						'label' => 'Senha',
						'rules' => 'required|min_length[6]'
					)				
				),
				'autenticar' => array(
					array(
						'field' => 'login',
						'label' => 'Nome de usuário ou e-mail',
						'rules' => 'required'.
						(strpos($this->input->post('login'), '@') ? 
							'|valid_email' : '|callback_login_check')
					),
					array(
						'field' => 'senha_',
						'label' => 'Senha',
						'rules' => 'required|min_length[6]'
					)
				),
				'postartweet' => array(		//criado ##########
					array(
						'field' => 'texto',
						'label' => 'Tweet',
						'rules' => 'required|min_length[1]'
					)
				),
				'buscar' => array(		//criado ##########
					array(
						'field' => 'buscar',
						'label' => 'Buscar',
						'rules' => 'required|min_length[1]'
					)
				)
			);

			$this->form_validation->set_rules($grupo);
		} // function

		public function login_check($str)
		{
			if(preg_match('/[A-Za-z0-9]+/', $str))
			{
				return TRUE;
			}
			else 
			{
				$this->form_validation->set_message('login_check', 
					'O login pode conter apenas letras e números e não pode inlcuir espaços.');
				return FALSE;
			}
		} // function


		public function sair()
		{
			$this->session->sess_destroy();
			redirect(base_url());
		}

		public function postartweet()		//criado ##########

		{
			// regras para validação do formulário
			$this->_set_validation_rules('postartweet');

			// se o a validação do formulário foi bem sucedida
			if ($this->form_validation->run())
			{
				$dados = array();
				$dados["texto"]=$this->input->post("texto");
				$dados["codigo_usuario"]=$this->session->userdata("user_id");
				$dados["data_hora_postagem"]=date("Y-m-d h:i:s");
				// insere os dados no bd
				$id = $this->Tweets->insert($dados);

				redirect(base_url());
			}
		}

		public function seguir()
		{
			$dados["codigo_seguidor"]=$this->session->userdata("user_id");
			$dados["codigo_seguido"]=$this->input->post("codigo");
			$id = $this->Seguidores->insert($dados);

			redirect(base_url());				
		}

		public function naoseguir()
		{
			$id_seguidor = $this->session->userdata("user_id");
			$id_seguido = $this->input->post("codigo");
			$id = $this->Seguidores->delete($id_seguidor,$id_seguido);

			redirect(base_url());
		}

		public function buscar()		//criado ##########

		{ 
			
			// regras para validação do formulário
			$this->_set_validation_rules('buscar');

			// se o a validação do formulário foi bem sucedida
			if ($this->form_validation->run())
			{
				
				// procura os dados no bd

				$usuario = $this->Usuarios->get($this->session->userdata('user_id'));
				$dados = array();
				$dados['usuario']        = $usuario;
				$dados['num_seguidores'] = $this->Seguidores->countFollowers($usuario->codigo);
				$dados['num_seguindo']   = $this->Seguidores->countFollowing($usuario->codigo);
				$dados['num_tweets']     = $this->Tweets->countByUser($usuario->codigo);
				$dados['seguir']		 = FALSE;

				$resultados = $this->Usuarios->buscar($this->input->post("buscar"));

				foreach ($resultados as $resultado) {
					$resultado->num_seguidores=$this->Seguidores->countFollowers($resultado->codigo);
					$resultado->num_seguindo=$this->Seguidores->countFollowing($resultado->codigo);
					$resultado->num_tweets=$this->Tweets->countByUser($resultado->codigo);
					//$resultado->codigo_seguidor=$this->Seguidores->getBySeguido($resultado->codigo)->codigo_seguidor;
					$resultado->mostrarseguir=	FALSE;

					if (!$this->Seguidores->verificarSeguidor(
						$this->session->userdata('user_id'),
						$resultado->codigo)){
						$resultado->seguindo = FALSE;
					}
					else{
						$resultado->seguindo = TRUE;
					}


				}
				$dados["resultados"]=$resultados;
				$this->load->view("principal",$dados);

				
			}
		}

		public function atualizarconta()		//criado ##########

		{ 
				// monta o vetor de dados
				$usuario = $this->Usuarios->get($this->session->userdata('user_id'));
				$dados = array();
				$dados['usuario'] = $usuario;
				// carrega a view
				$this->load->view('atualizarconta', $dados);
	
			
		}

		public function atualizarcontacompleta()
		{
			$this->_set_validation_rules('atualizarcontacompleta');

			if ($this->form_validation->run())
			{
				// atualiza os dados (complementa a conta)
				$this->Usuarios->update($this->input->
					post('codigo'), $this->input->post());

				// gravar dados na sessão (session)
				$this->session->set_userdata(
					array(
						'user_id' => $this->input->post('codigo')
						)
					);

				// redirecionar para timeline
				redirect(base_url());
			}
			else {
				// recarrega o formulário para exibir os erros de validação
				$this->atualizarconta();
			}
		} // function

		public function quemseguir()		//criado ##########

		{ 

				// procura os dados no bd

				$usuario = $this->Usuarios->get($this->session->userdata('user_id'));
				$dados = array();
				$dados['usuario']        = $usuario;
				$dados['num_seguidores'] = $this->Seguidores->countFollowers($usuario->codigo);
				$dados['num_seguindo']   = $this->Seguidores->countFollowing($usuario->codigo);
				$dados['num_tweets']     = $this->Tweets->countByUser($usuario->codigo);


				$resultados = $this->Usuarios->buscar($this->input->post("buscar"));

				foreach ($resultados as $resultado) {

					$resultado->mostrarseguir=	TRUE;
					$resultado->texto = $this->Tweets->getByTexto($resultado->codigo);

					if (!$this->Seguidores->verificarSeguidor(
						$this->session->userdata('user_id'),
						$resultado->codigo)){
						$resultado->seguindo = FALSE;


					}
					else{
						$resultado->seguindo = TRUE;
					}


				}


				$dados["resultados"]=$resultados;
				$this->load->view("principal",$dados);

				
				
				
		}

		public function mostrartweet()		//criado ##########

		{ 

				// procura os dados no bd

				$usuario = $this->Usuarios->get($this->session->userdata('user_id'));
				$dados = array();
				$dados['usuario']        = $usuario;
				$dados['num_seguidores'] = $this->Seguidores->countFollowers($usuario->codigo);
				$dados['num_seguindo']   = $this->Seguidores->countFollowing($usuario->codigo);
				$dados['num_tweets']     = $this->Tweets->countByUser($usuario->codigo);

				


				$this->load->view("principal",$dados);

		}


	} // fim da classe

/* End of file usuario.php */