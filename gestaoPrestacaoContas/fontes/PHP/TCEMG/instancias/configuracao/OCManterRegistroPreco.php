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
  * $Id: OCManterRegistroPreco.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
  * $Author: gelson $
  * $Rev: 59612 $
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGProcessoAdesaoRegistroPrecos.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";

function validaNroProcesso($stCodProcesso, $inCodEntidade) {

    $stCodProcessoAdesao = explode('/', $stCodProcesso);
    
    $rsProcessoAdesao = new RecordSet();
    $obTTCEMGProcessoAdesaoRegistroPrecos = new TTCEMGProcessoAdesaoRegistroPrecos();
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cod_entidade'           , $inCodEntidade);
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('numero_processo_adesao' , (int)$stCodProcessoAdesao[0]);
    $obTTCEMGProcessoAdesaoRegistroPrecos->setDado('exercicio_adesao'       , Sessao::getExercicio());
    $obTTCEMGProcessoAdesaoRegistroPrecos->recuperaPorChave($rsProcessoAdesao);
    
    if ( $rsProcessoAdesao->getNumLinhas() < 0 ) {
        if ( array_key_exists("1", $stCodProcessoAdesao) ) {
            $stCodProcessoAdesaoTMP = str_pad($stCodProcessoAdesao[0], 12, "0", STR_PAD_LEFT) .'/'.$stCodProcessoAdesao[1];
        } else {
            $stCodProcessoAdesaoTMP = str_pad($stCodProcessoAdesao[0], 12, "0", STR_PAD_LEFT) .'/'.Sessao::getExercicio();
        }

        $stJs = "jQuery('#stCodigoProcesso').val('".$stCodProcessoAdesaoTMP."'); ";
    } else {
        $stJs  = "jQuery('#stCodigoProcesso').val(''); ";
        $stJs .= "alertaAviso('Já existe um número de processo igual para a mesma entidade no mesmo exerício (".str_pad($stCodProcessoAdesao[0], 7, "0", STR_PAD_LEFT) .'/'.Sessao::getExercicio().")','form','erro','".Sessao::getId()."');\n";
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

    $stJs .= "jQuery('#spnLote').html('".$stHtml."'); ";    
    $stJs .= "jQuery('#nuPercentualItem').attr('disabled', 'disabled'); ";
    $stJs .= " if (jQuery('#inDescontoTabela:checked').val() == 2) { jQuery('#nuPercentualLote').attr('disabled', 'disabled'); } "; 
    
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
                $_REQUEST['stCodigoLote'] != $item['stCodigoLote']) {

                $stJs .= "alertaAviso('Não é permitido a mesma descrição para lotes diferentes no mesmo processo','form','erro','".Sessao::getId()."');\n";
                return $stJs;
            }
        }
    }
    
    # Validação para não permitir cadastrar lote = 0 (zero)
    if (isset($_REQUEST['stCodigoLote']) && $_REQUEST['stCodigoLote'] == 0) {
        $obErro->setDescricao("Código de Lote não pode ser igual a 0.");
        return "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    if ($_REQUEST['dtCotacao'] != "" &&
        $_REQUEST['inCodItem'] != "" &&
        $_REQUEST['nuVlReferencia'] > '0,0000' &&
        $_REQUEST['nuQuantidade'] > '0,0000' &&
        $_REQUEST['nuVlTotal'] > '0,0000' &&
        $_REQUEST['stNomItem'] != "" &&
        $_REQUEST['inNumCGMVencedor'] != "") {
        
        $arItens = Sessao::read('arItens');

        $arItensCotacao = array();
        $arItensCotacao['inCodItem']        = $_REQUEST['inCodItem'];
        $arItensCotacao['stNomItem']        = $_REQUEST['stNomItem'];
        $arItensCotacao['stNomUnidade']     = $_REQUEST['stNomUnidade'];
        $arItensCotacao['nuVlReferencia']   = number_format($_REQUEST['nuVlReferencia'], 4, ',', '.');
        $arItensCotacao['nuQuantidade']     = number_format($_REQUEST['nuQuantidade'], 4, ',', '.');
        $arItensCotacao['nuVlTotal']        = number_format($_REQUEST['nuVlTotal'], 4, ',', '.');
        $arItensCotacao['dtCotacao']        = $_REQUEST['dtCotacao'];
        $arItensCotacao['stCodigoLote']     = (!empty($_REQUEST['stCodigoLote']) ? $_REQUEST['stCodigoLote'] : 0);
        $arItensCotacao['nuPercentualLote'] = $_REQUEST['nuPercentualLote'];
        $arItensCotacao['txtDescricaoLote'] = $_REQUEST['txtDescricaoLote'];
        $arItensCotacao['nuVlUnitario']     = number_format($_REQUEST['nuVlUnitario'], 4, ',', '.');
        $arItensCotacao['nuQtdeLicitada']   = number_format($_REQUEST['nuQtdeLicitada'], 4, ',', '.');
        $arItensCotacao['nuQtdeAderida']    = number_format($_REQUEST['nuQtdeAderida'], 4, ',', '.');
        $arItensCotacao['nuPercentualItem'] = $_REQUEST['nuPercentualItem'];
        $arItensCotacao['inNumCGMVencedor'] = $_REQUEST['inNumCGMVencedor'];
        $arItensCotacao['inId']             = count($arItens);
        $arItensCotacao['stNomCGMVencedor'] = SistemaLegado::pegaDado("nom_cgm", "sw_cgm", "where numcgm = ".$_REQUEST['inNumCGMVencedor']);
       
        if ($arItens != "") {
            foreach ($arItens as $arrItem) {
                if ($arrItem['inCodItem'] == $arItensCotacao['inCodItem']) {
                    $obErro->setDescricao("Este Item já está na lista!");
                }
            }
        }

    } else {
       $obErro->setDescricao("Informe Todos os campos!");
    }

    if ($obErro->ocorreu()) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    } else {
        $arItens[] = $arItensCotacao;
        Sessao::write('arItens', $arItens);
        $stJs .= montaListaItens();
        $stJs .= limparFormItem();
    }
    
    return $stJs;
}

