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
    * Arquivo oculto com funcionalidades ref. ao vinculo do contrato aos empenhos.
    * Data de Criação: 05/03/2008

    * @author Alexandre Melo

    * Casos de uso: uc-02.03.37

    $Id: OCManterVinculoEmpenhoContrato.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculoEmpenhoContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {

    case "incluirEmpenho":

        $rsRecordSet = new Recordset;
        $rsEmpenhos  = new Recordset;
        $arElementos = array();
        $arElementos = Sessao::read('elementos');
        $inProxId    = 0;
        $inCount     = count($arElementos);
        $boExecuta   = false;

        $arEmpenho = explode('/', $_REQUEST['numEmpenho']);
        if ($arEmpenho[0] && strlen($arEmpenho[1]) == 4) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $stFiltro .= " AND e.cod_empenho	   =  ".$arEmpenho[0];
            $stFiltro .= " AND e.exercicio  	   =  '".$arEmpenho[1]."'";
            $stFiltro .= " AND pe.cgm_beneficiario =  ".$_REQUEST['cgm_credor'];
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                if (Sessao::read('elementos') != "") {
                    //Define proximo ID
                    $rsEmpenhos->preenche(Sessao::read('elementos'));
                    $rsEmpenhos->setUltimoElemento();
                    $inUltimoId = $rsEmpenhos->getCampo("inId");
                    $inProxId = $inUltimoId + 1;
                    //Verifica a existencia do empenho na lista
                    $rsEmpenhos->setPrimeiroElemento();
                    while (!$rsEmpenhos->eof()) {
                        $cod_empenho = $rsRecordSet->getCampo('cod_empenho');
                        if ($cod_empenho == $rsEmpenhos->getCampo('cod_empenho')) {
                            $boExecuta = true;
                            $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                        }
                        $rsEmpenhos->proximo();
                    }
                }
                if (!$boExecuta) {
                    while (!$rsRecordSet->eof()) {
                        $arElementos[$inCount]['inId']				= $inProxId;
                        $arElementos[$inCount]['cod_empenho'] 		= $rsRecordSet->getCampo('cod_empenho');
                        $arElementos[$inCount]['exercicio']   		= $rsRecordSet->getCampo('exercicio');
                        $arElementos[$inCount]['dt_empenho']  		= $rsRecordSet->getCampo('dt_empenho');
                        $arElementos[$inCount]['vl_saldo_anterior'] = number_format($rsRecordSet->getCampo('vl_saldo_anterior'), 2,',','.');
                            Sessao::write('elementos', $arElementos);

                        $inCount= $inCount + 1;
                        $rsRecordSet->proximo();
                    }
                    $stJs .= listarEmpenho();
                }
            } else {
                $stJs .= "alertaAviso('Empenho inexistente para o credor selecionado! ','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "alertaAviso('Informe o código de empenho no formato: \'Número do empenho/Exercício do empenho\'.','form','erro','".Sessao::getId()."');";
        }

        $stJs .= "f.numEmpenho.value = '';";
        $stJs .= "f.numEmpenho.focus();";

        echo $stJs;
        break;

    case "excluirEmpenho":

        $arElementosSessao = Sessao::read('elementos');
        $arExcluidosSessao = Sessao::read('elementos_excluidos');

        $id = $_REQUEST['inId'];
        $inCount = 0;
        $inCountExcluidos = count(Sessao::read('elementos_excluidos'));

        foreach ($arElementosSessao AS $arElementosTMP) {
            if ($arElementosTMP["inId"] != $id) {
                $arElementos[$inCount]['inId']        = $inCount;
                $arElementos[$inCount]['cod_empenho'] = $arElementosTMP["cod_empenho"];
                $arElementos[$inCount]['exercicio']	  = $arElementosTMP["exercicio"];
                $arElementos[$inCount]['dt_empenho']  = $arElementosTMP["dt_empenho"];
                $arElementos[$inCount]['vl_saldo_anterior'] = $arElementosTMP["vl_saldo_anterior"];
                $inCount= $inCount + 1;
            } else {
                $arExcluidosSessao[$inCountExcluidos]['inId']        = $inCount;
                $arExcluidosSessao[$inCountExcluidos]['cod_empenho'] = $arElementosTMP["cod_empenho"];
                $arExcluidosSessao[$inCountExcluidos]['exercicio']	  = $arElementosTMP["exercicio"];
                $arExcluidosSessao[$inCountExcluidos]['dt_empenho']  = $arElementosTMP["dt_empenho"];
                $arExcluidosSessao[$inCountExcluidos]['vl_saldo_anterior'] = $arElementosTMP["vl_saldo_anterior"];
                $inCountExcluidos = $inCountExcluidos + 1;
            }
        }
        Sessao::write('elementos_excluidos', $arExcluidosSessao);
        Sessao::write('elementos', $arElementos);

        $stJs .= listarEmpenho();
        echo $stJs;
        break;

    case "consultaContratoEmpenho":

        $rsEmpenhos  = new Recordset;
        $arElementos = array();
        $inCount     = 0;

        Sessao::remove('elementos_excluidos');

        include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoEmpenhoContrato.class.php';
        $obTEmpenhoEmpenhoContrato = new TEmpenhoEmpenhoContrato;
        $stFiltro .= "   AND ec.exercicio    = '".Sessao::getExercicio()."'";
        $stFiltro .= "   AND ec.cod_entidade =  ".Sessao::read('inCodEntidade');
        $stFiltro .= "   AND ec.num_contrato =  ".Sessao::read('inNumContrato');
        $obTEmpenhoEmpenhoContrato->recuperaRelacionamentoEmpenhoContrato($rsEmpenhos, $stFiltro, "");

        if ($rsEmpenhos->getNumLinhas() > 0) {
            while (!$rsEmpenhos->eof()) {
                $arElementos['inId']				= $inCount;
                $arElementos['cod_empenho'] 		= $rsEmpenhos->getCampo('cod_empenho');
                $arElementos['exercicio']   		= $rsEmpenhos->getCampo('exercicio');
                $arElementos['dt_empenho']  		= $rsEmpenhos->getCampo('dt_empenho');
                $arElementos['vl_saldo_anterior']   = number_format($rsEmpenhos->getCampo('vl_saldo_anterior'), 2,',','.');
                $arTMP[] = $arElementos;

                $inCount= $inCount + 1;
                $rsEmpenhos->proximo();
            }
            Sessao::write('elementos', $arTMP);

            $stJs .= listarEmpenho();
            $stJs .= "f.numEmpenho.focus();";
        } else {
            Sessao::remove('elementos');
        }
        echo $stJs;
        break;

    case "limpar":

        $stJs .= "f.numEmpenho.value = '';";
        $stJs .= "f.numEmpenho.focus();";

        echo $stJs;
        break;
}

function listarEmpenho()
{
    $rsRecordSet = new RecordSet;

    if (Sessao::read('elementos') != "") {
        $rsRecordSet->preenche(Sessao::read('elementos'));
    }

    if ($rsRecordSet->getNumLinhas() > 0) {

        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Empenhos do Contrato" );

        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Empenho" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Emissão" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Valor" );
        $obLista->ultimoCabecalho->setWidth( 20 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
        $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[dt_empenho]" );
        $obLista->ultimoDado->setAlinhamento('CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[vl_saldo_anterior]" );
        $obLista->ultimoDado->setAlinhamento('DIREITA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "Excluir" );
        $obLista->ultimaAcao->setFuncaoAjax( true );

        $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirEmpenho');" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace( "\n" ,"" ,$stHtml );
        $stHtml = str_replace( chr(13) ,"<br>" ,$stHtml );
        $stHtml = str_replace( "  " ,"" ,$stHtml );
        $stHtml = str_replace( "'","\\'",$stHtml );
        $stHtml = str_replace( "\\\'","\\'",$stHtml );

    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnListaEmpenhos').innerHTML = '".$stHtml."';";

    return $stJs;
}

?>
