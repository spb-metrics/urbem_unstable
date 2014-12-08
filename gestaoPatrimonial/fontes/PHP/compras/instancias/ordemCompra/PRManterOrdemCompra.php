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
    * Página de Processamento de Ordem de Compra
    * Data de Criação   : 06/12/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Id: PRManterOrdemCompra.php 59612 2014-09-02 12:00:51Z gelson $
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once(TCOM."TComprasOrdem.class.php");
require_once(TCOM."TComprasOrdemItem.class.php");
require_once(TCOM."TComprasOrdemAnulacao.class.php");
require_once(TCOM."TComprasOrdemItemAnulacao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrdemCompra";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";
$pgRel     = "OCGera".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTOrdemCompra );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stTipoOrdem = ( strpos($stAcao,'OS')===false ) ? 'C' : 'S';
$stDesc = ($stTipoOrdem=='C') ? 'Compra' : 'Serviço';

// recebe true caso haja alguma qtde <= 0
$obErro = false;
// recebe o número da listagem dos itens que tiverem a qtde <= 0
$arItens = array();
$itemZerado = 0;
$i = 1;

if ( strpos($stAcao,"anular")===false && strpos($stAcao,"reemitir")===false ) {
    $arItens = Sessao::read('arItens');

    foreach ($arItens as $chave =>$dados) {

        $inQtdItem = str_replace(',','.',str_replace('.','',$_REQUEST['qtdeOC_'.$i]));

        if ($inQtdItem == 0) {
            $itemZerado++;
        }
        $i++;
    }

    if ($itemZerado == count($arItens)) {
        $obErro = true;
    }

    if ( count($arItens) < 1 ) {
       $obErro = true;
    }

}

