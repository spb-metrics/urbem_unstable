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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 07/01/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  * @ignore
  * $Id: OCManterRegistroPreco.php 62567 2015-05-20 19:39:59Z evandro $
  * $Date: 2015-05-20 16:39:59 -0300 (Qua, 20 Mai 2015) $
  * $Author: evandro $
  * $Rev: 62567 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRegistroPrecos.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

$stPrograma = "ManterRegistroPreco";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

function validaNroAdesao($stCodAdesao, $inCodEntidade) {
    $stCodProcessoAdesao = explode('/', $stCodAdesao);
    
    if ( array_key_exists( 1, $stCodProcessoAdesao) ) {
        $stExercicioAdesao = (int)$stCodProcessoAdesao[1];
    } else {
        $stExercicioAdesao = Sessao::getExercicio();
    }
    
    if ( (int)$stCodProcessoAdesao[0] <> 0 ) {
        $stCodProcessoAdesaoTMP = str_pad($stCodProcessoAdesao[0], 12, "0", STR_PAD_LEFT) .'/'.$stExercicioAdesao;
        $stJs .= "jQuery('#stCodigoProcessoAdesao').val('".$stCodProcessoAdesaoTMP."'); \n";
    } else {
        $stJs .= "jQuery('#stCodigoProcessoAdesao').val(''); \n";
        $stJs .= "jQuery('#stCodigoProcessoAdesao').focus(); \n";
        $stJs .= "alertaAviso('Número de Processo de Adesão não pode ser igual a 0 (zero).','form','erro','".Sessao::getId()."'); \n";
    }
    
    return $stJs;
}

function validaNroProcesso($stCodProcesso, $inCodEntidade) {

    $stCodProcessoRegistroPrecos = explode('/', $stCodProcesso);
    
    if ( array_key_exists( 1, $stCodProcessoRegistroPrecos) ) {
        $stExercicioRegistroPrecos = (int)$stCodProcessoRegistroPrecos[1];
    } else {
        $stExercicioRegistroPrecos = Sessao::getExercicio();
    }
    
    $rsProcessoRegistroPrecos = new RecordSet();
    $obTTCEMGRegistroPrecos = new TTCEMGRegistroPrecos();
    $obTTCEMGRegistroPrecos->setDado('cod_entidade'          , $inCodEntidade);
    $obTTCEMGRegistroPrecos->setDado('numero_registro_precos', (int)$stCodProcessoRegistroPrecos[0]);
    $obTTCEMGRegistroPrecos->setDado('exercicio'             , $stExercicioRegistroPrecos);
    $obTTCEMGRegistroPrecos->recuperaPorChave($rsProcessoRegistroPrecos);
    
    if ( $rsProcessoRegistroPrecos->getNumLinhas() < 0 ) {
        $stCodProcessoRegistroPrecosTMP = str_pad($stCodProcessoRegistroPrecos[0], 12, "0", STR_PAD_LEFT) .'/'.$stExercicioRegistroPrecos;
        $stJs .= "jQuery('#stCodigoProcesso').val('".$stCodProcessoRegistroPrecosTMP."'); \n";
    } else {
        $stJs .= "jQuery('#stCodigoProcesso').val(''); \n";
        $stJs .= "alertaAviso('Já existe um número de processo igual para a mesma entidade no mesmo exerício (".str_pad($stCodProcessoRegistroPrecos[0], 7, "0", STR_PAD_LEFT) .'/'.$stExercicioRegistroPrecos.")','form','erro','".Sessao::getId()."'); \n";
    }
    return $stJs;
}

function montaFormLote() {

    $obTxtCodigoLote = new TextBox();
    $obTxtCodigoLote->setRotulo('Número do Lote');
    $obTxtCodigoLote->setTitle('Número do Lote.');
    $obTxtCodigoLote->setName('stCodigoLote');
    $obTxtCodigoLote->setId('stCodigoLote');
    $obTxtCodigoLote->setMascara('9999');
    $obTxtCodigoLote->setMaxLength(4);
    $obTxtCodigoLote->obEvento->setOnBlur("ajaxJavaScript('OCManterRegistroPreco.php?".Sessao::getId()."&inCodLote='+this.value,'buscaDescricaoLote');");

    $obTxtDescricaoLote = new TextArea();
    $obTxtDescricaoLote->setRotulo('Descrição do Lote');
    $obTxtDescricaoLote->setTitle('Descrição do Lote.<br/>Regra de unicidade. Não poderá ser cadastrado uma mesma descrição para dois lotes de um mesmo processo de adesão.');
    $obTxtDescricaoLote->setName('txtDescricaoLote');
    $obTxtDescricaoLote->setId('txtDescricaoLote');
    $obTxtDescricaoLote->setMaxCaracteres(250);
    
    $obVlrPercentualLote = new Porcentagem();
    $obVlrPercentualLote->setId( 'nuPercentualLote' );
    $obVlrPercentualLote->setName( 'nuPercentualLote' );
    $obVlrPercentualLote->setRotulo( 'Percentual por Lote' );
    $obVlrPercentualLote->setTitle( 'Selecione o percentual de desconto por Lote' );
    $obVlrPercentualLote->setValue(0);

    $obFormulario = new Formulario();
    $obFormulario->addTitulo ('Dados do Lote');
    $obFormulario->addComponente( $obTxtCodigoLote );
    $obFormulario->addComponente( $obTxtDescricaoLote );
    $obFormulario->addComponente( $obVlrPercentualLote );

    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";
    $stJs .= "jQuery('#spnLote').html('".$stHtml."'); ";    
    $stJs .= "jQuery('#nuPercentualItem').attr('disabled', 'disabled'); ";
    $stJs .= " if (jQuery('#inDescontoTabela:checked').val() == 2) { jQuery('#nuPercentualLote').attr('disabled', 'disabled'); } "; 
    
    $stJs .= montaLoteAbaQuantitativo();
    
    return $stJs;
}

function buscaDescricaoLote($inCodLote)
{
    $arItens = Sessao::read('arItens');
   
    if (is_array($arItens) && count($arItens) > 0) {
        foreach ($arItens as $item) {
            if ($item['stCodigoLote'] == $inCodLote) {
                return "jQuery('#txtDescricaoLote').val('".$item['txtDescricaoLote']."'); ";
            }
        }    
    }
    
    return "jQuery('#txtDescricaoLote').val(''); ";

}

