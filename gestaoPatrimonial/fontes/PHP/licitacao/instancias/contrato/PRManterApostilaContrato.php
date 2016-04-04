<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/*
    * Processamento de Apostila de Contrato
    * Data de Criação   : 25/02/2016
    
    * @author Analista:      Gelson W. Gonçalves  <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Carlos Adriano       <carlos.silva@cnm.org.br>
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: PRManterApostilaContrato.php 64464 2016-02-26 14:04:45Z carlos.silva $
*/
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php'        );
include_once ( '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php'  );
include_once ( TLIC.'TLicitacaoContrato.class.php'              );
include_once ( TLIC.'TLicitacaoContratoApostila.class.php'      );

$stPrograma = "ManterApostilaContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";


switch( $_REQUEST['stAcao'] ){
	case "incluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;

		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;
		$stFiltro  = " WHERE num_contrato  =  ".$_REQUEST['inNumContrato'];
		$stFiltro .= "   AND exercicio	   = '".$_REQUEST['stExercicioContrato']."'";
		$stFiltro .= "   AND cod_entidade  =  ".$_REQUEST['inCodEntidadeContrato'];
		$stFiltro .= "   AND cod_apostila  =  ".$_REQUEST['inCodApostila'];
		$obTLicitacaoContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);

		if( $rsRecordSet->getNumLinhas() > 0 )
			$obErro->setDescricao('Número da Apostila já existe para o Contrato '.$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato'].'.');
		
		if( !$obErro->ocorreu() ){
            $obTLicitacaoContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']           	  );
			$obTLicitacaoContratoApostila->setDado( 'num_contrato'  , $_REQUEST['inNumContrato']		      );
			$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	  );
            $obTLicitacaoContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		  );
			$obTLicitacaoContratoApostila->setDado( 'cod_tipo'      , $_REQUEST['inCodTipoApostila']          );
			$obTLicitacaoContratoApostila->setDado( 'cod_alteracao' , $_REQUEST['inCodTipoAlteracaoApostila'] );
			$obTLicitacaoContratoApostila->setDado( 'descricao'     , $_REQUEST['stDscApostila']     		  );
			$obTLicitacaoContratoApostila->setDado( 'data_apostila' , $_REQUEST['dtApostila']      		      );
			
			$nuVlApostila=(isset($_REQUEST['nuVlApostila'])) ? $_REQUEST['nuVlApostila'] : 0;
			$obTLicitacaoContratoApostila->setDado( 'valor_apostila', $nuVlApostila );
			
			$obErro = $obTLicitacaoContratoApostila->inclusao();
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"incluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break;

	case "alterar":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;

		if($_REQUEST['inCodApostilaAtual']!=$_REQUEST['inCodApostila']){
			$stFiltro  = " WHERE ( num_contrato  =  ".$_REQUEST['inNumContrato'];
			$stFiltro .= "   AND exercicio	   = '".$_REQUEST['stExercicioContrato']."'";
			$stFiltro .= "   AND cod_entidade  =  ".$_REQUEST['inCodEntidadeContrato'];
			$stFiltro .= "   AND cod_apostila  =  ".$_REQUEST['inCodApostila']." )";
            $stFiltro .= "   AND cod_apostila  !=  ".$_REQUEST['inHdnCodApostila'] ;
			$obTLicitacaoContratoApostila->recuperaTodos($rsRecordSet, $stFiltro);
	
			if( $rsRecordSet->getNumLinhas() > 0 )
				$obErro->setDescricao('Número da Apostila já existe para o Contrato '.$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato'].'.');
		}
		
		if( !$obErro->ocorreu() ){
			$obTLicitacaoContratoApostila->setDado( 'num_contrato'  , $_REQUEST['inNumContrato']		 );
			$obTLicitacaoContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']	 );
			$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato'] );
			$obTLicitacaoContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inHdnCodApostila']      );
			
			$obErro = $obTLicitacaoContratoApostila->exclusao();
			
			if( !$obErro->ocorreu() ){
                $obTLicitacaoContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']           	  );
                $obTLicitacaoContratoApostila->setDado( 'num_contrato'  , $_REQUEST['inNumContrato']		      );
                $obTLicitacaoContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato']	  );
                $obTLicitacaoContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']		  );
                $obTLicitacaoContratoApostila->setDado( 'cod_tipo'      , $_REQUEST['inCodTipoApostila']          );
                $obTLicitacaoContratoApostila->setDado( 'cod_alteracao' , $_REQUEST['inCodTipoAlteracaoApostila'] );
                $obTLicitacaoContratoApostila->setDado( 'descricao'     , $_REQUEST['stDscApostila']     		  );
                $obTLicitacaoContratoApostila->setDado( 'data_apostila' , $_REQUEST['dtApostila']      		      );
				
				$nuVlApostila=(isset($_REQUEST['nuVlApostila'])) ? $_REQUEST['nuVlApostila'] : 0;
				$obTLicitacaoContratoApostila->setDado( 'valor_apostila', $nuVlApostila );
				
				$obErro = $obTLicitacaoContratoApostila->inclusao();
			}
		}

		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgList."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"alterar","aviso", Sessao::getId(), "../");
		}
		
		Sessao::encerraExcecao();
		
	break;
	
	case "excluir":
		Sessao::setTrataExcecao ( true );
		
		$obErro = new Erro;
		
		$obTLicitacaoContratoApostila = new TLicitacaoContratoApostila ;
		$obTLicitacaoContratoApostila->setDado( 'cod_contrato'  , $_REQUEST['inCodContrato']		 );
		$obTLicitacaoContratoApostila->setDado( 'exercicio'     , $_REQUEST['stExercicioContrato']	 );
		$obTLicitacaoContratoApostila->setDado( 'cod_entidade'  , $_REQUEST['inCodEntidadeContrato'] );
		$obTLicitacaoContratoApostila->setDado( 'cod_apostila'  , $_REQUEST['inCodApostila']         );
		
		$obErro = $obTLicitacaoContratoApostila->exclusao();
		
		if( $obErro->ocorreu() ){
			SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
		}else{
			sistemaLegado::alertaAviso($pgFilt."?stAcao=".$_REQUEST['stAcao'], "Contrato:".$_REQUEST['inNumContrato'  ] .'/'. $_REQUEST['stExercicioContrato']." - Apostila:".$_REQUEST['inCodApostila'] ,"excluir","aviso", Sessao::getId(), "../");
		}
		Sessao::encerraExcecao();
	
	break; 
}
?>