/* Faz a validação para verificar se a quantidade é maior que zero */
if ($obErro == true) {

    if ($itemZerado == count($arItens)) {
        SistemaLegado::exibeAviso("A quantidade de ao menos um item deve ser maior que zero!");
    } else {
        if (count($arItens) > 1) {
            SistemaLegado::exibeAviso("A qtde. dos itens ".implode(",", $arItens)." deve ser maior que zero");
        } elseif ( count($arItens) <= 0 ) {
            SistemaLegado::exibeAviso("Deve ser incluído pelo menos um item na lista.");
        }
    }

} else {

    switch ($_REQUEST['stAcao']) {

    case "incluir":
    case "incluirOS":
        Sessao::write ('stIncluirAssinaturaUsuario', $_REQUEST['stIncluirAssinaturaUsuario']);
        $stExercicioOrdemCompra = Sessao::getExercicio();

        $obTOrdemCompra = new TComprasOrdem();
        $obTOrdemCompra->setDado('exercicio_empenho',$_REQUEST["stExercicioEmpenho"]);
        $obTOrdemCompra->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
        $obTOrdemCompra->setDado('exercicio',Sessao::getExercicio());
        $obTOrdemCompra->setDado('tipo', $stTipoOrdem);
        $obTOrdemCompra->proximoCod($inCodOrdem);
        $obTOrdemCompra->setDado('cod_ordem', $inCodOrdem);
        $obTOrdemCompra->setDado('cod_empenho', $_REQUEST["inCodEmpenho"]);
        $obTOrdemCompra->setDado('observacao', $_REQUEST["stObservacao"]);
        $obTOrdemCompra->inclusao();

        $inCount = 1;
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $key => $value) {
            $inQuantidade = str_replace(',','.',str_replace('.','',$_REQUEST['qtdeOC_'.$inCount]));

            if ($inQuantidade > 0) {
                $obTOrdemCompraItem = new TComprasOrdemItem;
                $obTOrdemCompraItem->setDado('exercicio'       , Sessao::getExercicio());
                $obTOrdemCompraItem->setDado('exercicio_pre_empenho' , $_REQUEST['stExercicioEmpenho']);
                $obTOrdemCompraItem->setDado('cod_entidade'    , $_REQUEST['inCodEntidade']);
                $obTOrdemCompraItem->setDado('cod_ordem'       , $obTOrdemCompra->getDado('cod_ordem'));
                $obTOrdemCompraItem->setDado('num_item'        , $value['num_item']);
                $obTOrdemCompraItem->setDado('cod_pre_empenho' , $value['cod_pre_empenho']);
                $obTOrdemCompraItem->setDado('quantidade'      , $inQuantidade);
                $obTOrdemCompraItem->setDado('tipo'            , $stTipoOrdem);
                $obTOrdemCompraItem->setDado('vl_total'        , ($value['vl_unitario'] * str_replace(',','.',str_replace('.','',$_REQUEST['qtdeOC_'.$inCount]))));
                $obTOrdemCompraItem->inclusao();
            }
            $inCount++;
        }
        SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$_REQUEST['inCodEntidade']."&inCodOrdem=".$inCodOrdem."&stTipo=".$_REQUEST['stTipo']."&stTipoOrdem=".$stTipoOrdem."&stExercicioOrdemCompra=".$stExercicioOrdemCompra,"Ordem de $stDesc - ".$obTOrdemCompra->getDado('cod_ordem'),"incluir","incluir_n", Sessao::getId(), "../");

    break;

    case "alterar":
    case "alterarOS":
        Sessao::write ('stIncluirAssinaturaUsuario', $_REQUEST['stIncluirAssinaturaUsuario']);

        // altera o campo observacao da tabela
        $obTOrdemCompra = new TComprasOrdem();
        $obTOrdemCompra->setDado('exercicio_empenho',$_REQUEST["stExercicioEmpenho"]);
        $obTOrdemCompra->setDado('exercicio', $_REQUEST['stExercicioOrdemCompra']);
        $obTOrdemCompra->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTOrdemCompra->setDado('cod_ordem', $_REQUEST['inCodOrdemCompra']);
        $obTOrdemCompra->setDado('exercicio_pre_empenho', $_REQUEST['stExercicioEmpenho']);
        $obTOrdemCompra->setDado('cod_empenho', $_REQUEST['inCodEmpenho']);
        $obTOrdemCompra->setDado('observacao', $_REQUEST['stObservacao'] );
        $obTOrdemCompra->setDado('tipo', $stTipoOrdem);
        $obTOrdemCompra->alteracao();

        // exclui os dados para inseri-los novamente na tabela
        $obTOrdemCompraItem = new TComprasOrdemItem();
        $obTOrdemCompraItem->setDado('exercicio', $_REQUEST['stExercicioOrdemCompra']);
        $obTOrdemCompraItem->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTOrdemCompraItem->setDado('cod_ordem', $_REQUEST['inCodOrdemCompra']);
        $obTOrdemCompraItem->setDado('exercicio_pre_empenho', $_REQUEST['stExercicioEmpenho']);
        $obTOrdemCompraItem->setDado('tipo', $stTipoOrdem);
        $obTOrdemCompraItem->exclusao();

        $inCount = 0;

        $arItens = Sessao::read('arItens');
        foreach ($arItens as $stChave => $stValor) {
            $inCount++;
            $inQuantidade = str_replace(',','.',str_replace('.','',$_REQUEST['qtdeOC_'.$inCount]));
            if ($inQuantidade > 0) {
                $obTOrdemCompraItem->setDado('exercicio', $_REQUEST['stExercicioOrdemCompra']);
                $obTOrdemCompraItem->setDado('exercicio_pre_empenho',$_REQUEST['stExercicioEmpenho']);
                $obTOrdemCompraItem->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
                $obTOrdemCompraItem->setDado('cod_ordem', $_REQUEST['inCodOrdemCompra']);
                $obTOrdemCompraItem->setDado('num_item', $stValor['num_item']);
                $obTOrdemCompraItem->setDado('cod_pre_empenho', $stValor['cod_pre_empenho']);
                $obTOrdemCompraItem->setDado('quantidade', $inQuantidade);
                $obTOrdemCompraItem->setDado('vl_total', $inQuantidade * $stValor['vl_unitario']);
                $obTOrdemCompraItem->setDado('tipo', $stTipoOrdem);
                $obTOrdemCompraItem->inclusao();
            }
        }
        SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$_REQUEST['inCodEntidade']."&inCodOrdem=".$_REQUEST['inCodOrdemCompra']."&stTipo=".$_REQUEST['stTipo']."&stTipoOrdem=".$stTipoOrdem."&stExercicioOrdemCompra=".$_REQUEST['stExercicioOrdemCompra'],"Ordem de $stDesc - ".$_REQUEST['inCodOrdemCompra'],"incluir","incluir", Sessao::getId(), "../");
    break;

    case "anular":
    case "anularOS":

        $obTOrdemCompraAnulacao = new TComprasOrdemAnulacao();
        $obTOrdemCompraAnulacao->setDado('exercicio', $_REQUEST['stExercicioOrdemCompra']);
        $obTOrdemCompraAnulacao->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTOrdemCompraAnulacao->setDado('cod_ordem', $_REQUEST['inCodOrdemCompra']);
        $obTOrdemCompraAnulacao->setDado('motivo', $_REQUEST['stMotivo']);
        $obTOrdemCompraAnulacao->setDado('tipo', $stTipoOrdem);
        $obTOrdemCompraAnulacao->inclusao();
        $obTOrdemCompraAnulacao->recuperaDados( $rsOrdemCompraAnulacao );

        $inCount = 0;
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $stChave => $stValor) {
            $inCount++;
            $flQuantidade = str_replace(",", ".", str_replace(".", "", $stValor["quantidade_original"]));
            if ($flQuantidade > 0) {
                $obTOrdemCompraItemAnulacao = new TComprasOrdemItemAnulacao();
                $obTOrdemCompraItemAnulacao->setDado('exercicio', $rsOrdemCompraAnulacao->getCampo("exercicio"));
                $obTOrdemCompraItemAnulacao->setDado('cod_entidade', $rsOrdemCompraAnulacao->getCampo("cod_entidade"));
                $obTOrdemCompraItemAnulacao->setDado('cod_ordem', $rsOrdemCompraAnulacao->getCampo("cod_ordem"));
                $obTOrdemCompraItemAnulacao->setDado('cod_pre_empenho', $stValor["cod_pre_empenho"]);
                $obTOrdemCompraItemAnulacao->setDado('num_item', $stValor["num_item"]);
                $obTOrdemCompraItemAnulacao->setDado('timestamp', $rsOrdemCompraAnulacao->getCampo("timestamp"));
                $obTOrdemCompraItemAnulacao->setDado('quantidade', $flQuantidade);
                $obTOrdemCompraItemAnulacao->setDado('vl_total', $flQuantidade * $stValor['vl_unitario']);
                $obTOrdemCompraItemAnulacao->setDado('tipo', $stTipoOrdem);
                $obTOrdemCompraItemAnulacao->setDado('exercicio_pre_empenho',$_REQUEST['stExercicioEmpenho']);
                $obTOrdemCompraItemAnulacao->inclusao();
            }
        }

        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Ordem de $stDesc - ".$_REQUEST['inCodOrdemCompra']."","excluir","excluir", Sessao::getId(), "../");
    break;

    case 'reemitir':
    case 'reemitirOS':
        $stIncluirAssinaturaUsuario = Sessao::read('stIncluirAssinaturaUsuario');
        SistemaLegado::alertaAviso($pgRel."&inCodEntidade=".$_REQUEST['inCodEntidade']."&inCodOrdem=".$_REQUEST['inCodOrdemCompra']."&stTipo=".$_REQUEST['stTipo']."&stTipoOrdem=".$_REQUEST['stTipoOrdem']."&stExercicioOrdemCompra=".$_REQUEST['stExercicioOrdemCompra']."&stIncluirAssinaturaUsuario=".$stIncluirAssinaturaUsuario,"Ordem de $stDesc - ".$_REQUEST['inCodOrdemCompra'],"incluir","incluir", Sessao::getId(), "../");
    break;

    }
}
Sessao::encerraExcecao();
?>