function incluirListaItens()
{
    $obErro  = new Erro();

    $arItens = Sessao::read('arItens');

    # Validação necessária para não permitir a mesma descrição de lote para lotes no mesmo processo.
    if (is_array($arItens) && count($arItens) > 0) {
        foreach ($arItens as $item) {
            if ($_REQUEST['txtDescricaoLote'] == $item['txtDescricaoLote'] &&            
                $_REQUEST['stCodigoLote'] != $item['stCodigoLote'] && isset($_REQUEST['stCodigoLote'])) {

                $obErro->setDescricao("Não é permitido a mesma descrição para lotes diferentes no mesmo processo");

                break;
            }
        }
    }
    
    # Validação para não permitir cadastrar lote = 0 (zero)
    if (isset($_REQUEST['stCodigoLote']) && $_REQUEST['stCodigoLote'] == 0) {
        $obErro->setDescricao("Código de Lote não pode ser igual a 0.");
    }

    if (!$obErro->ocorreu()) {
        if ($_REQUEST['dtCotacao'] != "" &&
            $_REQUEST['inCodItem'] != "" &&
            $_REQUEST['inNumItemLote'] != "" &&
            $_REQUEST['nuVlReferencia'] > '0,0000' &&
            $_REQUEST['nuQuantidade'] > '0,0000' &&
            $_REQUEST['nuVlTotal'] > '0,0000' &&
            $_REQUEST['stNomItem'] != "" &&
            $_REQUEST['inNumCGMVencedor'] != "" &&
            $_REQUEST['inOrdemClassifFornecedor'] != "") {
            
            $arItens = Sessao::read('arItens');
            $arItensRemovido = Sessao::read('arItensRemovido');
            
            $inIdMax=0;

            if (is_array($arItens) && count($arItens) > 0) {
                foreach($arItens as $arItem) {                    
                    $inIdMax = ($arItem['inId']>$inIdMax||$arItem['inId']==$inIdMax) ? $arItem['inId']+1 : $inIdMax;
                }
            }
            if (is_array($arItensRemovido) && count($arItensRemovido) > 0) {
                foreach($arItensRemovido as $arRemovido) {                    
                    $inIdMax = ($arRemovido['inId']>$inIdMax||$arRemovido['inId']==$inIdMax) ? $arRemovido['inId']+1 : $inIdMax;
                }
            }
    
            $arItensCotacao = array();
            $arItensCotacao['inCodItem']        = $_REQUEST['inCodItem'];
            $arItensCotacao['inNumItemLote']    = $_REQUEST['inNumItemLote'];
            $arItensCotacao['inOrdemClassifFornecedor'] = $_REQUEST['inOrdemClassifFornecedor'];
            $arItensCotacao['stNomItem']        = $_REQUEST['stNomItem'];
            $arItensCotacao['stNomUnidade']     = $_REQUEST['stNomUnidade'];
            $arItensCotacao['nuVlReferencia']   = $_REQUEST['nuVlReferencia'];
            $arItensCotacao['nuQuantidade']     = $_REQUEST['nuQuantidade'];
            $arItensCotacao['nuVlTotal']        = $_REQUEST['nuVlTotal'];
            $arItensCotacao['dtCotacao']        = $_REQUEST['dtCotacao'];
            $arItensCotacao['stCodigoLote']     = (!empty($_REQUEST['stCodigoLote']) ? $_REQUEST['stCodigoLote'] : 0);
            $arItensCotacao['nuPercentualLote'] = $_REQUEST['nuPercentualLote'];
            $arItensCotacao['txtDescricaoLote'] = $_REQUEST['txtDescricaoLote'];
            $arItensCotacao['nuVlUnitario']     = $_REQUEST['nuVlUnitario'];
            $arItensCotacao['nuQtdeLicitada']   = $_REQUEST['nuQtdeLicitada'];
            $arItensCotacao['nuQtdeAderida']    = $_REQUEST['nuQtdeAderida'];
            $arItensCotacao['nuPercentualItem'] = $_REQUEST['nuPercentualItem'];
            $arItensCotacao['inNumCGMVencedor'] = $_REQUEST['inNumCGMVencedor'];
            $arItensCotacao['inId']             = $inIdMax;
            $arItensCotacao['stNomCGMVencedor'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inNumCGMVencedor']);
           
            if ($arItens != "") {
                foreach ($arItens as $arrItem) {
                    if ($arrItem['inNumItemLote'] == $arItensCotacao['inNumItemLote'] && $arrItem['stCodigoLote'] == $arItensCotacao['stCodigoLote'] && $arrItem['inNumCGMVencedor'] == $arItensCotacao['inNumCGMVencedor']) {
                        $obErro->setDescricao("Este Número de Item já está na lista!");
                        break;
                    }
                    if ($arrItem['inNumItemLote'] == $arItensCotacao['inNumItemLote'] && $arrItem['stCodigoLote'] == $arItensCotacao['stCodigoLote'] && $arrItem['inCodItem'] != $arItensCotacao['inCodItem'] ) {
                        $obErro->setDescricao("Este Número de Item já está na lista!");
                        break;
                    }
                    if ($arrItem['inCodItem'] == $arItensCotacao['inCodItem'] && $arrItem['inNumCGMVencedor'] == $arItensCotacao['inNumCGMVencedor']) {
                        $obErro->setDescricao("Este Item já está na lista, para esse CGM de Fornecedor!");
                        break;
                    }
                }
            }
    
        } else {
           $obErro->setDescricao("Informe Todos os campos!");
        }
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arItens[] = $arItensCotacao;
        Sessao::write('arItens', $arItens);
        $stJs .= montaListaItens();
        $stJs .= limparFormItem();
        
        $boLote = ($_REQUEST['stCodigoLote']>0) ? true : false;
        $stJs .= preencheLoteOuNumItemAbaQuantitativo($boLote);
    }
    
    return $stJs;
}

function alterarListaItens()
{
    $obErro  = new Erro();

    $arItens = Sessao::read('arItens');
    
    if ($_REQUEST['stAcao'] == 'incluir') {
        # Validação necessária para não permitir a mesma descrição de lote para lotes no mesmo processo.
        if (is_array($arItens) && count($arItens) > 0) {
            foreach ($arItens as $item) {
                if ($_REQUEST['txtDescricaoLote'] == $item['txtDescricaoLote'] &&            
                    $_REQUEST['stCodigoLote'] != $item['stCodigoLote'] && isset($_REQUEST['stCodigoLote'])) {
    
                    $obErro->setDescricao("Não é permitido a mesma descrição para lotes diferentes no mesmo processo");
    
                    break;
                }
            }
        }
    
        # Validação para não permitir cadastrar lote = 0 (zero)
        if (isset($_REQUEST['stCodigoLote']) && $_REQUEST['stCodigoLote'] == 0) {
            $obErro->setDescricao("Código de Lote não pode ser igual a 0.");
        }

        $_REQUEST['stCodigoLote'] = (isset($_REQUEST['stCodigoLote'])) ? $_REQUEST['stCodigoLote'] : 0;
    
        if (!$obErro->ocorreu()) {
            if ($_REQUEST['dtCotacao'] != "" &&
                $_REQUEST['inCodItem'] != "" &&
                $_REQUEST['inNumItemLote'] != "" &&
                $_REQUEST['nuVlReferencia'] > '0,0000' &&
                $_REQUEST['nuQuantidade'] > '0,0000' &&
                $_REQUEST['nuVlTotal'] > '0,0000' &&
                $_REQUEST['stNomItem'] != "" &&
                $_REQUEST['inNumCGMVencedor'] != "" &&
                $_REQUEST['inOrdemClassifFornecedor'] != "") {
            
                foreach ($arItens as $key => $arItem) {
                    if($arItem['inId'] != $_REQUEST['inIdItem']){
                        if ($arItem['inNumItemLote'] == $_REQUEST['inNumItemLote'] && $arItem['stCodigoLote'] == $_REQUEST['stCodigoLote'] && $arItem['inNumCGMVencedor'] == $_REQUEST['inNumCGMVencedor']) {
                            $obErro->setDescricao("Este Número de Item já está na lista!");
                            break;
                        }
                        if ($arItem['inNumItemLote'] == $_REQUEST['inNumItemLote'] && $arItem['stCodigoLote'] == $_REQUEST['stCodigoLote'] && $arItem['inCodItem'] != $_REQUEST['inCodItem'] ) {
                            $obErro->setDescricao("Este Número de Item já está na lista!");
                            break;
                        }
                        if ($arItem['inCodItem'] == $_REQUEST['inCodItem'] && $arItem['inNumCGMVencedor'] == $_REQUEST['inNumCGMVencedor']) {
                            $obErro->setDescricao("Este Item já está na lista, para esse CGM de Fornecedor!");
                            break;
                        }                    
                    }
                }
                
                if (!$obErro->ocorreu()) {
                    foreach ($arItens as $key => $arItem) {
                        if ($arItem['inId'] == $_REQUEST['inIdItem']) {
                
                            $arItens[$key]['nuVlReferencia']   = $_REQUEST['nuVlReferencia'];
                            $arItens[$key]['inNumItemLote']    = $_REQUEST['inNumItemLote'];
                            $arItens[$key]['inOrdemClassifFornecedor']    = $_REQUEST['inOrdemClassifFornecedor'];
                            $arItens[$key]['nuQuantidade']     = $_REQUEST['nuQuantidade'];
                            $arItens[$key]['nuVlTotal']        = $_REQUEST['nuVlTotal'];
                            $arItens[$key]['dtCotacao']        = $_REQUEST['dtCotacao'];
                            $arItens[$key]['stCodigoLote']     = (!empty($_REQUEST['stCodigoLote']) ? $_REQUEST['stCodigoLote'] : 0);
                            $arItens[$key]['nuPercentualLote'] = $_REQUEST['nuPercentualLote'];
                            $arItens[$key]['txtDescricaoLote'] = $_REQUEST['txtDescricaoLote'];
                            $arItens[$key]['nuVlUnitario']     = $_REQUEST['nuVlUnitario'];
                            $arItens[$key]['nuQtdeLicitada']   = $_REQUEST['nuQtdeLicitada'];
                            $arItens[$key]['nuQtdeAderida']    = $_REQUEST['nuQtdeAderida'];        
                            $arItens[$key]['nuPercentualItem'] = $_REQUEST['nuPercentualItem'];
                            $arItens[$key]['inNumCGMVencedor'] = $_REQUEST['inNumCGMVencedor'];
                            $arItens[$key]['stNomCGMVencedor'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inNumCGMVencedor']);
                        
                            Sessao::write('arItens', $arItens);
                        
                            break;
                        }
                    }
                }
        
            } else {
               $obErro->setDescricao("Informe Todos os campos!");
            }
        }
    }else{
        foreach ($arItens as $key => $arItem) {
            if ($arItem['inId'] == $_REQUEST['inIdItem']) {
                
                $arItens[$key]['nuVlReferencia']   = $_REQUEST['nuVlReferencia'];
                $arItens[$key]['inNumItemLote']    = $_REQUEST['inNumItemLote'];
                $arItens[$key]['inOrdemClassifFornecedor']    = $_REQUEST['inOrdemClassifFornecedor'];
                $arItens[$key]['nuQuantidade']     = $_REQUEST['nuQuantidade'];
                $arItens[$key]['nuVlTotal']        = $_REQUEST['nuVlTotal'];
                $arItens[$key]['dtCotacao']        = $_REQUEST['dtCotacao'];
                $arItens[$key]['stCodigoLote']     = (!empty($_REQUEST['stCodigoLote']) ? $_REQUEST['stCodigoLote'] : 0);
                $arItens[$key]['nuPercentualLote'] = $_REQUEST['nuPercentualLote'];
                $arItens[$key]['txtDescricaoLote'] = $_REQUEST['txtDescricaoLote'];
                $arItens[$key]['nuVlUnitario']     = $_REQUEST['nuVlUnitario'];
                $arItens[$key]['nuQtdeLicitada']   = $_REQUEST['nuQtdeLicitada'];
                $arItens[$key]['nuQtdeAderida']    = $_REQUEST['nuQtdeAderida'];        
                $arItens[$key]['nuPercentualItem'] = $_REQUEST['nuPercentualItem'];
                $arItens[$key]['inNumCGMVencedor'] = $_REQUEST['inNumCGMVencedor'];
                $arItens[$key]['stNomCGMVencedor'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inNumCGMVencedor']);
                        
                Sessao::write('arItens', $arItens);
                        
            break;
            }
        }
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $stJs .= limparFormItem();
        $stJs .= montaListaItens();
        $stJs .= montaItemBuscaInner();
        
        $boLote = ($_REQUEST['stCodigoLote']>0) ? true : false;
        $stJs .= preencheLoteOuNumItemAbaQuantitativo($boLote);
    }

    return $stJs;
}

function montaItemBuscaInner()
{
    include_once( CAM_GP_ALM_COMPONENTES."IPopUpItem.class.php" );

    $obIPopUpCatalogoItem = new IPopUpItem(new Form);
    $obIPopUpCatalogoItem->setRotulo("*Item");
    $obIPopUpCatalogoItem->setNull           ( true );
    $obIPopUpCatalogoItem->setRetornaUnidade ( true );

    $obFormulario = new Formulario();    
    $obFormulario->addComponente($obIPopUpCatalogoItem);

    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= " jQuery('#spnBuscaInnerItem').html('".$stHtml."'); \n";

    return $stJs;
    
}


function montaListaItens()
{
    global $pgOcul;
    $stCaminho = "../../../../../../gestaoPrestacaoContas/fontes/PHP/TCEMG/instancias/configuracao/".$pgOcul;
    
    $rsRecordSet = new RecordSet();
    
    if (Sessao::read('arItens') != "") {
        $rsRecordSet->preenche(Sessao::read('arItens'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Itens" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nro. Lote" );
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nro. Item" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Vlr. Referência" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Quantidade" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Total" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data Cotação" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Vlr. Unitário" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtde. Licitada" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Qtde. Aderida" );
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "CGM Vencedor / Class" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
        
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stCodigoLote" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNumItemLote" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodItem] - [stNomItem]" );
    $obLista->ultimoDado->setAlinhamento('ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlReferencia" );
    $obLista->ultimoDado->setAlinhamento('DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQuantidade" );
    $obLista->ultimoDado->setAlinhamento('DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlTotal" );
    $obLista->ultimoDado->setAlinhamento('DIREITA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtCotacao" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlUnitario" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQtdeLicitada" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQtdeAderida" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[stNomCGMVencedor]/[inOrdemClassifFornecedor]º" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarItem');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
    
    $stDescQuestao = "Excluindo este item, será excluído da aba Quantitativos por Orgão em cadeia. Excluir Item?";
    $stCtrl = "excluirItem";
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alertaQuestaoValor('".$stCaminho."*-*-*stCtrl=".$stCtrl."*_*frameDestino=oculto*_*stDescQuestao=".$stDescQuestao."','sn','".Sessao::getId()."');" );
    $obLista->ultimaAcao->addCampo("1","&chave=inId&valor=[inId]");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaItens').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function alterarItem()
{
    $arItens = Sessao::read('arItens');
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    $obErro = new Erro();
    
    foreach ($arItens as $arItem) {
        if ($arItem['inId'] == $_REQUEST['inId']) {

            //Validacao comentada porque existe dados que foram importados e essa regra nao é aplicada
            // foreach ($arOrgaoItemQuantitativos as $arQuantitativo) {                
            //     if ($arQuantitativo['inCodLoteQ'] == $arItem['stCodigoLote'] &&
            //         $arQuantitativo['inNumItemQ'] == $arItem['inNumItemLote'] &&
            //         $arQuantitativo['inCodItemQ'] == $arItem['inCodItem'] &&
            //         $arQuantitativo['inCodFornecedorQ'] == $arItem['inNumCGMVencedor']) {

            //         $obErro->setDescricao("Item ".$arItem['inNumItemLote']." do Lote ".$arItem['stCodigoLote']." não pode ser alterado, pois está sendo utilizado na aba Quantitativos por Orgão!");
            //     }
            // }

            if ($obErro->ocorreu()) {
                $stJs  = limparFormItem();
                $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');       \n";
                $stJs .= "jQuery('#Ok').focus();                                                                \n";
            }else{
                $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                            \n";
                $stJs .= "jQuery('#btnSalvar').val('Alterar Item');                                             \n";
                $stJs .= "jQuery('#btnSalvar').attr('onclick', 'montaParametrosGET(\'alterarListaItens\');');   \n";
    
                # Preenche informações do Lote
                $stJs .= "jQuery('#stCodigoLote').val('".$arItem['stCodigoLote']."');                           \n";
                $stJs .= "jQuery('#txtDescricaoLote').val('".$arItem['txtDescricaoLote']."');                   \n";
                $stJs .= "jQuery('#nuPercentualLote').val('".$arItem['nuPercentualLote']."');                   \n";
                $stJs .= "jQuery('#inNumItemLote').val('".$arItem['inNumItemLote']."');                         \n";
                $stJs .= "jQuery('#inOrdemClassifFornecedor').val('".$arItem['inOrdemClassifFornecedor']."');   \n";
                $stJs .= "jQuery('#dtCotacao').val('".$arItem['dtCotacao']."');                                 \n";
                
                $stJs .= alteraItemLabel($arItem);

                $stJs .= "jQuery('#imgBuscar').parent('a').css('visibility', 'hidden');                                     \n";
                $stJs .= "jQuery('#stUnidadeMedida').html('".$arItem['stNomUnidade']."');                       \n";
                $stJs .= "jQuery('#nuVlReferencia').val('".$arItem['nuVlReferencia']."');                       \n";
                $stJs .= "jQuery('#nuQuantidade').val('".$arItem['nuQuantidade']."');                           \n";
                $stJs .= "jQuery('#nuVlTotal').val('".$arItem['nuVlTotal']."');                                 \n";
                $stJs .= "jQuery('#nuVlUnitario').val('".$arItem['nuVlUnitario']."');                           \n";
                $stJs .= "jQuery('#nuQtdeLicitada').val('".$arItem['nuQtdeLicitada']."');                       \n";
                $stJs .= "jQuery('#nuQtdeAderida').val('".$arItem['nuQtdeAderida']."');                         \n";
                $stJs .= "jQuery('#nuPercentualItem').val('".$arItem['nuPercentualItem']."');                   \n";
                $stJs .= "jQuery('#inNumCGMVencedor').val('".$arItem['inNumCGMVencedor']."');                   \n";
                $stJs .= "jQuery('#inNomCGMVencedor').html('".$arItem['stNomCGMVencedor']."');                  \n";
                $stJs .= "jQuery('#inIdItem').val('".$arItem['inId']."');                                       \n";
                $stJs .= "jQuery('#dtCotacao').focus();                                                         \n";
            }

            break;
        }
    }

    SistemaLegado::executaFrameOculto($stJs);
}


function alteraItemLabel($arItem)
{

    $stJs .= " jQuery('#stNomItem').parent().parent().parent().parent().parent().detach(); \n";
    
    $obLblItem = new Label();
    $obLblItem->setRotulo    ("*Item");
    $obLblItem->setName      ("lblItem");
    $obLblItem->setId        ("lblItem");

    $obLblDescricaoItem = new Label();
    $obLblDescricaoItem->setName     ("lblDescricaoItem");
    $obLblDescricaoItem->setId       ("lblDescricaoItem");
    $obLblDescricaoItem->setValue    ($arItem["inCodItem"]." - ".$arItem["stNomItem"]);

    $obFormulario = new Formulario();    
    $obFormulario->agrupaComponentes( array($obLblItem,$obLblDescricaoItem) );

    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= " jQuery('#spnBuscaInnerItem').html('".$stHtml."'); \n";

    return $stJs;
}

function excluirItem()
{
    $arTemp = array();

    $arItens = Sessao::read('arItens');
    $arItensRemovido = Sessao::read('arItensRemovido');
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');

    foreach ($arItens as $arItem) {
        if ($arItem['inId'] != $_REQUEST['inId']) {
            $arTemp[] = $arItem;
        } else {
            $arItensRemovido[] = $arItem;
            foreach( $arOrgaoItemQuantitativos as $arQuantitativo) {
                if ($arItem['inNumItemLote'] == $arQuantitativo['inNumItemQ'] && $arItem['stCodigoLote'] == $arQuantitativo['inCodLoteQ']){
                    $stJs .= excluirQuantitativo($arQuantitativo['inId']);
                }

            }
        }
    }

    $arItens = $arTemp;

    Sessao::write('arItensRemovido', $arItensRemovido);
    Sessao::write('arItens', $arItens);

    $stJs .= montaListaItens();

    SistemaLegado::executaFrameOculto($stJs);
}

function limparFormItem()
{
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;    \n";
    $stJs .= "if(jQuery('#stCodigoLote')){                                  \n";
    $stJs .= "      jQuery('#stCodigoLote').val('');                        \n";
    $stJs .= "}                                                             \n";
    $stJs .= "jQuery('#inNumItemLote').val('');                             \n";
    $stJs .= "jQuery('#inOrdemClassifFornecedor').val('');                  \n";
    $stJs .= "jQuery('#txtDescricaoLote').val('');                          \n";
    $stJs .= "jQuery('#nuPercentualLote').val('');                          \n";
    $stJs .= "jQuery('#dtCotacao').val('');                                 \n";
    $stJs .= "jQuery('input[name=inCodItem]').val('');                      \n";
    $stJs .= "jQuery('#stNomItem').html('&nbsp;');                          \n";
    $stJs .= "jQuery('#stUnidadeMedida').html('&nbsp;');                    \n";
    $stJs .= "jQuery('#nuVlReferencia').val('0,0000');                      \n";
    $stJs .= "jQuery('#nuQuantidade').val('0,0000');                        \n";
    $stJs .= "jQuery('#nuVlTotal').val('0,0000');                           \n";
    $stJs .= "jQuery('#nuVlUnitario').val('0,0000');                        \n";
    $stJs .= "jQuery('#nuQtdeLicitada').val('0,0000');                      \n";
    $stJs .= "jQuery('#nuQtdeAderida').val('0,0000');                       \n";
    $stJs .= "jQuery('#nuPercentualItem').val('0');                         \n";
    $stJs .= "jQuery('#inNumCGMVencedor').val('');                          \n";
    $stJs .= "jQuery('#inNomCGMVencedor').html('');                         \n";
    $stJs .= "jQuery('#dtCotacao').focus();                                 \n";
    $stJs .= "jQuery('#btnSalvar').val('Incluir Item');                     \n";
    $stJs .= "jQuery('#btnSalvar').attr('onclick', 'montaParametrosGET(\'incluirListaItens\');');\n";
    $stJs .= "jQuery('#stCtrl').val('incluirListaItens');                   \n";
    $stJs .= "jQuery('input[name=inCodItem]').removeAttr('readonly');       \n";
    $stJs .= "jQuery('#imgBuscar').css('visibility', '');                   \n";

    $stJs .= montaListaItens();
    
    return $stJs;
}

function buscaOrgaoGerenciador()
{
    $obRegra = new RCGM;
    
    $stText = "inNumOrgaoGerenciador";
    $stSpan = "inNomOrgaoGerenciador";
    $stNull = "&nbsp;";
    
    if ($_REQUEST[ $stText ] != "" AND $_REQUEST[ $stText ] != "0") {
        $obRegra->setNumCGM ($_REQUEST[$stText]);
        $obRegra->listar ($rsCGM);
        
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";'."\n";
        }
    } else {
        $stJs .= 'f.'.$stText.'.value = "";'."\n";
        $stJs .= 'f.'.$stText.'.focus();'."\n";
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');"."\n";
    }
    
    return $stJs;
}

function buscaCGMResponsavel()
{
    $obRegra = new RCGM;
    
    $stText = "inNumCGMResponsavel";
    $stSpan = "inNomCGMResponsavel";
    $stNull = "&nbsp;";
    
    if ($_REQUEST[ $stText ] != "" AND $_REQUEST[ $stText ] != "0") {
        $obRegra->setNumCGM ($_REQUEST[$stText]);
        $obRegra->listar ($rsCGM);
        
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";'."\n";
        }
    } else {
        $stJs .= 'f.'.$stText.'.value = "";'."\n";
        $stJs .= 'f.'.$stText.'.focus();'."\n";
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');"."\n";
    }
    
    return $stJs;
}

function buscaCGMVencedor()
{
    $obRegra = new RCGM;
    
    $stText = "inNumCGMVencedor";
    $stSpan = "inNomCGMVencedor";
    $stNull = "&nbsp;";
    
    if ($_REQUEST[ $stText ] != "" AND $_REQUEST[ $stText ] != "0") {
        $obRegra->setNumCGM ($_REQUEST[$stText]);
        $obRegra->listar ($rsCGM);
        
        if ( $rsCGM->getNumLinhas() <= 0) {
            $stJs .= 'f.'.$stText.'.value = "";';
            $stJs .= 'f.'.$stText.'.focus();';
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";';
            $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');";
        } else {
            $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.($rsCGM->getCampo('nom_cgm')?$rsCGM->getCampo('nom_cgm'):$stNull).'";'."\n";
        }
    } else {
        $stJs .= 'f.'.$stText.'.value = "";'."\n";
        $stJs .= 'f.'.$stText.'.focus();'."\n";
        $stJs .= 'd.getElementById("'.$stSpan.'").innerHTML = "'.$stNull.'";'."\n";
        $stJs .= "alertaAviso('@Valor inválido. (".$_REQUEST[ $stText ].")','form','erro','".Sessao::getId()."');"."\n";
    }
    
    return $stJs;
}

function incluirEmpenho()
{
    include_once CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php";
    
    $stExercicio = $_REQUEST['stExercicioEmpenho'];
    list($numEmpenho) = explode( '/', $_REQUEST['numEmpenho']);
    $obErro = new Erro();
    $obREmpenhoEmpenho = new REmpenhoEmpenho();
    $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
    $obREmpenhoEmpenho->setCodEmpenho($numEmpenho);
    $obREmpenhoEmpenho->setExercicio($stExercicio);
    
    $arEmpenhos = Sessao::read("arEmpenhos");
    $arEmpenhosRemovidos = Sessao::read('arEmpenhosRemovidos');
    
    $inIdMax = 0;
    if (is_array($arEmpenhos) && count($arEmpenhos) > 0) {
        foreach($arEmpenhos as $arEmpenho) {            
            $inIdMax = ($arEmpenho['inId']>$inIdMax||$arEmpenho['inId']==$inIdMax) ? $arEmpenho['inId']+1 : $inIdMax;
        }
    }
    if (is_array($arEmpenhosRemovidos) && count($arEmpenhosRemovidos) > 0) {
        foreach($arEmpenhosRemovidos as $arRemovido) {            
            $inIdMax = ($arRemovido['inId']>$inIdMax||$arRemovido['inId']==$inIdMax) ? $arRemovido['inId']+1 : $inIdMax;
        }
    }
    
    $arEmpenhoNovo = array();
    if ( $stExercicio == Sessao::getExercicio() ) {
        $obREmpenhoEmpenho->listarConsultaEmpenho( $rsLista );
    } else {
        $obREmpenhoEmpenho->listarRestosConsultaEmpenho( $rsLista );
    }

    # Validação necessária para não permitir incluir o mesmo empenho em Registro de Preços diferentes.
    $boValidaInclusao = SistemaLegado::pegaDado("cod_empenho", "tcemg.empenho_registro_precos", " WHERE cod_entidade = ".$_REQUEST['inCodEntidade']." AND cod_empenho = ".$numEmpenho." AND exercicio_empenho = '".$stExercicio."'");
  
    if (!empty($boValidaInclusao)) {
        $obErro->setDescricao("Este empenho já foi vinculado em outro Registro de Preços");
    } else {
   
        if ( $rsLista->getNumLinhas() == 1 ) {
            $rsLista->addFormatacao("vl_empenhado","NUMERIC_BR");
            $arEmpenhoNovoTmp = $rsLista->getElementos();

            $arEmpenhoNovo['cod_entidade']   = $arEmpenhoNovoTmp[0]["cod_entidade"];
            $arEmpenhoNovo['exercicio']      = $arEmpenhoNovoTmp[0]["exercicio"];
            $arEmpenhoNovo['cod_empenho']    = $arEmpenhoNovoTmp[0]["cod_empenho"];
            $arEmpenhoNovo['nom_fornecedor'] = $arEmpenhoNovoTmp[0]["nom_fornecedor"];
            $arEmpenhoNovo['vl_empenhado']   = $arEmpenhoNovoTmp[0]["vl_empenhado"];
            $arEmpenhoNovo['dt_empenho']     = $arEmpenhoNovoTmp[0]["dt_empenho"];
            $arEmpenhoNovo['inId']           = $inIdMax;
        }else{
            $obErro->setDescricao("Não existe registro desse empenho na entidade selecionada!");
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        }
    
        if ($arEmpenhos != "") {
            foreach($arEmpenhos AS $arEmpenho){
                if ( $arEmpenhoNovo['cod_entidade'] == $arEmpenho['cod_entidade'] AND
                     $arEmpenhoNovo['exercicio']    == $arEmpenho['exercicio'] AND
                     $arEmpenhoNovo['cod_empenho']  == $arEmpenho['cod_empenho'])
                {
                    $obErro->setDescricao("Este empenho já foi adicionado na lista de Empenhos!");
                }
            }
        }
    
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
        return $stJs;
    } else {
        $arEmpenhos[] = $arEmpenhoNovo;
        Sessao::write('arEmpenhos',$arEmpenhos);
        $stJs .= limparFormEmpenho();
        $stJs .= montaListaEmpenho();
    }
    
    return $stJs;
}

function excluirEmpenho()
{
    $arTemp = array();

    $arEmpenhos = Sessao::read('arEmpenhos');
    $arEmpenhosRemovidos = Sessao::read('arEmpenhosRemovidos');

    foreach ($arEmpenhos as $arEmpenho) {
        if ($arEmpenho['inId'] != $_REQUEST['inId']) {
            $arTemp[] = $arEmpenho;
        } else {
            $arEmpenhosRemovidos[] = $arEmpenho;
        }
    }

    $arEmpenhos = $arTemp;

    Sessao::write('arEmpenhosRemovidos', $arEmpenhosRemovidos);
    Sessao::write('arEmpenhos', $arEmpenhos);

    $stJs .= montaListaEmpenho();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function limparFormEmpenho()
{
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";
    $stJs .= "jQuery('#numEmpenho').val('');                             \n";
    $stJs .= "jQuery('#stEmpenho').html('&nbsp;');                       \n";
    
    return $stJs;
}

function montaListaEmpenho()
{
    $rsRecordSet = new RecordSet();
    
    if (Sessao::read('arEmpenhos') != "") {
        $rsRecordSet->preenche(Sessao::read('arEmpenhos'));
    }

    $rsRecordSet->addFormatacao("vl_empenhado","NUMERIC_BR");

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Empenhos" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Número Empenho");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->ultimoCabecalho->setWidth( 60 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Empenho");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Empenho");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_fornecedor" );
    $obLista->ultimoDado->setAlinhamento('E' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_empenho" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_empenhado" );
    $obLista->ultimoDado->setAlinhamento('DIREITA' );
    $obLista->commitDado();
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirEmpenho');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
    
    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnEmpenhos').innerHTML = '".$stHtml."';\n";
    
    return $stJs;
}

function incluirListaOrgaos()
{
    $obErro  = new Erro();

    $arOrgaos = Sessao::read('arOrgaos');
    $arOrgaosRemovido = Sessao::read('arOrgaosRemovido');
    
    $inIdMax=0;

    if (is_array($arOrgaos) && count($arOrgaos) > 0) {
        foreach($arOrgaos as $arOrgao) {
            if ( $arOrgao['stExercicioOrgao'] == $_REQUEST['stExercicioOrgao'] && $arOrgao['inMontaCodOrgaoM'] == $_REQUEST['inMontaCodOrgaoM'] && $arOrgao['inMontaCodUnidadeM'] == $_REQUEST['inMontaCodUnidadeM']) {
                $obErro->setDescricao('Não é permitido adicionar Orgão e Unidade iguais para o mesmo exercicio.');
            }
            if ( $_REQUEST['inOrgaoGerenciador'] == 1 ) {
                if ( $arOrgao['inOrgaoGerenciador'] == $_REQUEST['inOrgaoGerenciador'] ) {
                    $obErro->setDescricao('Não é permitido adicionar mais do que um gerenciador para o Registro de Preço.');
                }
            }
            
            $inIdMax = ($arOrgao['inId']>$inIdMax||$arOrgao['inId']==$inIdMax) ? $arOrgao['inId']+1 : $inIdMax;
        }
    }
    
    if (is_array($arOrgaosRemovido) && count($arOrgaosRemovido) > 0) {
        foreach($arOrgaosRemovido as $arRemovido) {            
            $inIdMax = ($arRemovido['inId']>$inIdMax||$arRemovido['inId']==$inIdMax) ? $arRemovido['inId']+1 : $inIdMax;
        }
    }
    
    if(!$obErro->ocorreu()) {
        if ($_REQUEST['stExercicioOrgao'] != "" &&
            $_REQUEST['stUnidadeOrcamentaria'] != "" &&
            $_REQUEST['inNaturezaProcedimento'] != "" &&
            $_REQUEST['inMontaCodOrgaoM'] != "" &&
            $_REQUEST['inMontaCodUnidadeM'] != "" &&
            $_REQUEST['inOrgaoGerenciador'] != "") {
        
            $arUnidadeOrcamentaria = explode('.',$_REQUEST['stUnidadeOrcamentaria']);
            
            $arOrgaoRegPreg = array();
            $arOrgaoRegPreg['stExercicioOrgao']          = $_REQUEST['stExercicioOrgao'];
            $arOrgaoRegPreg['stUnidadeOrcamentaria']     = $_REQUEST['stUnidadeOrcamentaria'];
            $arOrgaoRegPreg['stMontaCodOrgaoM']          = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$_REQUEST['stExercicioOrgao']."' AND num_orgao = ".(int)$arUnidadeOrcamentaria[0]);
            $arOrgaoRegPreg['stMontaCodUnidadeM']        = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$_REQUEST['stExercicioOrgao']."' AND num_orgao = ".(int)$arUnidadeOrcamentaria[0]." AND num_unidade = ".(int)$arUnidadeOrcamentaria[1]);
            $arOrgaoRegPreg['inMontaCodOrgaoM']          = $_REQUEST['inMontaCodOrgaoM'];
            $arOrgaoRegPreg['inMontaCodUnidadeM']        = $_REQUEST['inMontaCodUnidadeM'];
            $arOrgaoRegPreg['inNaturezaProcedimento']    = $_REQUEST['inNaturezaProcedimento'];
            $arOrgaoRegPreg['inOrgaoGerenciador']        = $_REQUEST['inOrgaoGerenciador'];
            $arOrgaoRegPreg['stOrgaoGerenciador']        = ($_REQUEST['inOrgaoGerenciador'] == 1) ? "Sim":"Não";
            $arOrgaoRegPreg['stNaturezaProcedimento']    = ($_REQUEST['inNaturezaProcedimento'] == 1) ? "Órgão Participante":"Órgão Não Participante";
            $arOrgaoRegPreg['stCodigoProcessoAdesao']    = $_REQUEST['stCodigoProcessoAdesao'];
            $arOrgaoRegPreg['dtAdesao']                  = $_REQUEST['dtAdesao'];
            $arOrgaoRegPreg['dtPublicacaoAvisoIntencao'] = $_REQUEST['dtPublicacaoAvisoIntencao'];
            $arOrgaoRegPreg['inId']                      = $inIdMax;
           
        } else {
           $obErro->setDescricao("Informe Todos os campos com *!");
        }
    }
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arOrgaos[] = $arOrgaoRegPreg;
        Sessao::write('arOrgaos', $arOrgaos);
        $stJs .= montaListaOrgaos();
        $stJs .= limparFormOrgaos();
        $stJs .= preencheComboOrgaoAbaQuantitativo();
    }

    return $stJs;
}

function montaListaOrgaos()
{
    global $pgOcul;
    $stCaminho = "../../../../../../gestaoPrestacaoContas/fontes/PHP/TCEMG/instancias/configuracao/".$pgOcul;
    
    $rsRecordSet = new RecordSet();
    
    if (Sessao::read('arOrgaos') != "") {
        $rsRecordSet->preenche(Sessao::read('arOrgaos'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Orgãos" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Exercício Orgão Unidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Orgão");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Unidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Gerenciador");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Natureza do Procedimento");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nro. Processo de Adesão");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data da Adesão");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data da Intenção");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stExercicioOrgao" );
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stMontaCodOrgaoM" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stMontaCodUnidadeM" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stOrgaoGerenciador" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNaturezaProcedimento" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stCodigoProcessoAdesao" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtAdesao" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dtPublicacaoAvisoIntencao" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarOrgao');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
    
    $stDescQuestao = "Excluindo este orgão, será excluído da aba Quantitativos por Orgão em cadeia. Excluir Orgão?";
    $stCtrl = "excluirOrgao";
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:alertaQuestaoValor('".$stCaminho."*-*-*stCtrl=".$stCtrl."*_*frameDestino=oculto*_*stDescQuestao=".$stDescQuestao."','sn','".Sessao::getId()."');" );
    $obLista->ultimaAcao->addCampo("1","&chave=inId&valor=[inId]");
    $obLista->commitAcao();
    
    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnListaOrgao').innerHTML = '".$stHtml."';\n";
    
    return $stJs;
}

function limparFormOrgaos()
{
    $stJs .= "jQuery('input[name=stExercicioOrgao]').val('".Sessao::getExercicio()."');                     \n";
    $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').val('');                                          \n";
    $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').removeAttr('readonly');                           \n";
    $stJs .= "jQuery('#inMontaCodOrgaoM').val('');                                                          \n";
    $stJs .= "jQuery('#inMontaCodOrgaoM').removeAttr('disabled');                                           \n";
    $stJs .= "jQuery('#inMontaCodUnidadeM').val('');                                                        \n";
    $stJs .= "jQuery('#inMontaCodUnidadeM').removeAttr('disabled');                                         \n";
    $stJs .= "f.inMontaCodUnidadeM.options.length = 0;                                                      \n";
    $stJs .= "f.inMontaCodUnidadeM.options[0] = new Option('Selecione','');                                 \n";
    $stJs .= "jQuery('input:radio[name=\"inOrgaoGerenciador\"][value=\"1\"]').attr(\"checked\",false);      \n";
    $stJs .= "jQuery('input:radio[name=\"inOrgaoGerenciador\"][value=\"2\"]').attr(\"checked\",false);      \n";    
    $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"1\"]').attr(\"checked\",false);  \n";
    $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"2\"]').attr(\"checked\",false);  \n";
    $stJs .= "jQuery('#stCodigoProcessoAdesao').val('');                                                    \n";
    $stJs .= "jQuery('#dtAdesao').val('');                                                                  \n";
    $stJs .= "jQuery('#dtPublicacaoAvisoIntencao').val('');                                                 \n";
    $stJs .= "jQuery('#dtPublicacaoAvisoIntencao').focus();                                                 \n";
    $stJs .= "jQuery('#btnSalvarOrgao').val('Incluir Orgão');                                               \n";
    $stJs .= "jQuery('#btnSalvarOrgao').attr('onclick', 'montaParametrosGET(\'incluirListaOrgaos\')');      \n";
    $stJs .= montaListaOrgaos();
    
    return $stJs;
}

function preencheNatureza()
{
    $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                    \n";
    $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"1\"]').attr(\"checked\",true);   \n";
    $stJs .= "jQuery('#inNaturezaProcedimento2').attr('disabled', 'disabled');                              \n";
    
    return $stJs;
}

function excluirOrgao()
{
    $arTemp = $arTempRemovido = array();

    $arOrgaos = Sessao::read('arOrgaos');
    $arOrgaosRemovido = Sessao::read('arOrgaosRemovido');
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');

    foreach ($arOrgaos as $arOrgao) {
        if ($arOrgao['inId'] != $_REQUEST['inId']) {
            $arTemp[] = $arOrgao;
        } else {
            $arOrgaosRemovido[] = $arOrgao;
            
            foreach ($arOrgaoItemQuantitativos as $arQuantitativo) {
                $stUnidOrcaQuantitativo = str_pad($arQuantitativo['inCodOrgaoQ'], 2, "0", STR_PAD_LEFT).'.'.str_pad($arQuantitativo['inCodUnidadeQ'], 2, "0", STR_PAD_LEFT);
                
                if ($stUnidOrcaQuantitativo == $arOrgao['stUnidadeOrcamentaria']) {
                    $stJs .= excluirQuantitativo($arQuantitativo['inId']);
                }
            }
        }
    }

    $arOrgaos = $arTemp;

    Sessao::write('arOrgaosRemovido', $arOrgaosRemovido);
    Sessao::write('arOrgaos', $arOrgaos);

    $stJs .= montaListaOrgaos();
    $stJs .= preencheComboOrgaoAbaQuantitativo();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarOrgao()
{
    $arOrgaos = Sessao::read('arOrgaos');
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    $boOrgaoQuantitativo = false;
    
    foreach ($arOrgaos as $arOrgao) {
        if ($arOrgao['inId'] == $_REQUEST['inId']) {
            foreach ($arOrgaoItemQuantitativos as $arQuantitativo) {
                $stUnidOrcaQuantitativo = str_pad($arQuantitativo['inCodOrgaoQ'], 2, "0", STR_PAD_LEFT).'.'.str_pad($arQuantitativo['inCodUnidadeQ'], 2, "0", STR_PAD_LEFT);
                
                if ($stUnidOrcaQuantitativo == $arOrgao['stUnidadeOrcamentaria']) {
                    $boOrgaoQuantitativo = true;
                }
            }
            
            $inNaturezaSelecionada = ($arOrgao['inNaturezaProcedimento'] == 't' OR $arOrgao['inNaturezaProcedimento'] == 1) ? 1 : 2;
            $inGerenciadorSelecionado = ($arOrgao['inOrgaoGerenciador'] == 't' OR $arOrgao['inOrgaoGerenciador'] == 1) ? 1 : 2;
            
            $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                \n";
            $stJs .= "jQuery('#btnSalvarOrgao').val('Alterar Orgão');                                           \n";
            $stJs .= "jQuery('#btnSalvarOrgao').attr('onclick', 'montaParametrosGET(\'alterarListaOrgaos\');'); \n";
            # Preenche informações do Lote
            $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').removeAttr('readonly');                       \n";
            $stJs .= "jQuery('#inMontaCodOrgaoM').removeAttr('disabled');                                       \n";
            $stJs .= "jQuery('#inMontaCodUnidadeM').removeAttr('disabled');                                     \n";
            $stJs .= "jQuery('#stExercicioOrgao').val('".$arOrgao['stExercicioOrgao']."');                      \n";
            $stJs .= "jQuery('#stExercicioOrgao').removeAttr('readonly');                                       \n";
            $stJs .= "jQuery('#inHndIdOrgao').val('".$arOrgao['inId']."');                                      \n";
            $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').focus();                                      \n";
            $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').val('".$arOrgao['stUnidadeOrcamentaria']."'); \n";
            if($boOrgaoQuantitativo){
                $stJs .= "jQuery('input[name=stUnidadeOrcamentaria]').attr('readonly', 'readonly');             \n";
                $stJs .= "jQuery('#inMontaCodOrgaoM').attr('disabled', 'disabled');                             \n";
                $stJs .= "jQuery('#inMontaCodUnidadeM').attr('disabled', 'disabled');                           \n";
                $stJs .= "jQuery('#stExercicioOrgao').attr('readonly', 'readonly');                             \n";
            }
            $stJs .= "jQuery('#dtPublicacaoAvisoIntencao').focus();                                             \n";
            $stJs .= "jQuery('#inOrgaoGerenciador".$inGerenciadorSelecionado."').attr(\"checked\",true);        \n";
            
            if ( $inGerenciadorSelecionado == 1 ) {
                $stJs .= "jQuery('#inNaturezaProcedimento2').attr('disabled', 'disabled');                      \n";
            }
            
            $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"".$inNaturezaSelecionada."\"]').attr(\"checked\",true); \n";
            $stJs .= "jQuery('#stCodigoProcessoAdesao').val('".$arOrgao['stCodigoProcessoAdesao']."');          \n";
            $stJs .= "jQuery('#dtAdesao').val('".$arOrgao['dtAdesao']."');                                      \n";
            $stJs .= "jQuery('#dtPublicacaoAvisoIntencao').val('".$arOrgao['dtPublicacaoAvisoIntencao']."');    \n";
            
            break;
        }
    }
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarListaOrgaos()
{
    $obErro  = new Erro();
    $arOrgaos = Sessao::read('arOrgaos');
    if (is_array($arOrgaos) && count($arOrgaos) > 0) {
        foreach($arOrgaos as $arOrgao) {
            if ($arOrgao['inId'] != $_REQUEST['inHndIdOrgao']) {
                $arComparaUnidadeOrcamentaria = explode('.',$_REQUEST['stUnidadeOrcamentaria']);
                if ( $arOrgao['stExercicioOrgao'] == $_REQUEST['stExercicioOrgao'] && $arOrgao['inMontaCodOrgaoM'] == $arComparaUnidadeOrcamentaria[0] && $arOrgao['inMontaCodUnidadeM'] == $arComparaUnidadeOrcamentaria[1]) {
                    $obErro->setDescricao('Não é permitido adicionar Orgão e Unidade iguais para o mesmo exercicio.');
                }
                if ( $_REQUEST['inOrgaoGerenciador'] == 1 ) {
                    if ( $arOrgao['inOrgaoGerenciador'] == $_REQUEST['inOrgaoGerenciador'] ) {
                        $obErro->setDescricao('Não é permitido adicionar mais do que um gerenciador para o Registro de Preço.');
                    }
                }
            }
        }
    }

    if ( !$obErro->ocorreu() ) {
        foreach ($arOrgaos as $key => $arOrgao) {
            if ($arOrgao['inId'] == $_REQUEST['inHndIdOrgao']) {
                
                $arUnidadeOrcamentaria = explode('.',$_REQUEST['stUnidadeOrcamentaria']);
                $arOrgaos[$key]['stExercicioOrgao']          = $_REQUEST['stExercicioOrgao'];
                $arOrgaos[$key]['stUnidadeOrcamentaria']     = $_REQUEST['stUnidadeOrcamentaria'];
                $arOrgaos[$key]['stMontaCodOrgaoM']          = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$_REQUEST['stExercicioOrgao']."' AND num_orgao = ".(int)$arUnidadeOrcamentaria[0]);
                $arOrgaos[$key]['stMontaCodUnidadeM']        = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$_REQUEST['stExercicioOrgao']."' AND num_orgao = ".(int)$arUnidadeOrcamentaria[0]." AND num_unidade = ".(int)$arUnidadeOrcamentaria[1]);
                $arOrgaos[$key]['inMontaCodOrgaoM']          = (int) $arUnidadeOrcamentaria[0];
                $arOrgaos[$key]['inMontaCodUnidadeM']        = (int) $arUnidadeOrcamentaria[1];
                $arOrgaos[$key]['inNaturezaProcedimento']    = $_REQUEST['inNaturezaProcedimento'];
                $arOrgaos[$key]['inOrgaoGerenciador']        = $_REQUEST['inOrgaoGerenciador'];
                $arOrgaos[$key]['stOrgaoGerenciador']        = ($_REQUEST['inOrgaoGerenciador'] == 1) ? "Sim":"Não";
                $arOrgaos[$key]['stNaturezaProcedimento']    = ($_REQUEST['inNaturezaProcedimento'] == 1) ? "Órgão Participante":"Órgão Não Participante";
                $arOrgaos[$key]['stCodigoProcessoAdesao']    = $_REQUEST['stCodigoProcessoAdesao'];
                $arOrgaos[$key]['dtAdesao']                  = $_REQUEST['dtAdesao'];
                $arOrgaos[$key]['dtPublicacaoAvisoIntencao'] = $_REQUEST['dtPublicacaoAvisoIntencao'];

                break;
            }
        }
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        Sessao::write('arOrgaos', $arOrgaos);
        $stJs .= limparFormOrgaos();
        $stJs .= preencheComboOrgaoAbaQuantitativo();
    }

    return $stJs;
}


function preencheComboOrgaoAbaQuantitativo()
{
    $arOrgaos = Sessao::read('arOrgaos');
    $arOrgaosTMP = array();
    $inContador = 0;
    
    $stExercicioOrgaoQ = (isset($_REQUEST['stExercicioOrgaoQ'])) ? $_REQUEST['stExercicioOrgaoQ'] : Sessao::getExercicio();

    $stJs .= "f.inCodOrgaoQ.options.length = 0;                                      \n";
    $stJs .= "f.inCodOrgaoQ.options[".$inContador++."] = new Option('Selecione',''); \n";
    
    foreach( $arOrgaos as $arOrgao) {
        if($stExercicioOrgaoQ==$arOrgao['stExercicioOrgao']){
            $arUnidadeOrcamentaria = explode('.',$arOrgao['stUnidadeOrcamentaria']);
            $arOrgaosTMP[(int)$arUnidadeOrcamentaria[0]] = (int)$arUnidadeOrcamentaria[0]." - ".$arOrgao['stMontaCodOrgaoM'];
        }
    }
    foreach( $arOrgaosTMP as $key => $arOrgao) {
        $stJs .= "f.inCodOrgaoQ.options[".$inContador++."] = new Option('".$arOrgao."','".$key."'); \n";    
    }
    
    $stJs .= "if(f.inCodUnidadeQ){                                              \n";
    $stJs .= "      f.inCodUnidadeQ.options.length = 0;                         \n";
    $stJs .= "      f.inCodUnidadeQ.options[0] = new Option('Selecione','');    \n";
    $stJs .= "}                                                                 \n";

    return $stJs;
}

function preencheComboUnidadeAbaQuantitativo($inCodUnidadeQ = "")
{
    $arOrgaos = Sessao::read('arOrgaos');
    $arOrgaosTMP = array();
    $inContador = 0;
    
    $obSlcUnidade = new Select();
    $obSlcUnidade->setRotulo("Unidade");
    $obSlcUnidade->setId("inCodUnidadeQ");
    $obSlcUnidade->setName("inCodUnidadeQ");
    $obSlcUnidade->addOption("","Selecione");

    foreach( $arOrgaos as $arOrgao) {
        $stSelecionado = "";
        $arUnidadeOrcamentaria = explode('.',$arOrgao['stUnidadeOrcamentaria']);
        if ( (int)$arUnidadeOrcamentaria[0] == $_REQUEST['inCodOrgaoQ'] ) {
            if ( $inCodUnidadeQ == (int)$arUnidadeOrcamentaria[1] )
                $stSelecionado = "selected";

            $obSlcUnidade->addOption((int)$arUnidadeOrcamentaria[1],(int)$arUnidadeOrcamentaria[1]." - ".$arOrgao['stMontaCodUnidadeM'], $stSelecionado);
        }
    }
    
    $obFormulario = new Formulario();
    $obFormulario->addComponente( $obSlcUnidade );
    
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stJs .= "jQuery('#spnCodUnidadeQ').html('".$stHtml."'); ";    

    return $stJs;
}

function preencheLoteOuNumItemAbaQuantitativo($boLote = false)
{
    $arItens = Sessao::read('arItens');
    $arItensTMP = array();
    $arLote = array();
    $inContador = 0;

    if ( $boLote )
    {
        foreach ($arItens as $arItem) {
            $arLote[$arItem['stCodigoLote']] = $arItem['stCodigoLote'].' - '.$arItem['txtDescricaoLote'];
        }    

        $stJs .= "f.inCodLoteQ.options.length = 0;                     \n";
        $stJs .= "f.inCodLoteQ.options[".$inContador++."] = new Option('Selecione','');\n";
        
        foreach( $arLote as $key => $value) {
            $stJs .= "f.inCodLoteQ.options[".$inContador++."] = new Option('".$value."','".$key."'); \n";
        }
        
        $_REQUEST['inCodLoteQ'] = NULL;
    }

    $stJs .= preencheNumItemAbaQuantitativo($boLote);

    return $stJs;
}

function preencheNumItemAbaQuantitativo($boValidaCodLote = false)
{
    $arItens = Sessao::read('arItens');
    $arItensTMP = array();
    $inContador = 0;

    $stJs .= "f.inCodFornecedorQ.options.length = 0;\n";
    $stJs .= "f.inCodFornecedorQ.options[0] = new Option('Selecione',''); \n";
    $stJs .= "f.inNumItemQ.options.length = 0;\n";
    $stJs .= "f.inNumItemQ.options[".$inContador++."] = new Option('Selecione',''); \n";
    foreach( $arItens as $arItem) {
        if ( $boValidaCodLote ) {
            if ( isset($_REQUEST['inCodLoteQ']) && $_REQUEST['inCodLoteQ'] == $arItem['stCodigoLote'] ){
                $arItensTMP[$arItem['inNumItemLote']] = $arItem['inNumItemLote']." - ". $arItem['stNomItem'];
            }
        } else {
            $arItensTMP[$arItem['inNumItemLote']] = $arItem['inNumItemLote']." - ". $arItem['stNomItem'];
        }
    }

    foreach( $arItensTMP as $key => $arItem) {
        $stJs .= "f.inNumItemQ.options[".$inContador++."] = new Option('".$arItem."',".$key."); \n"; 
    }
    
    $stJs .= "f.nuHdnQtdeFornecida.value = '0,0000'; \n";
    $stJs .= "jQuery('#nuQtdeFornecida').html('0,0000');\n";

    return $stJs;
}

function montaLoteAbaQuantitativo()
{
    $obSlcLote = new Select();
    $obSlcLote->setRotulo("Lote");
    $obSlcLote->setId("inCodLoteQ");
    $obSlcLote->setName("inCodLoteQ");
    $obSlcLote->addOption("","Selecione");
    $obSlcLote->obEvento->setOnChange("montaParametrosGET('preencheNumItemAbaQuantitativo','inCodLoteQ');");
    
    $obFormulario1 = new Formulario();
    $obFormulario1->addComponente( $obSlcLote );

    $obFormulario1->montaInnerHTML();
    $stHtml .= $obFormulario1->getHTML();

    $stJs .= "jQuery('#spnLoteQuantitativo').html('".$stHtml."');\n";
    
    return $stJs;
}

function retornaNatureza()
{
    $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery; \n";
    $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"2\"]').removeAttr('disabled'); \n";
    $stJs .= "jQuery('input:radio[name=\"inNaturezaProcedimento\"][value=\"1\"]').attr('checked',false); \n";
    
    return $stJs;
}

function preencheComboFornecedorAbaQuantitativo()
{
    $arItens = Sessao::read('arItens');
    $arItensTMP = array();
    $inContador = 0;
    $inCloLoteV = (isset($_REQUEST['inCodLoteQ']) && $_REQUEST['inCodLoteQ'] != '') ? $_REQUEST['inCodLoteQ'] : 0;

    $stJs .= "f.inCodFornecedorQ.options.length = 0;\n";
    $stJs .= "f.inCodFornecedorQ.options[".$inContador++."] = new Option('Selecione',''); \n";

    foreach( $arItens as $arItem) {
        if ( $arItem["inNumItemLote"] == $_REQUEST['inNumItemQ'] && $arItem["stCodigoLote"] == $inCloLoteV) {
            $stJs .= "f.inHdnCodItemQ.value = ".$arItem["inCodItem"].";\n";
            $stJs .= "f.inCodFornecedorQ.options[".$inContador++."] = new Option('".$arItem["inNumCGMVencedor"]." - ".$arItem['stNomCGMVencedor']."','".$arItem["inNumCGMVencedor"]."'); \n"; 
        }
    }
    
    $stJs .= "f.nuHdnQtdeFornecida.value = '0,0000'; \n";
    $stJs .= "jQuery('#nuQtdeFornecida').html('0,0000');\n";

    return $stJs;
}

function preencheSpanQuantidadeFornecidaAbaQuantitativo()
{
    $arItens = Sessao::read('arItens');
    $arItensTMP = array();
    $inContador = 0;
    
    $stJs .= "f.nuHdnQtdeFornecida.value = '0,0000'; \n";
    $stJs .= "jQuery('#nuQtdeFornecida').html('0,0000');\n";
    
    foreach( $arItens as $arItem) {
        if ( $arItem["inNumItemLote"] == $_REQUEST['inNumItemQ'] && $arItem["inNumCGMVencedor"] == $_REQUEST['inCodFornecedorQ']) {
            $stJs .= "f.nuHdnQtdeFornecida.value = ".$arItem['nuQuantidade']."; \n";
            $stJs .= "jQuery('#nuQtdeFornecida').html('".$arItem['nuQuantidade']."');\n"; 
        }
    }
    return $stJs;
}

function incluirListaQuantitativo()
{

    $obErro  = new Erro();
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    $arOrgaoItemQuantitativosRemovido = Sessao::read('arOrgaoItemQuantitativosRemovido');
    $arItens = Sessao::read('arItens');
    $inIdMax=0;

    if ($_REQUEST['inCodOrgaoQ'] != "" &&
        $_REQUEST['inCodUnidadeQ'] != "" &&
        $_REQUEST['inNumItemQ'] != "" &&
        $_REQUEST['inCodFornecedorQ'] != "" &&
        $_REQUEST['stExercicioOrgaoQ'] != "" &&
        $_REQUEST['nuQtdeOrgao'] > '0,0000') {
        
        foreach( $arOrgaoItemQuantitativos as $arQuantitativo) {
            $inIdMax = ($arQuantitativo['inId']>$inIdMax||$arQuantitativo['inId']==$inIdMax) ? $arQuantitativo['inId']+1 : $inIdMax;
        }
        if (is_array($arOrgaoItemQuantitativosRemovido) && count($arOrgaoItemQuantitativosRemovido) > 0) {
            foreach( $arOrgaoItemQuantitativosRemovido as $arQuantitativoRemovido) {
                $inIdMax = ($arQuantitativoRemovido['inId']>$inIdMax||$arQuantitativoRemovido['inId']==$inIdMax) ? $arQuantitativoRemovido['inId']+1 : $inIdMax;
            }
        }
        
        $arOrgaoItemQuantitativo['stExercicioOrgao'] = $_REQUEST['stExercicioOrgaoQ'];
        $arOrgaoItemQuantitativo['inCodOrgaoQ']      = $_REQUEST['inCodOrgaoQ'];
        $arOrgaoItemQuantitativo['stNomOrgaoQ']      = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$_REQUEST['stExercicioOrgaoQ']."' AND num_orgao = ".$_REQUEST['inCodOrgaoQ']);
        $arOrgaoItemQuantitativo['inCodUnidadeQ']    = $_REQUEST['inCodUnidadeQ'];
        $arOrgaoItemQuantitativo['stNomUnidadeQ']    = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$_REQUEST['stExercicioOrgaoQ']."' AND num_orgao = ".$_REQUEST['inCodOrgaoQ']." AND num_unidade = ".$_REQUEST['inCodUnidadeQ']);
        $arOrgaoItemQuantitativo['inCodLoteQ']       = (!empty($_REQUEST['inCodLoteQ']) ? $_REQUEST['inCodLoteQ'] : 0);
        $arOrgaoItemQuantitativo['inNumItemQ']       = $_REQUEST['inNumItemQ'];
        $arOrgaoItemQuantitativo['inCodItemQ']       = $_REQUEST['inHdnCodItemQ'];

        foreach( $arItens as $arItem) {
            if ( $arItem["inCodItem"] == $_REQUEST['inHdnCodItemQ'] )
                $_REQUEST['stNomItem'] = $arItem["stNomItem"];
        }

        $arOrgaoItemQuantitativo['stNomItemQ']       = $_REQUEST['stNomItem'];
        $arOrgaoItemQuantitativo['inCodFornecedorQ'] = $_REQUEST['inCodFornecedorQ'];
        $arOrgaoItemQuantitativo['stNomFornecedorQ'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inCodFornecedorQ']);
        $arOrgaoItemQuantitativo['nuQtdeOrgao']      = $_REQUEST['nuQtdeOrgao'];
        $arOrgaoItemQuantitativo['inId']             = $inIdMax;
       
        if ($arOrgaoItemQuantitativos != "") {
            foreach ($arOrgaoItemQuantitativos as $arOrgaoItemQuanti) {
                if ($arOrgaoItemQuanti['stExercicioOrgao'] == $arOrgaoItemQuantitativo['stExercicioOrgao'] &&
                    $arOrgaoItemQuanti['inCodOrgaoQ']      == $arOrgaoItemQuantitativo['inCodOrgaoQ'] &&
                    $arOrgaoItemQuanti['inCodUnidadeQ']    == $arOrgaoItemQuantitativo['inCodUnidadeQ'] &&
                    $arOrgaoItemQuanti['inCodLoteQ']       == $arOrgaoItemQuantitativo['inCodLoteQ'] &&
                    $arOrgaoItemQuanti['inNumItemQ']       == $arOrgaoItemQuantitativo['inNumItemQ'] &&
                    $arOrgaoItemQuanti['inCodFornecedorQ'] == $arOrgaoItemQuantitativo['inCodFornecedorQ'] ) {
                    
                    $obErro->setDescricao("Estes valores informados já estão na lista!");
                    
                    break;
                }
            }
        }

    } else {
       $obErro->setDescricao("Informe Todos os campos!");
    }
    
    if ( (float)number_format($_REQUEST['nuHdnQtdeFornecida'], 4, ',', '.') < (float)number_format($_REQUEST['nuQtdeOrgao'], 4, ',', '.') ) {
        $obErro->setDescricao("A quantidade informada não pode ser maior do que a quantidade fornecida!");
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arOrgaoItemQuantitativos[] = $arOrgaoItemQuantitativo;

        Sessao::write('arOrgaoItemQuantitativos', $arOrgaoItemQuantitativos);
        $stJs .= montaListaOrgaoItemQuantitativos();
        $stJs .= limparFormOrgaoItemQuantitativos();
        $stJs .= preencheComboOrgaoAbaQuantitativo();
        $stJs .= preencheLoteOuNumItemAbaQuantitativo();
    }
    
    return $stJs;
}

