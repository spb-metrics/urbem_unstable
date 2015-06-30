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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';	
include_once ( CAM_GP_PAT_MAPEAMENTO."FPatrimonioDepreciacaoAutomatica.class.php"     );
include_once ( CAM_GP_PAT_MAPEAMENTO."FPatrimonioReavaliacaoDepreciacaoAutomatica.class.php" );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamentoDepreciacao.class.php" );
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacao.class.php"               );
include_once ( CAM_GP_PAT_MAPEAMENTO."TPatrimonioDepreciacaoAnulada.class.php"        );

$stProg = 'DepreciacaoAutomatica';
$pgFilt = 'FL'.$stProg.'.php';

SistemaLegado::BloqueiaFrames(true,true);

$boTransacao = new Transacao;
$obErro      = new Erro;
$obTContabilidadeLancamentoDepreciacao  = new TContabilidadeLancamentoDepreciacao();
$obTPatrimonioDepreciacao               = new TPatrimonioDepreciacao();
$obTPatrimonioDepreciacaoAnulada        = new TPatrimonioDepreciacaoAnulada();

$stAcao                   = $request->get("stAcao");
$inMesCompetenciaFiltro   = $request->get("inExercicio").str_pad($_REQUEST['inCompetencia'],2,'0',STR_PAD_LEFT);
$inMesCompetenciaMensagem = str_pad($_REQUEST['inCompetencia'],2,'0',STR_PAD_LEFT)."/".$request->get("inExercicio");
$inCodEntidade            = SistemaLegado::pegaDado("valor","administracao.configuracao","WHERE cod_modulo = 8 AND parametro ilike 'cod_entidade_prefeitura' AND exercicio = '".Sessao::getExercicio()."';");

