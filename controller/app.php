<?php
  //classe dashboard
  class Dashboard{

    public $data_inicio;
    public $data_fim;
    public $numeroVendas;
    public $totalVendas;

    public $clienteAtivo;
    public $clienteInativo;

    public $reclamacao;
    public $elogios;
    public $sugestao;

    public $totalDespesas;

    public function __get($atributo) {
      return $this->$atributo;
    }

    public function __set($atributo, $valor) {
      $this->$atributo = $valor;
      return $this;
    }
  }

  //classe conexão
  class Conexao{

    private $host = 'localhost';
    private $dbname = 'dashboard';
    private $username = 'root';
    private $password = '';

    public function conectar(){
      try{
        $conexao = new PDO(
          "mysql: host=$this->host;dbname=$this->dbname",
          "$this->username",
          "$this->password"
        );

        $conexao->exec('set charset utf8');

        return $conexao;

      } catch (PDOException $e){
        echo '<p>Error: '. $e->getMessage . '</p>';
      }
    }
  }

  //class (model)
  class Bd{
    private $conexao;
    private $dashboard;

    public function __construct(Conexao $conexao, Dashboard $dashboard){
      $this->conexao = $conexao->conectar();
      $this->dashboard = $dashboard;
    }
/* ********************************* RECUPERAR ******************************* */
    //metodo que recupera informações
    public function getNumeroVendas() {
      $query = '
        select
          count(*) as numero_vendas
        from 
          tb_vendas 
        where data_venda between :data_inicio and :data_fim';

      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
    }

    public function getTotalVendas() {
      $query = '
        select
          sum(total) as total_vendas
        from 
          tb_vendas 
        where data_venda between :data_inicio and :data_fim';

      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
    }

    public function getClienteAtivo() {
      $query = '
        select 
          sum(cliente_ativo) as total_ativo
        from 
          tb_clientes 
        where cliente_ativo = true';

      $stmt = $this->conexao->prepare($query);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->total_ativo;
    }

    public function getClienteInativo() {
      $query = '
        select 
          sum(cliente_ativo) as total_ativo
        from 
          tb_clientes 
        where cliente_ativo = false';

      $stmt = $this->conexao->prepare($query);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_OBJ)->total_ativo;
    }

    public function getReclamacao() {
      $query = '
        select 
          sum(tipo_contato) as total_critica
        from 
          tb_contatos 
        where tipo_contato = 1';

      $stmt = $this->conexao->prepare($query);
      $stmt->execute();
      //select sum(tipo_contato) as total_critica from tb_contatos where tipo_contato = 1
      return $stmt->fetch(PDO::FETCH_OBJ)->total_critica;
    }

    public function getElogios() {
      $query = 'select *,count(*) as total_elogio from tb_contatos where tipo_contato like 3';
      
      $stmt = $this->conexao->prepare($query);
      $stmt->execute();
      //'select *,count(*) as total_elogio from tb_contatos where tipo_contato like 3';
      return $stmt->fetch(PDO::FETCH_OBJ)->total_elogio;
    }

    public function getSugestao() {
      $query = 'select *, count(*) as total_sugestao from tb_contatos where tipo_contato like 2';
      
      $stmt = $this->conexao->prepare($query);
      $stmt->execute();
      //'select *, count(*) as total_sugestao from tb_contatos where tipo_contato like 2';
      return $stmt->fetch(PDO::FETCH_OBJ)->total_sugestao;
    }

    public function getTotaldespesas() {
      $query = 'select sum(total) as total_despesas from tb_despesas where data_despesa between :data_inicio and :data_fim';
      
      $stmt = $this->conexao->prepare($query);
      $stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
      $stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
      $stmt->execute();
      //select sum(total) as total_despesas from tb_despesas;
      return $stmt->fetch(PDO::FETCH_OBJ)-> total_despesas;
    }
  }

  //instanciar (A instanciação é um processo por meio do qual se realiza a cópia de um objeto (classe) existente.)
  //lógica do script
  $dashboard = new Dashboard();
  
  $conexao = new Conexao();

  $competencia = explode('-',$_GET['competencia']);
  $ano = $competencia[0];
  $mes = $competencia[1];

  $dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

  $dashboard->__set('data_inicio', $ano. '-' .$mes . '-01');
  $dashboard->__set('data_fim', $ano. '-' .$mes . '-' .$dias_do_mes);

  $bd = new Bd($conexao, $dashboard);
  $dashboard->__set('numeroVendas',$bd->getNumeroVendas());
  $dashboard->__set('totalVendas',$bd->getTotalVendas());
  $dashboard->__set('clienteAtivo',$bd->getClienteAtivo());
  $dashboard->__set('clienteInativo',$bd->getClienteInativo());
  $dashboard->__set('reclamacao', $bd->getReclamacao());
  $dashboard->__set('elogios', $bd->getElogios());
  $dashboard->__set('sugestao', $bd->getSugestao());
  $dashboard->__set('totalDespesas', $bd->getTotalDespesas());

  //print_r($dashboard);
  echo json_encode($dashboard);

?>