function montaListaOrgaoItemQuantitativos()
{
        $rsRecordSet = new RecordSet();
    
    if (Sessao::read('arOrgaoItemQuantitativos') != "") {
        $rsRecordSet->preenche(Sessao::read('arOrgaoItemQuantitativos'));
    }

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Lista de Quantitativo por Orgão" );

    $obLista->setRecordSet( $rsRecordSet );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Exercício Orgão Unidade");
    $obLista->ultimoCabecalho->setWidth( 6 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Orgão");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Unidade");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Lote");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Número Item");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Item");
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->ultimoCabecalho->setWidth( 8 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Quantidade");
    $obLista->ultimoCabecalho->setWidth( 7 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stExercicioOrgao" );
    $obLista->ultimoDado->setAlinhamento('CENTER');
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodOrgaoQ] - [stNomOrgaoQ]" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodUnidadeQ] - [stNomUnidadeQ]" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inCodLoteQ" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNumItemQ" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();
    
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodItemQ] - [stNomItemQ]" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[inCodFornecedorQ] - [stNomFornecedorQ]" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuQtdeOrgao" );
    $obLista->ultimoDado->setAlinhamento('CENTER' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarQuantitativo');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
    
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirQuantitativo');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
    
    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stJs .= "d.getElementById('spnQuantitativoOrgao').innerHTML = '".$stHtml."';\n";
    
    return $stJs;
}