switch ($stAcao) {
    case 'depreciar':

        if ($request->get("boAnulacao") == "true") {

            $stFiltroDepreciacao = "\n WHERE competencia = '".$inMesCompetenciaFiltro."'";
            $obErro = $obTPatrimonioDepreciacao->recuperaTodos($rsPatrimonioDepreciacao, $stFiltroDepreciacao, " ORDER BY cod_depreciacao DESC ");
	    
            $obErro = $obTPatrimonioDepreciacao->recuperaMaxCompetenciaDepreciada($rsMaxCompetenciaDepreciada);
            $obErro = $obTPatrimonioDepreciacao->recuperaMaxCodDepreciacao($rsMaxDepreciacao, $stFiltroDepreciacao);
            $obErro = $obTPatrimonioDepreciacaoAnulada->recuperaMaxCodDepreciacaoAnulada($rsMaxAnulada, $stFiltroDepreciacao);
            
            $stFiltroContabilidade = " WHERE lancamento_depreciacao.timestamp = ( SELECT MAX(lancamento_depreciacao.timestamp) AS timestamp 
                                                                                    FROM contabilidade.lancamento_depreciacao
                                                                                   WHERE competencia  = '".$inMesCompetenciaFiltro."'
                                                                                     AND cod_entidade = ".$inCodEntidade."
                                                                                     AND exercicio    = '".$request->get("inExercicio")."'
						                                )
                                         AND lancamento_depreciacao.exercicio = '".$request->get("inExercicio")."'";
            $obErro = $obTContabilidadeLancamentoDepreciacao->verificaDepreciacoesAnteriores($rsLancamentosAnteriores, $stFiltroContabilidade, $stOrdem, $boTransacao);
            
            if (!$obErro->ocorreu()) {
            
                // Verifica se há bens depreciados na competencia selecionada.
                if ($rsPatrimonioDepreciacao->getNumLinhas() <= 0) {
                    $obErro->setDescricao("Não existem bens depreciados na competência ".$inMesCompetenciaMensagem);
                
                // Verifica se existe algum lançamento em contabilidade.lancamento_depreciacao, caso exista não pode efetuar a anulação, e deve estornar os bens da competencia primeiro.
                } elseif ($rsLancamentosAnteriores->getNumLinhas() >= 1 && $rsLancamentosAnteriores->getCampo('estorno') == "f") {
                    $obErro->setDescricao("Competência ".$inMesCompetenciaMensagem." com lançamento contábil. Efetue estorno dos lançamentos antes da anulação!");
                    
                // Caso já tenha anulado, e seja da mesma depreciacao, não permite anular. Necessário depreciar novamente.
                } elseif ($rsMaxAnulada->getNumLinhas() >= 1 && $rsMaxAnulada->getCampo('max_cod_depreciacao_anulada') == $rsMaxDepreciacao->getCampo('max_cod_depreciacao')) {
                    $obErro->setDescricao("Competência ".$inMesCompetenciaMensagem." já anulada!");
                
                // Verifica se o usuário quer anular uma depreciação menor do que a atual. Deve anular em sequencia dos mes maior ao menor.
                } elseif ($rsMaxCompetenciaDepreciada->getCampo('max_competencia') != "" && $inMesCompetenciaFiltro < $rsMaxCompetenciaDepreciada->getCampo('max_competencia')) {
                    $obErro->setDescricao("Deve anular última competência depreciada - ".$rsMaxCompetenciaDepreciada->getCampo('max_competencia_formatada'));
                    
                } else {            
		    //SistemaLegado::BloqueiaFrames(true,true);
		    $obTPatrimonioDepreciacaoAnulada = new TPatrimonioDepreciacaoAnulada();
                    $stParametros  = "'".$inMesCompetenciaFiltro."' ,";
		    $stParametros .= '\''.($_REQUEST['stMotivo'] ? $_REQUEST['stMotivo'] : '').'\'';
	    
		    $obErro = $obTPatrimonioDepreciacaoAnulada->executaFuncao($stParametros, $boTransacao);
		    
                }
                
                if (!$obErro->ocorreu()) {
		    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"Bens anulados com sucesso até a competência: ".str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT).'/'.$_REQUEST['inExercicio'],"$stAcao","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
		
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        
        }else{
	    
            $obErro = $obTPatrimonioDepreciacao->recuperaMaxCompetenciaDepreciada($rsMaxCompetenciaDepreciada);
	    
            $stFiltroDepreciacao = "\n WHERE competencia = '".$inMesCompetenciaFiltro."'";
            $obErro = $obTPatrimonioDepreciacao->recuperaMaxCodDepreciacao($rsMaxDepreciacao, $stFiltroDepreciacao );
            $obErro = $obTPatrimonioDepreciacaoAnulada->recuperaMaxCodDepreciacaoAnulada($rsMaxAnulada, $stFiltroDepreciacao );

            $stFiltroAnterior = "\n WHERE competencia = '".($request->get("inExercicio").str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT) - 1)."'";
            $obErro = $obTPatrimonioDepreciacao->recuperaMaxCodDepreciacao($rsMaxDepreciacaoAnterior, $stFiltroAnterior);
            $obErro = $obTPatrimonioDepreciacaoAnulada->recuperaMaxCodDepreciacaoAnulada($rsMaxAnuladaAnterior, $stFiltroAnterior);
            $obErro = $obTPatrimonioDepreciacaoAnulada->recuperaMaxCompetenciaAnulada($rsMxCompetenciaAnterior, $stFiltroAnterior);
            
	    $stProximaCompetencia = ($rsMaxCompetenciaDepreciada->getCampo('max_competencia') != $request->get("inExercicio")."12") ? substr(($rsMaxCompetenciaDepreciada->getCampo('max_competencia') + 1), 4, 6)."/".substr($rsMaxCompetenciaDepreciada->getCampo('max_competencia'),0,4) : substr($rsMaxCompetenciaDepreciada->getCampo('max_competencia'), 4, 6)."/".substr($rsMaxCompetenciaDepreciada->getCampo('max_competencia'),0,4);
	    
            if (!$obErro->ocorreu()) {
            
                // Verifica se a competência anterior possui anulação, não pode depreciar a atual sem antes depreciar novamente a anterior.
                if ($rsMaxAnuladaAnterior->getCampo('max_cod_depreciacao_anulada') != "" && $rsMaxDepreciacaoAnterior->getCampo('max_cod_depreciacao') == $rsMaxAnuladaAnterior->getCampo('max_cod_depreciacao_anulada') ) {
                    $obErro->setDescricao("Competência ".$rsMxCompetenciaAnterior->getCampo('max_competencia_formatada')." com anulação. Deprecie antes de continuar! ");
                    
                // Caso existam bens já depreciados na competencia, mas não tenham sido anulados, não deixa depreciar novamente até que sejam anulados.
                } elseif ($rsMaxAnulada->getCampo('max_cod_depreciacao_anulada') != $rsMaxDepreciacao->getCampo('max_cod_depreciacao')) {
                    $obErro->setDescricao("Já existem bens depreciados para a competência ".$inMesCompetenciaMensagem);
                
                // Quando existir ao menos uma depreciação, A competencia selecionada nao pode ser menor que a última depreciada.
                } elseif ( $rsMaxCompetenciaDepreciada->getCampo('max_competencia') != "" && $inMesCompetenciaFiltro < $rsMaxCompetenciaDepreciada->getCampo('max_competencia') ) {                                        
                    $obErro->setDescricao("A competência selecionada não pode ser menor que ".$stProximaCompetencia);
                
                // Quando existir ao menos uma depreciação, A competencia selecionada nao pode ser maior que a última depreciada.
                } elseif ( $rsMaxCompetenciaDepreciada->getCampo('max_competencia') != "" && $inMesCompetenciaFiltro > ($rsMaxCompetenciaDepreciada->getCampo('max_competencia') + 1) ) {
                    $obErro->setDescricao("A competência selecionada não pode ser maior que ".$stProximaCompetencia);
                
                // Não pode ser maior que a competência logada do sistema.
                } elseif (((int) $request->get("inExercicio") == date('Y') && (int) $request->get("inCompetencia") > date('m')) || ((int) $request->get("inExercicio") != date('Y'))) {
		    $obErro->setDescricao("A competência selecionada não pode ser maior que a atual do sistema!");
    
                } else {
		    $obFPAtrimonioDepreciacaoAutomatica = new FPatrimonioDepreciacaoAutomatica;
		    $obFPAtrimonioReavaliacaoDepreciacaoAutomatica = new FPatrimonioReavaliacaoDepreciacaoAutomatica;
		    
                    $stParametros  = '\''.$request->get("inExercicio").'\',';
                    $stParametros .= '\''.str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT).'\',';
                    $stParametros .= 'null,null,null,';
                    $stParametros .= '\''.($request->get("stMotivo") ? $request->get("stMotivo") : 'Depreciação Automática').'\'';
    
                    // Verifica quais bens comprados antes do exercicio corrente, precisam de reavaliação para serem depreciados
		    $obErro = $obFPAtrimonioReavaliacaoDepreciacaoAutomatica->recuperaReavaliacao($rsReavaliacao, $stParametros, $boTransacao);
		    
		    if ($rsReavaliacao->getNumLinhas() > 0 ){
			SistemaLegado::LiberaFrames(true,true);
			SistemaLegado::exibeAviso(urlencode("Existem bens a serem reavaliados até a competência ".str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT).'/'.$_REQUEST['inExercicio']),"n_incluir","erro");
			
			$preview = new PreviewBirt(3,6,24);
			$preview->setVersaoBirt( '2.5.0' );
					    
			$preview->setTitulo('Log de Reavaliação de Depreciação');
			$preview->setNomeArquivo('log_depreciacao_'.sistemaLegado::mesExtensoBR($request->get("inCompetencia"))."_".$request->get("inExercicio"));
			
			$preview->addParametro( 'exercicio'   , Sessao::getExercicio() );
			$preview->addParametro( 'stExercicio' , Sessao::getExercicio() );
			$preview->addParametro( 'stMes'       , str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT) );
			$preview->addParametro( 'stMesExtenso', sistemaLegado::mesExtensoBR($request->get("inCompetencia")) );
			$preview->addParametro( 'stMotivo'    , "Reavaliação de Depreciação" );
			$preview->addParametro( 'stCabecalho' , "Log de Depreciação ".sistemaLegado::mesExtensoBR($request->get("inCompetencia"))." de ".$request->get("inExercicio") );
		    
			$preview->preview();
		       
			//Parar o processamento e carregar o relatório
			die;
		    } else {
			$obErro = $obFPAtrimonioDepreciacaoAutomatica->executaFuncao($rsDepreciacao, $stParametros, $boTransacao);
		    }

                }

                if (!$obErro->ocorreu()) {
		    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"Bens depreciados com sucesso até a competência: ".str_pad($request->get("inCompetencia"),2,'0',STR_PAD_LEFT).'/'.$_REQUEST['inExercicio'],"$stAcao","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                }
                
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
            
        }
        
    break;
}

SistemaLegado::mudaFramePrincipal($pgFilt);
SistemaLegado::LiberaFrames(true,true);

?>