function alterarListaItens()
{
    $obErro  = new Erro();

    $arItens = Sessao::read('arItens');
   
    foreach ($arItens as $key => $arItem) {
        
        if ($arItem['inCodItem'] == $_REQUEST['inCodItem']) {

            $arItens[$key]['nuVlReferencia']   = $_REQUEST['nuVlReferencia'];
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
    
    $stJs .= limparFormItem();
    $stJs .= montaListaItens();

    SistemaLegado::executaFrameOculto($stJs);
}

function montaListaItens()
{
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
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nro. Lote" );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
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
    $obLista->ultimoCabecalho->addConteudo( "CGM Vencedor" );
    $obLista->ultimoCabecalho->setWidth( 8 );
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
    $obLista->ultimoDado->setCampo( "inCodItem" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomItem" );
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
    $obLista->ultimoDado->setCampo( "stNomCGMVencedor" );
    $obLista->ultimoDado->setAlinhamento('CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('alterarItem');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();
  
    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirItem');" );
    $obLista->ultimaAcao->addCampo("1","inId");
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
    
    foreach ($arItens as $arItem) {
        
        if ($arItem['inId'] == $_GET['inId']) {

            $stJs .= "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

            $stJs .= "jQuery('#btnSalvar').val('Alterar Item'); \n";
            $stJs .= "jQuery('#btnSalvar').attr('onclick', 'return alterarItem();'); \n";

            # Preenche informações do Lote
            $stJs .= "jQuery('#stCodigoLote').val('".$arItem['stCodigoLote']."'); \n";
            $stJs .= "jQuery('#txtDescricaoLote').val('".$arItem['txtDescricaoLote']."'); \n";
            $stJs .= "jQuery('#nuPercentualLote').val('".$arItem['nuPercentualLote']."'); \n";
            
            $stJs .= "jQuery('#dtCotacao').val('".$arItem['dtCotacao']."'); \n";
            $stJs .= "jQuery('input[name=inCodItem]').val('".$arItem['inCodItem']."'); \n";
            $stJs .= "jQuery('input[name=inCodItem]').attr('readonly', 'readonly'); \n";
            $stJs .= "jQuery('#imgBuscar').css('visibility', 'hidden'); \n";
            $stJs .= "jQuery('#stNomItem').html('".$arItem['stNomItem']."'); \n";
            $stJs .= "jQuery('#stUnidadeMedida').html('".$arItem['stNomUnidade']."'); \n";
            $stJs .= "jQuery('#nuVlReferencia').val('".$arItem['nuVlReferencia']."'); \n";
            $stJs .= "jQuery('#nuQuantidade').val('".$arItem['nuQuantidade']."'); \n";
            $stJs .= "jQuery('#nuVlTotal').val('".$arItem['nuVlTotal']."'); \n";
            $stJs .= "jQuery('#nuVlUnitario').val('".$arItem['nuVlUnitario']."'); \n";
            $stJs .= "jQuery('#nuQtdeLicitada').val('".$arItem['nuQtdeLicitada']."'); \n";
            $stJs .= "jQuery('#nuQtdeAderida').val('".$arItem['nuQtdeAderida']."'); \n";
            $stJs .= "jQuery('#nuPercentualItem').val('".$arItem['nuPercentualItem']."'); \n";
            $stJs .= "jQuery('#inNumCGMVencedor').val('".$arItem['inNumCGMVencedor']."'); \n";
            $stJs .= "jQuery('#inNomCGMVencedor').html('".$arItem['stNomCGMVencedor']."'); \n";
            $stJs .= "jQuery('#dtCotacao').focus(); \n";

            break;
        }
    }
    
    SistemaLegado::executaFrameOculto($stJs);
    
}

function excluirItem()
{
    $arTemp = $arTempRemovido = array();

    $arItens = Sessao::read('arItens');
    $arItensRemovidos = Sessao::read('arItensRemovido');

    foreach ($arItens as $arItem) {
        if ($arItem['inId'] != $_GET['inId']) {
            $arTemp[] = $arItem;
        } else {
            $arTempRemovido[] = $arItem;
        }
    }

    $arItens = $arTemp;
    $arItensRemovidos .= $arTempRemovido;

    Sessao::write('arItensRemovido', $arTempRemovido);
    Sessao::write('arItens', $arItens);

    $stJs .= montaListaItens();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function limparFormItem()
{
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

    $stJs .= "jQuery('#stCodigoLote').val('');           \n";
    $stJs .= "jQuery('#txtDescricaoLote').val('');       \n";
    $stJs .= "jQuery('#nuPercentualLote').val('');       \n";
    $stJs .= "jQuery('#dtCotacao').val('');              \n";
    $stJs .= "jQuery('input[name=inCodItem]').val('');   \n";
    $stJs .= "jQuery('#stNomItem').html('&nbsp;');       \n";
    $stJs .= "jQuery('#stUnidadeMedida').html('&nbsp;'); \n";
    $stJs .= "jQuery('#nuVlReferencia').val('0,0000');   \n";
    $stJs .= "jQuery('#nuQuantidade').val('0,0000');     \n";
    $stJs .= "jQuery('#nuVlTotal').val('0,0000');        \n";
    $stJs .= "jQuery('#nuVlUnitario').val('0,0000');     \n";
    $stJs .= "jQuery('#nuQtdeLicitada').val('0,0000');   \n";
    $stJs .= "jQuery('#nuQtdeAderida').val('0,0000');    \n";
    $stJs .= "jQuery('#nuPercentualItem').val('0');      \n";
    $stJs .= "jQuery('#inNumCGMVencedor').val('');       \n";
    $stJs .= "jQuery('#inNomCGMVencedor').html('');      \n";
    $stJs .= "jQuery('#dtCotacao').focus();              \n";

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
            $arEmpenhoNovo['inId']           = count($arEmpenhos);
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
    $arTemp = $arTempRemovido = array();

    $arEmpenhos = Sessao::read('arEmpenhos');
    $arEmpenhosRemovidos = Sessao::read('arEmpenhosRemovidos');

    foreach ($arEmpenhos as $arEmpenho) {
        if ($arEmpenho['inId'] != $_GET['inId']) {
            $arTemp[] = $arEmpenho;
        } else {
            $arTempRemovido[] = $arEmpenho;
        }
    }

    $arEmpenhos = $arTemp;
    $arEmpenhosRemovidos[] = $arTempRemovido;

    Sessao::write('arEmpenhosRemovidos', $arEmpenhosRemovidos);
    Sessao::write('arEmpenhos', $arEmpenhos);

    $stJs .= montaListaEmpenho();
    
    SistemaLegado::executaFrameOculto($stJs);
}

function limparFormEmpenho()
{
    $stJs  = "var jQuery = window.parent.frames['telaPrincipal'].jQuery;";

    $stJs .= "jQuery('#numEmpenho').val('');           \n";
    $stJs .= "jQuery('#stEmpenho').html('&nbsp;');       \n";
    
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


switch ($request->get('stCtrl'))
{
    case 'validaNroProcesso':
        $stJs .= validaNroProcesso($request->get('stNumProcesso'), $request->get('inCodEntidade'));
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
}

if (isset($stJs)) {
   echo $stJs;
}

?>