function limparFormOrgaoItemQuantitativos()
{
    $stJs .= "jQuery('#stExercicioOrgaoQ').val('".Sessao::getExercicio()."');   \n";
    $_REQUEST['stExercicioOrgaoQ'] = Sessao::getExercicio();
    $stJs .= preencheComboOrgaoAbaQuantitativo();    
    $stJs .= "if(f.inCodUnidadeQ){                                              \n";
    $stJs .= "      f.inCodUnidadeQ.options.length = 0;                         \n";
    $stJs .= "      f.inCodUnidadeQ.options[0] = new Option('Selecione','');    \n";
    $stJs .= "}                                                                 \n";
    $stJs .= "if(f.inCodLoteQ){                                                 \n";
    $stJs .= "      jQuery('#inCodLoteQ').val('');                              \n";
    $stJs .= "      f.inNumItemQ.options.length = 0;                            \n";
    $stJs .= "      f.inNumItemQ.options[0] = new Option('Selecione','');       \n";
    $stJs .= "}                                                                 \n";
    $stJs .= "jQuery('#inNumItemQ').val('');                                    \n";
    $stJs .= "f.inCodFornecedorQ.options.length = 0;                            \n";
    $stJs .= "f.inCodFornecedorQ[0] = new Option('Selecione','');               \n";
    $stJs .= "jQuery('#nuQtdeFornecida').html('0,0000');                        \n";
    $stJs .= "f.nuQtdeOrgao.value = '0,0000';                                   \n";
    $stJs .= "f.nuHdnQtdeFornecida.value = '0,0000';                            \n";
    $stJs .= "f.inHndIdItemQ.value = '';                                        \n";
    $stJs .= "jQuery('#btnSalvarQuantitativo').val('Incluir Quantitativo');     \n";
    $stJs .= "jQuery('#btnSalvarQuantitativo').attr('onclick', 'montaParametrosGET(\'incluirListaQuantitativo\')'); \n";
    

    return $stJs;
}

