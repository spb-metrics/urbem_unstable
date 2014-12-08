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
/**
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoContrato.class.php" );
include_once( TLIC."TLicitacaoContratoAditivos.class.php" );
include_once( TLIC."TLicitacaoContratoAditivosAnulacao.class.php" );
include_once( TLIC."TLicitacaoPublicacaoContratoAditivos.class.php" );

Sessao::getExercicio();
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$dadosFiltro = Sessao::read('dadosFiltro',$param);
foreach ($dadosFiltro as $chave =>$valor) {
    $stFiltro.= "&".$chave."=".$valor;
}

$stPrograma = "ManterAditivoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arValores = Sessao::read('arValores');

// método para setar os dados do objeto passado por parâmetro.
function setaDados(&$obTLicitacaoContratoAditivo, $stAcao)
{
    $obTLicitacaoContratoAditivo->setDado('exercicio_contrato', $_REQUEST['stExercicioContrato']);
    $obTLicitacaoContratoAditivo->setDado('num_contrato', $_REQUEST['inNumContrato']);
    $obTLicitacaoContratoAditivo->setDado('cod_entidade', $_REQUEST['inCodEntidade']);

    if ($stAcao == "incluirCD") {
        $obTLicitacaoContratoAditivo->setDado('exercicio', Sessao::getExercicio());
        $obTLicitacaoContratoAditivo->proximoCod($inCodNumAditivo);
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $inCodNumAditivo);

    } else {
        $obTLicitacaoContratoAditivo->setDado('exercicio', $_REQUEST['stExercicioAditivo']);
        $obTLicitacaoContratoAditivo->setDado('num_aditivo', $_REQUEST['inNumeroAditivo']);
    }

    $obTLicitacaoContratoAditivo->setDado('responsavel_juridico', $_REQUEST['inCodResponsavelJuridico']);
    $obTLicitacaoContratoAditivo->setDado('dt_vencimento', $_REQUEST['dtFinalVigencia']);
    $obTLicitacaoContratoAditivo->setDado('dt_assinatura', $_REQUEST['dtAssinatura']);
    $obTLicitacaoContratoAditivo->setDado('inicio_execucao', $_REQUEST['dtInicioExcucao']);
    $vlValorContratado = number_format(str_replace(".", "", $_REQUEST['vlValorContratado']), 2, ".", "");
    $obTLicitacaoContratoAditivo->setDado('valor_contratado', $vlValorContratado);
    $obTLicitacaoContratoAditivo->setDado('objeto', $_REQUEST['stObjeto']);
    $obTLicitacaoContratoAditivo->setDado('fundamentacao', $_REQUEST['stFundamentacaoLegal']);
}

Sessao::setTrataExcecao( true );
$stMensagem = "";

// validação dos dados caso ação seje diferente de 'anular'
if ($stAcao != "anularCD") {

    $obTLicitacaoContrato = new TLicitacaoContrato();
    $obTLicitacaoContrato->setDado('exercicio_contrato', $_REQUEST['stExercicio']);
    $obTLicitacaoContrato->setDado('num_contrato', $_REQUEST['inNumContrato']);
    $obTLicitacaoContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTLicitacaoContrato->recuperaPorChave( $rsLicitacaoContrato );

    if ( implode(array_reverse(explode('/',$_REQUEST['dtAssinatura']))) < implode(array_reverse(explode('/',$rsLicitacaoContrato->getCampo("dt_assinatura"))))) {
        $stMensagem = "A data de assinatura do aditivo não pode ser anterior que a data de assinatura do contrato.";
    }

    if ( implode(array_reverse(explode('/',$_REQUEST['dtInicioExcucao']))) < implode(array_reverse(explode('/',$_REQUEST['dtAssinatura'])))) {
        $stMensagem = "A data de início de execução não pode ser anterior que a data de assinatura do aditivo.";
    }

    if ( implode(array_reverse(explode('/',$_REQUEST['dtFinalVigencia']))) < implode(array_reverse(explode('/',$_REQUEST['dtInicioExcucao'])))) {
        $stMensagem = "A data de final de vigência não pode ser anterior que a data de início de execução.";
    }
}

if ($stMensagem != "") {
    SistemaLegado::exibeAviso(urlencode($stMensagem), "n_incluir", "erro" );
} else {

    $obTLicitacaoPublicacaoContratoAditivos = new TLicitacaoPublicacaoContratoAditivos;

    switch ($stAcao) {

        case "incluirCD":
            $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivos();
            setaDados($obTLicitacaoContratoAditivo, $stAcao);
            $obTLicitacaoContratoAditivo->inclusao();

            //inclui os dados da publicacao do contrato
            foreach ($arValores as $arTemp) {
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato'       , $obTLicitacaoContratoAditivo->getDado('num_contrato') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'        , $obTLicitacaoContratoAditivo->getDado('num_aditivo') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'numcgm'             , $arTemp['inVeiculo'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'dt_publicacao'      , $arTemp['dtDataPublicacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_publicacao'     , $arTemp['inNumPublicacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'          , Sessao::getExercicio() );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio_contrato' , $obTLicitacaoContratoAditivo->getDado('exercicio_contrato') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'observacao'         , $arTemp['stObservacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->inclusao();
            }

            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro,"Contrato: ".$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'],"incluir", "aviso", Sessao::getId(),"");
            break;

        case "alterarCD":
            $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivos();
            setaDados($obTLicitacaoContratoAditivo, $stAcao);
            $obTLicitacaoContratoAditivo->alteracao();

            //exclui os veiculos de publicidade existentes
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato' , $_REQUEST['inNumContrato']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'  , $_REQUEST['inNumAditivo']);
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'    , Sessao::getExercicio());
            $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade']);
            $obTLicitacaoPublicacaoContratoAditivos->exclusao();

            //inclui os veiculos que estao na sessao
            foreach ($arValores as $arTemp) {
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_contrato'       , $obTLicitacaoContratoAditivo->getDado('num_contrato') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_aditivo'        , $obTLicitacaoContratoAditivo->getDado('num_aditivo') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'numcgm'             , $arTemp['inVeiculo'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'dt_publicacao'      , $arTemp['dtDataPublicacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'num_publicacao'     , $arTemp['inNumPublicacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio'          , Sessao::getExercicio() );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'exercicio_contrato' , $obTLicitacaoContratoAditivo->getDado('exercicio_contrato') );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'cod_entidade'       , $_REQUEST['inCodEntidade'] );
                $obTLicitacaoPublicacaoContratoAditivos->setDado( 'observacao'         , $arTemp['stObservacao'] );
                $obTLicitacaoPublicacaoContratoAditivos->inclusao();
            }

            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro,"Contrato: ".$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'],"alterar", "aviso", Sessao::getId(),"");
            break;

        case "anularCD":
            $obTLicitacaoContratoAditivo = new TLicitacaoContratoAditivosAnulacao();
            $obTLicitacaoContratoAditivo->setDado("exercicio_contrato", $_REQUEST["stExercicioContrato"]);
            $obTLicitacaoContratoAditivo->setDado("cod_entidade", $_REQUEST["inCodEntidade"]);
            $obTLicitacaoContratoAditivo->setDado("num_contrato", $_REQUEST["inNumContrato"]);
            $obTLicitacaoContratoAditivo->setDado("exercicio", $_REQUEST["stExercicioAditivo"]);
            $obTLicitacaoContratoAditivo->setDado("num_aditivo", $_REQUEST["inNumeroAditivo"]);
            $obTLicitacaoContratoAditivo->setDado("dt_anulacao", $_REQUEST["dtAnulacao"]);
            $obTLicitacaoContratoAditivo->setDado("motivo", $_REQUEST["stMotivoAnulacao"]);
            $obTLicitacaoContratoAditivo->inclusao();
            SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro,"Contrato: ".$_REQUEST['inNumContrato']."/".$_REQUEST['stExercicioContrato'],"anular", "aviso", Sessao::getId(),"");
            break;
    }
}

Sessao::encerraExcecao();
