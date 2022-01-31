<?php  

	class Dashboard{
		public $data_inicio;
		public $data_fim;
		public $numeroVendas;
		public $totalVendas;
		public $clientesAtivo;
		public $clientesInativo;
		public $totalReclamaçao; //2
		public $totalElogio; //1
		public $totalSugestao; //3
		public $totalDespesa;

		public function __get($atr){
			return $this->$atr;
		} 
		public function __set($atr, $valor){
			$this->$atr = $valor;
			return $this;
		}
	}
	class Conexao{
		private $host = 'localhost';
		private $dbname = 'dashboard';
		private $user = 'root';
		private $pass = '';

		public function conectar(){
			try{
				$conexao = new PDO(
					"mysql:host=$this->host;dbname=$this->dbname;","$this->user","$this->pass"
				);
				$conexao->exec('set charset utf8');

				return $conexao;
			}catch(PDOException $e){
				echo '<p>'. $e->getMessage(). '</p>';
			}
		}
	}

	class Bd{
		private $conexao;
		private $dashboard;

		public function __construct(Conexao $conexao, Dashboard $dashboard){
			$this->conexao = $conexao->conectar();
			$this->dashboard = $dashboard;
		}
		public function getNumeroVendas(){
			$query = '
				select count(*) 
					as numero_vendas 
					from tb_vendas where 
					data_venda between :data_inicio and :data_fim
			';
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue('data_inicio', $this->dashboard->__get('data_inicio'));
			$stmt->bindValue('data_fim', $this->dashboard->__get('data_fim'));
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
		}
		public function getTotalVendas(){
			$query = '
				select SUM(total) 
					as total_vendas 
					from tb_vendas where 
					data_venda between :data_inicio and :data_fim
			';
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue('data_inicio', $this->dashboard->__get('data_inicio'));
			$stmt->bindValue('data_fim', $this->dashboard->__get('data_fim'));
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
		}
		public function getClientesAtivo(){
			$query = "
				select count(cliente_ativo) as total_ativo from tb_clientes where cliente_ativo = :numero;
			";
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':numero', 1);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_ativo;
		}
		public function getClientesInativo(){
			$query = "
				select count(*) as total_inativo from tb_clientes where cliente_ativo = :numero;
			";
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':numero', 0);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_inativo;
		}
		public function getReclamacao(){
			$query = "
				select count(*) as total_reclamacao from tb_contatos where tipo_contato = :numero;
			";
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':numero', 2);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_reclamacao;
		}
		public function getElogio(){
			$query ="
				select count(*) as total_elogio from tb_contatos where tipo_contato = :numero;
			";
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':numero', 1);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_elogio;
		}
		public function getSugestao(){
			$query="
				select count(*) as total_sugestao from tb_contatos where tipo_contato = :numero;
			";
			$stmt=$this->conexao->prepare($query);
			$stmt->bindValue(':numero',3);
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestao;
		}
		public function getDespesa(){
			$query="
				select sum(total) as total_despesa from tb_despesas where data_despesa between :data_inicio and :data_fim 
			";
			$stmt = $this->conexao->prepare($query);
			$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
			$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
			$stmt->execute();
			return $stmt->fetch(PDO::FETCH_OBJ)->total_despesa;
		}
	}
	$dashboard = new Dashboard();
	$conexao = new Conexao();
	$competencia = explode('-',$_GET['competencia']);
	$ano = $competencia[0];
	$mes = $competencia[1];
	$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);
	$dashboard->__set('data_fim',$ano.'-'.$mes.'-'.$dias_do_mes);
	$dashboard->__set('data_inicio',$ano.'-'.$mes.'-'.'01');
	$bd = new Bd($conexao, $dashboard);
	$dashboard->__set('numeroVendas',$bd->getNumeroVendas());
	$dashboard->__set('totalVendas',$bd->getTotalVendas());
	$dashboard->__set('clientesAtivo', $bd->getClientesAtivo());
	$dashboard->__set('clientesInativo', $bd->getClientesInativo());
	$dashboard->__set('totalReclamaçao', $bd->getReclamacao());
	$dashboard->__set('totalElogio', $bd->getElogio());
	$dashboard->__set('totalSugestao', $bd->getSugestao());
	$dashboard->__set('totalDespesa', $bd->getDespesa());
	echo json_encode($dashboard);

?>