function carregaLicitacao()
{
    $stExercicioLicitacao = $_REQUEST['stExercicioProcessoLicitacao'];
    $inCodEntidade = $_REQUEST['inCodEntidade'];
    $inCodModalidade = $_REQUEST['inCodModalidadeLicitacao'];
    $stNroProcessoLicitacao = (isset($_REQUEST['stNroProcessoLicitacao'])) ? $_REQUEST['stNroProcessoLicitacao'] : NULL;
    if($stNroProcessoLicitacao!=NULL){
        $arProcessoLicitacao = explode('_', $stNroProcessoLicitacao);
        $inCodModalidade = $arProcessoLicitacao[1];
    }

    if($inCodModalidade==1)
        $inCodModalidade = 3;
    elseif($inCodModalidade==2)
        $inCodModalidade = '6,7';
    
    if($stExercicioLicitacao!=NULL&&$inCodEntidade!=NULL&&$inCodModalidade!=NULL){
        $stJs  = "f.stNroProcessoLicitacao.length = 0;\n";
        $stJs .= "f.stNroProcessoLicitacao.options[0] = new Option('Selecione',''); \n";

        include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNumeroLicitacao.class.php";
        
        $obTTCEMGNumeroLicitacao = new TTCEMGNumeroLicitacao();
        $obTTCEMGNumeroLicitacao->setDado( 'exercicio' , $stExercicioLicitacao );
        $obTTCEMGNumeroLicitacao->setDado( 'cod_entidade', $inCodEntidade );
        $obTTCEMGNumeroLicitacao->setDado( 'cod_modalidade', $inCodModalidade );

        $obTTCEMGNumeroLicitacao->recuperaNumeroLicitacaoHomologado( $rsLicitacao );
        $x = 1;
        while ( !$rsLicitacao->eof() ) {
            $stComparaValor = $rsLicitacao->getCampo('cod_licitacao')."_".$rsLicitacao->getCampo('cod_modalidade');
            if($stComparaValor == $stNroProcessoLicitacao)
                $selected = $stNroProcessoLicitacao;
            
            $stJs .= "f.stNroProcessoLicitacao.options[".$x++."] = new Option('".$rsLicitacao->getCampo('num_licitacao')."/".$rsLicitacao->getCampo('exercicio_licitacao')."','".$rsLicitacao->getCampo('cod_licitacao')."_".$rsLicitacao->getCampo('cod_modalidade')."');\n";
            
            $rsLicitacao->proximo();
        }
        
        $stJs .= "f.stNroProcessoLicitacao.value = '".$selected."';\n";
    }
    
    return $stJs;
}

function excluirQuantitativo($inId = '')
{
    $arTemp = array();
    $inId = ($inId=='') ? $_REQUEST['inId'] : $inId;

    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    $arOrgaoItemQuantitativosRemovido = Sessao::read('arOrgaoItemQuantitativosRemovido');

    foreach ($arOrgaoItemQuantitativos as $arQuantitativo) {
        if ($arQuantitativo['inId'] != $inId) {
            $arTemp[] = $arQuantitativo;
        } else {
            $arOrgaoItemQuantitativosRemovido[] = $arQuantitativo;
        }
    }

    $arOrgaoItemQuantitativos = $arTemp;

    Sessao::write('arOrgaoItemQuantitativosRemovido', $arOrgaoItemQuantitativosRemovido);
    Sessao::write('arOrgaoItemQuantitativos', $arOrgaoItemQuantitativos);

    $stJs .= montaListaOrgaoItemQuantitativos();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function alterarQuantitativo()
{
    $arQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    foreach ($arQuantitativos as $arQuantitativo) {
        if ($arQuantitativo['inId'] == $_REQUEST['inId']) {
            $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;                                            \n";
            $stJs .= "jQuery('#btnSalvarQuantitativo').val('Alterar Quantitativo');                                         \n";
            $stJs .= "jQuery('#btnSalvarQuantitativo').attr('onclick', 'montaParametrosGET(\'alterarListaQuantitativo\');');\n";

            # Preenche informações do Lote  inHdnCodItemQ
            $stJs .= "jQuery('#inHndIdItemQ').val('".$arQuantitativo['inId']."');                                           \n";
            $stJs .= "jQuery('#stExercicioOrgaoQ').val('".$arQuantitativo['stExercicioOrgao']."');                          \n";
            $_REQUEST['stExercicioOrgaoQ'] = $arQuantitativo['stExercicioOrgao'];
            $stJs .= preencheComboOrgaoAbaQuantitativo();   
            $stJs .= "jQuery('#inCodOrgaoQ').focus();                                                                       \n";
            $stJs .= "jQuery('#inCodOrgaoQ').val('".$arQuantitativo['inCodOrgaoQ']."');                                     \n";
            $_REQUEST['inCodOrgaoQ'] = $arQuantitativo['inCodOrgaoQ'];

            $stJs .= preencheComboUnidadeAbaQuantitativo($arQuantitativo['inCodUnidadeQ']);

            $stJs .= "jQuery('#inNumItemQ').val('".$arQuantitativo['inNumItemQ']."');                                       \n";
            $stJs .= "jQuery('#inHdnCodItemQ').val('".$arQuantitativo['inCodItemQ']."');                                    \n";
            $_REQUEST['inNumItemQ'] = $arQuantitativo['inNumItemQ'];

            $stJs .= preencheComboFornecedorAbaQuantitativo();
            $stJs .= "jQuery('#inCodFornecedorQ').val('".$arQuantitativo['inCodFornecedorQ']."');                           \n";
            $stJs .= "jQuery('#inCodFornecedorQ').change();                                                                 \n";
            $stJs .= "jQuery('#nuQtdeOrgao').focus();                                                                       \n";
            $stJs .= "jQuery('#nuQtdeOrgao').val('".str_replace('.', ',',$arQuantitativo['nuQtdeOrgao'])."');               \n";
            

            break;
        }
    }

    SistemaLegado::executaFrameOculto($stJs);
}

function alterarListaQuantitativo()
{
    $obErro  = new Erro();
    $arOrgaoItemQuantitativos = Sessao::read('arOrgaoItemQuantitativos');
    $arItens = Sessao::read('arItens');
    $qtdSomaItem = 0;

    if ($_REQUEST['inCodOrgaoQ'] != "" && $_REQUEST['inCodUnidadeQ'] != "" && $_REQUEST['inNumItemQ'] != ""  && $_REQUEST['inCodFornecedorQ'] != "" && $_REQUEST['stExercicioOrgaoQ'] != "" && $_REQUEST['nuQtdeOrgao'] > '0,0000') {
        foreach ($arOrgaoItemQuantitativos as $key => $value) {
            if ($value['inId'] != $_REQUEST['inHndIdItemQ']) {
                if ($_REQUEST['inCodOrgaoQ']                == $value['inCodOrgaoQ'] &&
                    $_REQUEST['inCodUnidadeQ']              == $value['inCodUnidadeQ'] &&
                    $_REQUEST['inNumItemQ']                 == $value['inNumItemQ'] &&
                    $_REQUEST['inCodFornecedorQ']           == $value['inCodFornecedorQ'] &&
                    $_REQUEST['inCodLoteQ']                 == $value['inCodLoteQ'] &&
                    $arOrgaoItemQuanti['stExercicioOrgao']  == $arOrgaoItemQuantitativo['stExercicioOrgao'] ) {
                    
                    $obErro->setDescricao("Estes valores informados já estão na lista!");
                    
                    break;
                }
            }
            if($value['inCodItemQ'] == $_REQUEST['inHdnCodItemQ'] && $value['inId'] != $_REQUEST['inHndIdItemQ']){
                $qtdSomaItem = $qtdSomaItem + $value['nuQtdeOrgao'];
            }
        }

        if ( !$obErro->ocorreu() ) {
            foreach ($arOrgaoItemQuantitativos as $key => $value) {
                if ($value['inId'] == $_REQUEST['inHndIdItemQ']) {
                    $arOrgaoItemQuantitativos[$key]['stExercicioOrgao'] = $_REQUEST['stExercicioOrgaoQ'];
                    $arOrgaoItemQuantitativos[$key]['inCodOrgaoQ']      = $_REQUEST['inCodOrgaoQ'];
                    $arOrgaoItemQuantitativos[$key]['stNomOrgaoQ']      = SistemaLegado::pegaDado("nom_orgao", "orcamento.orgao", "WHERE exercicio ='".$_REQUEST['stExercicioOrgaoQ']."' AND num_orgao = ".$_REQUEST['inCodOrgaoQ']);
                    $arOrgaoItemQuantitativos[$key]['inCodUnidadeQ']    = $_REQUEST['inCodUnidadeQ'];
                    $arOrgaoItemQuantitativos[$key]['stNomUnidadeQ']    = SistemaLegado::pegaDado("nom_unidade", "orcamento.unidade", "WHERE exercicio ='".$_REQUEST['stExercicioOrgaoQ']."' AND num_orgao = ".$_REQUEST['inCodOrgaoQ']." AND num_unidade = ".$_REQUEST['inCodUnidadeQ']);
                    $arOrgaoItemQuantitativos[$key]['inCodLoteQ']       = (!empty($_REQUEST['inCodLoteQ']) ? $_REQUEST['inCodLoteQ'] : 0);
                    $arOrgaoItemQuantitativos[$key]['inNumItemQ']       = $_REQUEST['inNumItemQ'];
                    $arOrgaoItemQuantitativos[$key]['inCodItemQ']       = $_REQUEST['inHdnCodItemQ'];
                    
                    $qtdSomaItem = $qtdSomaItem + $_REQUEST['nuQtdeOrgao'];
                    foreach( $arItens as $arItem) {
                        if ( $arItem["inCodItem"] == $_REQUEST['inHdnCodItemQ'] ){
                            $_REQUEST['stNomItem'] = $arItem["stNomItem"];
                            if ( (float)number_format($arItem['nuQuantidade'], 4, ',', '.') < (float)number_format($qtdSomaItem, 4, ',', '.') ) {
                                $obErro->setDescricao("A quantidade informada não pode ser maior do que a quantidade fornecida!");
                                break;
                            }
                        }
                    }
            
                    $arOrgaoItemQuantitativos[$key]['stNomItemQ']       = $_REQUEST['stNomItem'];
                    $arOrgaoItemQuantitativos[$key]['inCodFornecedorQ'] = $_REQUEST['inCodFornecedorQ'];
                    $arOrgaoItemQuantitativos[$key]['stNomFornecedorQ'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inCodFornecedorQ']);
                    $arOrgaoItemQuantitativos[$key]['nuQtdeOrgao']      = $_REQUEST['nuQtdeOrgao'];

                    break;
                }
            }
        }
    } else {
       $obErro->setDescricao("Informe Todos os campos!");
    }
    
    if ( (float)number_format($_REQUEST['nuHdnQtdeFornecida'], 4, ',', '.') < (float)number_format($_REQUEST['nuQtdeOrgao'], 4, ',', '.') ) {
        $obErro->setDescricao("A quantidade informada não pode ser maior do que a quantidade fornecida!");
    }
    
    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        Sessao::write('arOrgaoItemQuantitativos', $arOrgaoItemQuantitativos);
        $stJs .= montaListaOrgaoItemQuantitativos();
        $stJs .= limparFormOrgaoItemQuantitativos();
        $stJs .= preencheComboOrgaoAbaQuantitativo();
        $stJs .= preencheLoteOuNumItemAbaQuantitativo();
    }
    
    return $stJs;
}

if ( array_key_exists('stCtrl',$_REQUEST)){
    $stCtrl = $_REQUEST['stCtrl'];
} else if (array_key_exists('stCtrl',$_POST)) {
    $stCtrl = $_POST['stCtrl'];
} else {
    $stCtrl = $_REQUEST['stCtrl'];
}
switch ($stCtrl)
{
    case 'validaNroProcesso':
        $stJs .= validaNroProcesso($request->get('stNumProcesso'), $request->get('inCodEntidade'));
    break;
    case 'validaNroAdesao':
        $stJs .= validaNroAdesao($request->get('stCodigoProcessoAdesao'), $request->get('inCodEntidade'));
    break;
    case 'validaNroItemNoLote':
        $stJs .= validaNroItemNoLote($request->get('inCodLote'), $request->get('stNumItemLote'));
    break;
    case 'incluirListaItens':
        $stJs .= incluirListaItens();
    break;

    case 'alterarListaItens':
        $stJs .= alterarListaItens();
    break;    
    
    case "alterarItem":
        $stJs .= alterarItem();
    break;

    case "excluirItem":
        $stJs .= excluirItem();
    break;

    case 'limparFormItem':
        $stJs .= limparFormItem();
    break;

    case 'montaFormLote':
        $stJs .= montaFormLote();
    break;

    case "buscaOrgaoGerenciador":
        echo buscaOrgaoGerenciador();
    break;

    case "buscaCGMResponsavel":
        echo buscaCGMResponsavel();
    break;

    case "buscaCGMVencedor":
        echo buscaCGMVencedor();
    break;

    case "buscaDescricaoLote":
        echo buscaDescricaoLote($_REQUEST['inCodLote']);
    break;

    case "incluirEmpenho":
        $stJs .= incluirEmpenho();
        break;

    case "excluirEmpenho":
        $stJs .= excluirEmpenho();
        break;
    
    case "preencheInner":

        $numEmpenho = $_REQUEST['numEmpenho'];
    
        if ($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho'] and $numEmpenho) {
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado('cod_empenho'  , $numEmpenho );
            $obTEmpenhoEmpenho->setDado('exercicio'    , $_REQUEST['stExercicioEmpenho']);
            $obTEmpenhoEmpenho->setDado('cod_entidade' , $_REQUEST['inCodEntidade']);
            
            # Busca somente os Empenhos da modalidade Registro De Preços
            $obTEmpenhoEmpenho->setDado('registro_precos', true);
            $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenho($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';
    
            } else {
                $stJs  = "alertaAviso('Empenho não cadastrado ou não pertence a Modalidade Registro de Preços','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
            }
        } else {
            if (!$_REQUEST['inCodEntidade']) {
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
            }
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
            }
            if (!$numEmpenho) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
            }
            $stJs .= "f.numEmpenho.value = '';";
        }
    
        echo $stJs;
        
        break;
    
    case "incluirListaOrgaos":
        $stJs .= incluirListaOrgaos();
        break;
    case "limparFormOrgaos":
        $stJs .= limparFormOrgaos();
        break;
    case "alterarOrgao":
        $stJs .= alterarOrgao();
        break;
    case 'alterarListaOrgaos':
        $stJs .= alterarListaOrgaos();
        break;
    case "excluirOrgao":
        $stJs .= excluirOrgao();
        break;
    case "excluirQuantitativo":
        $stJs .= excluirQuantitativo();
        break;
    case "alterarQuantitativo":
        $stJs .= alterarQuantitativo();
        break;
    case "preencheComboOrgaoAbaQuantitativo":
        $stJs .= preencheComboOrgaoAbaQuantitativo();
        break;
    case "preencheComboUnidadeAbaQuantitativo":
        $stJs .= preencheComboUnidadeAbaQuantitativo();
        break;
    case "preencheComboFornecedorAbaQuantitativo":
        $stJs .= preencheComboFornecedorAbaQuantitativo();
        break;
    case "preencheNatureza":
        $stJs .= preencheNatureza();
        break;
    case "retornaNatureza":
        $stJs .= retornaNatureza();
        break;
    case "preencheSpanQuantidadeFornecidaAbaQuantitativo":
        $stJs .= preencheSpanQuantidadeFornecidaAbaQuantitativo();
        break;
    case "incluirListaQuantitativo":
        $stJs .= incluirListaQuantitativo();
        break;
    case "limparFormOrgaoItemQuantitativos":
        $stJs .= limparFormOrgaoItemQuantitativos();
        break;
    case "preencheNumItemAbaQuantitativo":
        $boLote = (isset($_REQUEST['inCodLoteQ'])) ? TRUE : FALSE;
        $stJs .= preencheNumItemAbaQuantitativo($boLote);
        break;
    case "carregaLicitacao":
        $stJs .= carregaLicitacao();
        break;
    case 'alterarListaQuantitativo':
        $stJs .= alterarListaQuantitativo();
        break;
}

if (isset($stJs)) {
   echo $stJs;
}

?>