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
    * Classe Oculta de Autorização
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @ignore

    $Id: OCManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.02
                    uc-02.01.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterAutorizacao';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgPror = 'PO'.$stPrograma.'.php';

$stCtrl = $_REQUEST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());

function montaLista($arRecordSet, $boExecuta = true)
{
    for($i=0;$i<count($arRecordSet);$i++){
        if(isset($arRecordSet[$i]['cod_item'])&&$arRecordSet[$i]['cod_item']!='')
            $codItem = true;
        break;
    }

    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao('vl_total', 'NUMERIC_BR');

    if (!$rsLista->eof()) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao(false);
        $obLista->setRecordSet($rsLista);
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('&nbsp;');
        $obLista->ultimoCabecalho->setWidth(5);
        $obLista->commitCabecalho();
        if ($codItem) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('Código ');
            $obLista->ultimoCabecalho->setWidth(10);
            $obLista->commitCabecalho(); 
        }
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Descrição ');
        $obLista->ultimoCabecalho->setWidth(50);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Unitário ');
        $obLista->ultimoCabecalho->setWidth(15);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Quantidade ');
        $obLista->ultimoCabecalho->setWidth(10);
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo('Valor Total');
        $obLista->ultimoCabecalho->setWidth(15);
        $obLista->commitCabecalho();

        if ($_REQUEST['stAcao'] != 'anular' && $_REQUEST['hdnBoModuloEmpenho'] != true) {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth(5);
            $obLista->commitCabecalho();
        }
        if ($codItem) {
            $obLista->addDado();
            $obLista->ultimoDado->setCampo('cod_item');
            $obLista->ultimoDado->setAlinhamento('ESQUERDA');
            $obLista->commitDado();
        }
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('nom_item');
        $obLista->ultimoDado->setAlinhamento('ESQUERDA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_unitario');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('quantidade');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo('vl_total');
        $obLista->ultimoDado->setAlinhamento('DIREITA');
        $obLista->commitDado();

        if ($_REQUEST['stAcao'] != 'anular' && $_REQUEST['hdnBoModuloEmpenho'] != true) {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao      ('ALTERAR');
            $obLista->ultimaAcao->setFuncaoAjax(true);
            $obLista->ultimaAcao->setLink      ("JavaScript:alterarEmpenho('alterarItemPreEmpenho');");
            $obLista->ultimaAcao->addCampo     ('1', 'num_item');
            if ($codItem) {
                $obLista->ultimaAcao->addCampo('2', 'cod_item');    
            }
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao  ('EXCLUIR');
            $obLista->ultimaAcao->setFuncao(true);
            $obLista->ultimaAcao->setLink  ("JavaScript:excluirItem('excluirItemPreEmpenho');");
            $obLista->ultimaAcao->addCampo ('1', 'num_item');
            $obLista->commitAcao();
        }

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotalAutorizacao = $nuVlTotal;
        $nuVlTotal = number_format($nuVlTotal, 2, ',', '.');
        
        $nuVlReserva = $nuVlTotal ? $nuVlTotal : 0;
        
        $stLista    = "jq_('#spnLista').html('".$stHTML."'); ";
        $stLista   .= "jq_('#Ok').attr('disabled',false); ";
        if ($codItem) {
            $stLista .= "jq_('#inCodItem').val(''); ";
            $stLista .= "jq_('#stNomItemCatalogo').html('&nbsp;'); ";
            $stLista .= "jq_('#stUnidadeMedida').html('&nbsp;'); ";
        }else{
            $stLista .= "jq_('#stNomItem').html('&nbsp;'); ";
        }
        $stVlTotal  = "jq_('#nuValorTotal').html('".$nuVlTotal."'); ";
        $stVlTotal .= "jq_('#nuVlTotalAutorizacao').val('".$nuVlTotalAutorizacao."'); ";
        $stVlTotal .= "jq_('#nuVlReserva').html('".$nuVlReserva."'); ";
        $stVlTotal .= "jq_('#hdnVlReserva').val('".$nuVlReserva."'); ";
        
    } else {
        $stLista    = "jq_('#spnLista').html(''); ";
        $stLista   .= "jq_('#Ok').attr('disabled',false); ";
        $stVlTotal .= "jq_('#nuValorTotal').html('&nbsp;'); ";
        $stVlTotal .= "jq_('#nuVlTotalAutorizacao').val(''); ";
        $stVlTotal .= "if (jq_('#nuVlReserva')) jq_('#nuVlReserva').html('&nbsp;');";
        $stVlTotal .= "jq_('#hdnVlReserva').val(''); ";
        Sessao::remove('arItens');
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stLista.$stVlTotal);
    } else {
        return $stLista.$stVlTotal;
    }
}

function montaCombo($stNomDespesa)
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($_REQUEST['inCodDespesa'] != '' and $stNomDespesa) {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_POST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);
        $stCodClassificacao = $rsConta->getCampo('cod_estrutural');
        
        if (Sessao::read('inCodClassificacao') != ''){
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setClassificacao(Sessao::read('inCodClassificacao'));
        } else {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        }
        
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);
        
        if (Sessao::read('inCodClassificacao') != ''){
            $js .= "jq_('#stCodClassificacao_label').html('".$rsClassificacao->getCampo('cod_estrutural')." - ".$rsClassificacao->getCampo('descricao')."');\n";
        } else if ($rsClassificacao->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo('mascara_reduzida');
                if ($stMascaraReduzidaOld) {
                    if ($stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld))) {
                        $selected = '';
                      if ($stCodEstruturalOld == $_POST['stCodEstrutural']) {
                          $selected = 'selected';
                          $stCodEstrutural = $stCodEstruturalOld;
                      }
                        $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                        $js .= "f.stCodClassificacao.options[$inContador] = new Option( $stOption ); \n";
                        $inContador++;
                    }
                    $js .= "f.stCodClassificacao.value = '".$stCodEstrutural."'; \n";
                }
                $inCodContaOld        = $rsClassificacao->getCampo('cod_conta');
                $stCodEstruturalOld   = $rsClassificacao->getCampo('cod_estrutural');
                $stDescricaoOld       = $rsClassificacao->getCampo('descricao');
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = '';
                $rsClassificacao->proximo();
            }
            if ($stMascaraReduzidaOld) {
                if ($stCodEstruturalOld == $_POST['stCodEstrutural']) {
                    $selected = 'selected';
                }
                $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                $js .= "f.stCodClassificacao.options[$inContador] = new Option( $stOption ); \n";
            }
        } else {
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.stCodClassificacao,0); \n";
        $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
    }

    return $js;
}

function montaLabel($flSaldoDotacao)
{
    $obHdnSaldoDotacao = new Hidden;
    $obHdnSaldoDotacao->setName ('flVlSaldoDotacao');
    $obHdnSaldoDotacao->setValue($flSaldoDotacao);

    $flSaldoDotacao = number_format($flSaldoDotacao , 2, ',', '.');

    $obHdnSaldo = new Hidden;
    $obHdnSaldo->setName ('flVlSaldo');
    $obHdnSaldo->setValue($flSaldoDotacao);

    $obLblSaldo = new Label;
    $obLblSaldo->setRotulo('Saldo da Dotação');
    $obLblSaldo->setValue ($flSaldoDotacao);

    $obFormulario = new Formulario;
    $obFormulario->addHidden     ($obHdnSaldo);
    $obFormulario->addHidden     ($obHdnSaldoDotacao);
    $obFormulario->addComponente ($obLblSaldo);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    
    $js1 = "jq_('#spnSaldoDotacao').html('".$stHtml."');";

    return $js1;
}

function montaSpanReserva()
{
    $stDtValidadeFinal = '31/12/'.Sessao::getExercicio();
    // Define Objeto Label para Valor da Reserva
    $obLblReserva = new Label;
    $obLblReserva->setId    ('nuVlReserva');
    $obLblReserva->setRotulo('Valor da reserva: ');
    $obLblReserva->setValue (number_format((integer) $_REQUEST['nuVlTotalAutorizacao'], 2, ',', '.'));

    // Define objeto Data para validade final
    $obDtValidadeFinal = new Data;
    $obDtValidadeFinal->setName  ('stDtValidadeFinal');
    $obDtValidadeFinal->setValue ($stDtValidadeFinal);
    $obDtValidadeFinal->setRotulo('Data Validade Final');
    $obDtValidadeFinal->setTitle ('');
    $obDtValidadeFinal->setNull  (false);

    if (isset($_REQUEST['hdnBoModuloEmpenho']) && $_REQUEST['hdnBoModuloEmpenho'] == true) {
        $obDtValidadeFinal->setLabel(true);
    }

    $obFormulario = new Formulario;
    $obFormulario->addComponente ($obLblReserva);
    $obFormulario->addComponente ($obDtValidadeFinal);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    
    $js1 = "jq_('#spnReserva').html('".$stHtml."');";

    return $js1;
}

function buscaOrgaoUnidade()
{
    
    global $obREmpenhoAutorizacaoEmpenho;

    if ($_REQUEST['inCodOrgao'] != '') {
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
        
        if (Sessao::read('inCodUnidadeOrcamentaria') != ''){
             $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade(Sessao::read('inCodUnidadeOrcamentaria'));
        }
        
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade, 'ou.num_orgao, ou.num_unidade');
        
        if (Sessao::read('inCodUnidadeOrcamentaria') != ''){
            $js .= "jq_('#inCodUnidadeOrcamento_label').html('".$rsUnidade->getCampo('num_unidade')." - ".$rsUnidade->getCampo('nom_unidade')."');\n";
        } else if ($rsUnidade->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
            
            while (!$rsUnidade->eof()) {
                $inCodUnidade = $rsUnidade->getCampo('num_unidade');
                $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                $selected = '';
                
                if ($_REQUEST['hdnCodUnidade'] == $rsUnidade->getCampo('num_unidade')) {
                    $selected   = 'selected';
                    $numUnidade = $rsUnidade->getCampo('num_unidade');
                }
                
                $js .= "f.inCodUnidadeOrcamento.options[$inContador] = new
                Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";
                $inContador++;
                $rsUnidade->proximo();
            }
            
            $js .= "f.inCodUnidadeOrcamento.value = '".$numUnidade ."'; \n";
        } else {
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
        $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
        
    }
    return $js;
}

function montaOrgaoUnidade($entCodOrgao = '', $entCodUnidade = '', $entCodDespesa = ''){
    global $obREmpenhoAutorizacaoEmpenho;
    
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($entCodOrgao);
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao);
    
    $inContOrgao = 1;
    $inContUnidade = 1;
    
    if ($entCodOrgao != '' && $entCodUnidade != '') {
        $codOrgao = $entCodOrgao;
        $codUnidade = $entCodUnidade;
        
        $selected = '';
        
    } else {
        $selected = 'selected';
    }
    
    $js  = "limpaSelect(f.inCodOrgao,0); \n";
    $js .= "f.inCodOrgao.options[0] = new Option('Selecione','','".$selected."');\n";
    
    $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
    $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','','".$selected."');\n";
    
    while (!$rsOrgao->eof()){
        $inCodOrgao   = $rsOrgao->getCampo('num_orgao');
        $stNomOrgao   = $rsOrgao->getCampo('num_orgao').' - '.$rsOrgao->getCampo('nom_orgao');
        
        $js .= "f.inCodOrgao.options[$inContOrgao] = new Option('".$stNomOrgao."','".$inCodOrgao."','";
        
        $selected = ($codOrgao == $inCodOrgao) ? 'selected' : '';
        
        $js.= "".$selected."'); \n";
        
        if ($codOrgao == $inCodOrgao) {
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($codOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade);
            
            $js .= "f.hdnCodOrgao.value = ".$inCodOrgao."; \n";
            $js .= "f.inCodOrgao.value=".$inCodOrgao."; \n";
            
            while (!$rsUnidade->eof()){
                
                $inCodUnidade   = $rsUnidade->getCampo('num_unidade');
                $stNomUnidade   = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                
                $selected = ($codUnidade == $inCodUnidade) ? 'selected' : '';
                
                
                if($entCodDespesa==''){
                    $js .= "f.inCodUnidadeOrcamento.options[$inContUnidade] = new Option('".$stNomUnidade."','".$inCodUnidade."','";
                    $js .= "".$selected."'); \n";
                    
                    $inContUnidade++;
                }else{
                    if($codUnidade == $inCodUnidade){
                        $js .= "f.inCodUnidadeOrcamento.options[$inContUnidade] = new Option('".$stNomUnidade."','".$inCodUnidade."','";
                        $js .= "".$selected."'); \n";
                        
                        $inContUnidade++;
                    }
                }
                
                if($codUnidade == $inCodUnidade){
                    $js .= "f.inCodUnidadeOrcamento.value=".$inCodUnidade."; \n";
                    $js .= "f.hdnCodUnidade.value = ".$inCodUnidade."; \n";
                }
                
                $rsUnidade->proximo();
            }
        }
        
        $inContOrgao++;
        $rsOrgao->proximo();
    }
    
    return $js;
}

switch ($stCtrl) {
case 'buscaOrgaoUnidade':
    if ($_REQUEST['inCodOrgao'] != '') {
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade, 'ou.num_orgao, ou.num_unidade');
        
        if ($rsUnidade->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
            
            while (!$rsUnidade->eof()) {
                $inCodUnidade = $rsUnidade->getCampo('num_unidade');
                $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                $selected     = '';
                
                $js .= "f.inCodUnidadeOrcamento.options[$inContador] = new
                Option('".$stNomUnidade."','".$inCodUnidade."','".$selected."'); \n";
                
                $inContador++;
                $rsUnidade->proximo();
            }
        } else {
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
        }
    } else {
        $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
        $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
    }
    
    SistemaLegado::executaFrameOculto($js);
    break;

case 'alterar':
    $_REQUEST['inCodOrgao'] = $_GET['inCodOrgao'];
    $js .= montaCombo('true');
    $js .= buscaOrgaoUnidade();
    $js .= montaSpanReserva();
    if ($_REQUEST['inCodDespesa'] != '') {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);
        $nuSaldoDotacao = bcadd($nuSaldoDotacao, $_REQUEST['nuVlItemExcluidos'], 4);
        $js .= montaLabel($nuSaldoDotacao);
    }
    $js .= montaLista(Sessao::read('arItens'), false);
    SistemaLegado::executaFrameOculto("LiberaFrames(true,false);".$js);
    break;

case 'buscaDespesa':
    if ($_REQUEST['inCodDespesa'] != '' AND $_REQUEST['inCodEntidade'] != '') {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario($rsDespesa);
        $stNomDespesa = $rsDespesa->getCampo('descricao');

        if (!$stNomDespesa) {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesa($rsDespesa2);
            $stNomDespesa2 = $rsDespesa2->getCampo('descricao');
            if (!$stNomDespesa2) {
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'f.inCodDespesa.focus();';
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'f.inCodDespesa.focus();';
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Você não possui permissão para esta dotação. (".$_POST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stNomDespesa = $rsDespesa->getCampo('descricao');
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
        }
        
    } else {
        $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
    }

    if ($_REQUEST["inCodDespesa"] != '' AND $stNomDespesa) {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);
        
        if ($_REQUEST['inCodDespesa'] == $_REQUEST['inCodDespesaAux'] and $_REQUEST['stAcao'] == 'alterar') {
            $nuSaldoDotacao = bcadd($nuSaldoDotacao, $_REQUEST['nuVlItemExcluidos'], 4);
        }
        $js .= montaLabel($nuSaldoDotacao);
        
        $inNumOrgao   = $rsDespesa->getCampo('num_orgao');
        $inNumUnidade = $rsDespesa->getCampo('num_unidade');
        
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($inNumUnidade);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao);
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade);
        
        $inCodOrgao   = $rsUnidade->getCampo('num_orgao');
        $stNomOrgao   = $rsUnidade->getCampo('num_orgao').' - '.$rsOrgao->getCampo('nom_orgao');
        $inCodUnidade = $rsUnidade->getCampo('num_unidade');
        $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
        
        if (Sessao::read('inCodUnidadeOrcamentaria') != '') {
            $js .= montaOrgaoUnidade($inCodOrgao, $inCodUnidade);
        } else {
            $js .= "limpaSelect(f.inCodOrgao,0); \n";
            $js .= "f.inCodOrgao.options[0] = new Option('".$stNomOrgao ."','".$inCodOrgao ."', 'selected');\n";
            
            $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
            $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('".$stNomUnidade ."','".$inCodUnidade ."', 'selected');\n";
        }
        
        $js .= montaSpanReserva();
    } else {
        $js .= montaOrgaoUnidade($inNumOrgao, $_REQUEST['inCodUnidadeOrcamento']);
        
        $js .= "jq_('#spnSaldoDotacao').html('');";
        $js .= "jq_('#spnReserva').html('');";
    }
    
    $js .= montaCombo($stNomDespesa);
    
    SistemaLegado::executaFrameOculto($js);
    break;

case 'buscaClassificacao':
    if ($_POST["stCodClassificacao"] != '') {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($_POST['stCodClassificacao']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsClassificacao);
        $stNomClassificacao = $rsClassificacao->getCampo('descricao');
        if (!$stNomClassificacao) {
            $js .= 'f.stCodClassificacao.value = "";';
            $js .= 'f.stCodClassificacao.focus();';
            $js .= 'd.getElementById("stNomClassificacao").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["stCodClassificacao"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= "jq_('#stNomClassificacao').html('".$stNomClassificacao."';";
        }
    } else $js .= 'jq_("#stNomClassificacao").html("&nbsp;");';
    SistemaLegado::executaFrameOculto($js);
    break;

case 'buscaFornecedor':
    if ($_POST["inCodFornecedor"] != '') {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($_POST['inCodFornecedor']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->listar($rsCGM);
        $stNomFornecedor = $rsCGM->getCampo('nom_cgm');
        if (!$stNomFornecedor) {
            $js .= 'f.inCodFornecedor.value = "";';
            $js .= 'f.inCodFornecedor.focus();';
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCGM"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'jq_("#stNomFornecedor").html("'.$stNomFornecedor.'");';
        }
    } else {
        $js .= 'jq_("#stNomFornecedor").html("&nbsp;");';
    }
    SistemaLegado::executaFrameOculto($js);
    break;

case 'incluiItemPreEmpenho':
    $arItens = Sessao::read('arItens');
    $inCount = sizeof($arItens);
    $nuVlTotal = str_replace('.','',$_POST['nuVlTotal']);
    $nuVlTotal = str_replace(',','.',$nuVlTotal);
    if($_REQUEST['stTipoItem']=='Catalogo'){
        list($inCodUnidade, $inCodGrandeza) = explode("-",$_POST['inCodUnidadeMedida']);
        $stNomUnidade = $_POST['stNomUnidade'];
    }else{
        list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$_POST['inCodUnidade']);
    }
    $arItens[$inCount]['num_item']     = $inCount+1;
    if ($_REQUEST['stTipoItem']=='Catalogo') {
        foreach ($arItens as $key => $valor) {
            if ($valor['cod_item'] == $_POST['inCodItem']) {
                $erro=true;
            }
        }
        $arItens[$inCount]['cod_item']     = $_POST['inCodItem'];
        $arItens[$inCount]['nom_item']     = $_POST['stNomItemCatalogo'];
    }else{
        $arItens[$inCount]['nom_item']     = $_POST['stNomItem'];
    }
    $arItens[$inCount]['complemento']  = trim($_POST['stComplemento']);
    $arItens[$inCount]['quantidade']   = trim($_POST['nuQuantidade']);
    $arItens[$inCount]['vl_unitario']  = $_POST['nuVlUnitario'];
    $arItens[$inCount]['cod_unidade']  = $inCodUnidade;
    $arItens[$inCount]['cod_grandeza'] = $inCodGrandeza;
    $arItens[$inCount]['nom_unidade']  = $stNomUnidade;
    $arItens[$inCount]['vl_total']     = $nuVlTotal;
    $arItens[$inCount]['cod_material'] = trim($_POST['inCodMaterial']);
    if($erro){
        $stJs = "alertaAviso('Item(".$_POST['inCodItem'].") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
        $stJs .= "f.Ok.disabled = false; \n";
    }else{
        $stJs = montaLista( $arItens, false);
        Sessao::write('arItens', $arItens);
    }
    SistemaLegado::executaFrameOculto($stJs);
    break;

case 'alterarItemPreEmpenho':
    $arItens = array();
    $arItens = Sessao::read('arItens');

    foreach ($arItens as $valor) {
        if ($valor['num_item'] == $_REQUEST['num_item']) {

            $stUnidade = $valor['cod_unidade'].'-'.$valor['cod_grandeza'].'-'.$valor['nom_unidade'];
            $stJs .= "jq('#hdnNumItem').val('".$_REQUEST['num_item']."');";
            if ($_REQUEST['cod_item']) {
                $stJs .= "f.inCodItem.value= '".$valor['cod_item']."';";
                $stJs .= "f.HdninCodItem.value= '".$valor['cod_item']."';";
                $stJs .= "f.stNomItemCatalogo.value ='".$valor["nom_item"]."';";
                $stJs .= "d.getElementById('stNomItemCatalogo').innerHTML ='".$valor["nom_item"]."';";
                $stJs .= "f.inCodUnidadeMedida.value= '".$valor["cod_unidade"]."-". $valor["cod_grandeza"]."';";
                $stJs .= "f.stNomUnidade.value= '".$valor["nom_unidade"]."';";
            }else{
                $stJs .= "jq('#stNomItem').val('".htmlentities($valor["nom_item"], ENT_QUOTES)."');";
            }
            $stJs .= "jq('#stComplemento').val('".htmlentities($valor["complemento"], ENT_QUOTES)."');";
            $stJs .= "jq('#nuQuantidade').val('".number_format($valor['quantidade'],2,',','.')."');";
            $stJs .= "jq('#inCodUnidade').val('".$stUnidade."');";
            $stJs .= "jq('#nuVlUnitario').val('".$valor['vl_unitario']."');";
            $stJs .= "jq('#nuVlTotal').val('".number_format($valor['vl_total'],2,',','.')."');";
            $stJs .= "jq('#btnIncluir').val('Alterar');";
            $stJs .= "f.btnIncluir.setAttribute('onclick','return alterarItem()');";
            $stJs .= "jq('#stNomItem').val(f.stNomItem.value.unescapeHTML());";
            $stJs .= "jq('#stComplemento').val(f.stComplemento.value.unescapeHTML());";
        }
    }
    echo $stJs;
    break;

case 'alteradoItemPreEmpenho':
    $arItens = array();
    $arItens = Sessao::read('arItens');
    foreach ($arItens as $key => $valor) {
        if ($valor['num_item'] == $_REQUEST['hdnNumItem']) {
            for($i=0;$i<count($arItens);$i++){
                if($_REQUEST['stTipoItem']=='Catalogo'&&($arItens[$i]['cod_item'] == $_POST['inCodItem'])&&($arItens[$i]['num_item'] != $_REQUEST['hdnNumItem'])){
                    $erro=true;      
                }
            }
            
            if(!$erro){
                if($_REQUEST['stTipoItem']=='Catalogo'){
                    list($inCodUnidade, $inCodGrandeza) = explode("-",$_POST['inCodUnidadeMedida']);
                    $stNomUnidade = $_POST['stNomUnidade'];
                    $arItens[$key]['cod_item']    = $_POST['inCodItem'];
                    $arItens[$key]['nom_item']    = stripslashes($_REQUEST['stNomItemCatalogo']);
                }else{
                    list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$_POST['inCodUnidade']);
                    $arItens[$key]['nom_item'   ]  = stripslashes($_REQUEST['stNomItem']);
                }
                $arItens[$key]['complemento']  = stripslashes($_REQUEST['stComplemento']);
                $arItens[$key]['quantidade' ]  = $_REQUEST['nuQuantidade'];
                $arItens[$key]['cod_unidade']  = $inCodUnidade;
                $arItens[$key]['cod_grandeza'] = $inCodGrandeza;
                $arItens[$key]['nom_unidade']  = $stNomUnidade;
                $arItens[$key]['vl_unitario']  = $_REQUEST['nuVlUnitario'];
    
                $nuVlTotal = str_replace('.','',$_REQUEST['nuVlTotal']);
                $nuVlTotal = str_replace(',','.',$nuVlTotal);
    
                $arItens[$key]['vl_total'] = $nuVlTotal;
                break;
            }
        }
        else{
            if($_REQUEST['stTipoItem']=='Catalogo'&&($valor['cod_item'] == $_POST['inCodItem'])){
                $erro=true;    
            }
        }
    }
    
    if($erro){
        $js = "alertaAviso('Item(".$_POST['inCodItem'].") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
        $js .= "f.Ok.disabled = false; \n";
        $js .= "f.btnIncluir.setAttribute('onclick','return incluirItem()'); \n";
        SistemaLegado::executaFrameOculto($js);
    }else{
        Sessao::write('arItens', $arItens);
        $stJs = "f.btnIncluir.setAttribute('onclick','return incluirItem()');";
    
        echo montaLista(Sessao::read('arItens'));
        SistemaLegado::executaFrameOculto($stJs);
    }
    
    break;

case 'excluirItemPreEmpenho':
    $arTEMP = array();
    $inCount = 0;
    $arItens = Sessao::read('arItens');
    for($i=0;$i<count($arItens);$i++){
        if($arItens[$i]['num_item']!=$_GET['inNumItem']){
            $arTEMP[$inCount]['num_item']     = $inCount+1;
            
            if($_REQUEST['stTipoItem']=='Catalogo'){
                $arTEMP[$inCount]['cod_item']     = $arItens[$i]['cod_item'];
            }
            $arTEMP[$inCount]['nom_item']     = $arItens[$i]['nom_item'];
            $arTEMP[$inCount]['complemento']  = $arItens[$i]['complemento'];
            $arTEMP[$inCount]['quantidade']   = $arItens[$i]['quantidade'];
            $arTEMP[$inCount]['cod_unidade']  = $arItens[$i]['cod_unidade'];
            $arTEMP[$inCount]['nom_unidade']  = $arItens[$i]['nom_unidade'];
            $arTEMP[$inCount]['cod_grandeza'] = $arItens[$i]['cod_grandeza'];
            $arTEMP[$inCount]['vl_total']     = $arItens[$i]['vl_total'];
            $arTEMP[$inCount]['vl_unitario']  = $arItens[$i]['vl_unitario'];
            $arTEMP[$inCount]['cod_material'] = $arItens[$i]['cod_material'];
            $inCount++;
        }
    }
    
    $arItens = $arTEMP;
    $stJs .= montaLista($arItens, false);
    Sessao::write('arItens', $arItens);
    SistemaLegado::executaFrameOculto($stJs);
    if(count($arTEMP)==0){
        $js  = "d.getElementById('stTipoItemRadio1').disabled = false;";
        $js .= "d.getElementById('stTipoItemRadio2').disabled = false;";
        SistemaLegado::executaFrameOculto($js);
    }
    break;

case 'montaListaItemPreEmpenho':
    $js  = montaLista(Sessao::read('arItens'), false);
    $js .= montaCombo('');
    SistemaLegado::executaFrameOculto($js);
    break;

case 'montaListaItemPreEmpenhoAnular':
    $js  = montaLista(Sessao::read('arItens'), false);
    SistemaLegado::executaFrameOculto($js);
    break;

case 'limpar' :
    Sessao::remove('arItens');
    $stJs .= montaLista(Sessao::read('arItens'), false);
    SistemaLegado::executaFrameOculto($stJs);
    break;

case 'recuperaOrgao':
    $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao, $stOrder);
    $inContador = 1;
    $js .= "limpaSelect(f.inCodOrgao,0);";
    $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');";
    $js .= "limpaSelect(f.inCodUnidadeOrcamento,0);";
    while (!$rsOrgao->eof()) {
        $stOption = "'".$rsOrgao->getCampo("num_orgao").' - '.$rsOrgao->getCampo("nom_orgao")."','".$rsOrgao->getCampo("num_orgao")."','".$selected."'";
        $js .= "f.inCodOrgao.options[$inContador] = new Option( $stOption ); \n";
        $inContador++;
        $rsOrgao->proximo();
    }
    $js .= "limpaSelect(f.inCodUnidadeOrcamento,0);";
    $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');";

    SistemaLegado::executaFrameOculto($js);
    break;

case 'buscaDtAutorizacao':
    if ($_REQUEST["inCodEntidade"] != '') {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
        $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData($rsMaiorData);
        if (!$obErro->ocorreu()) {
            $stDtAutorizacao = $rsMaiorData->getCampo('data_autorizacao');
            if ($stDtAutorizacao) {
                $js .= 'f.stDtAutorizacao.value = "'.$stDtAutorizacao.'";';
                $js .= 'f.stDtAutorizacao.focus();';
            } else {
                $js .= 'f.stDtAutorizacao.value= "01/01/'.date("Y").'";';
            }
        }

        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio( Sessao::getExercicio() );
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario( $rsOrgao, $stOrder );
        $inContador = 1;
        $js .= "limpaSelect(f.inCodOrgao,0);";
        $js .= "f.inCodOrgao.options[0] = new Option('Selecione','', 'selected');";
        $js .= "limpaSelect(f.inCodUnidadeOrcamento,0);";
        $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');";
        while (!$rsOrgao->eof()) {
            $stOption = "'".$rsOrgao->getCampo('num_orgao').' - '.$rsOrgao->getCampo('nom_orgao')."','".$rsOrgao->getCampo('num_orgao')."','".$selected."'";
            $js .= "f.inCodOrgao.options[$inContador] = new Option( $stOption ); \n";
            $inContador++;
            $rsOrgao->proximo();
        }

    } else {
        $js .= 'f.stDtAutorizacao.value= "'.date("d/m/Y").'";';
    }

    $js .= "LiberaFrames(true,false);";

    echo $js;
    break;

case 'verificaFornecedor':
    if ($_REQUEST['inCodFornecedor'] && $_REQUEST['inCodContrapartida'] && ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3)) {
        $boPendente = false;
        if ($_REQUEST['stDtAutorizacao']) {
            include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$_REQUEST['inCodFornecedor']);
            $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$_REQUEST['inCodContrapartida']);
            $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);

            if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                while (!$rsVerificaEmpenho->eof()) {
                    if (SistemaLegado::comparaDatas($_REQUEST['stDtAutorizacao'],$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                           $boPendente = true;
                    }
                    $rsVerificaEmpenho->Proximo();
                }
            }
        }

        if ($boPendente) {
            echo " alertaAviso('@O responsável por adiantamento informado possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
        } else {
            echo " alertaAviso('','','','".Sessao::getId()."'); ";
        }
    }

    break;

case 'buscaContrapartida':
    if ($_REQUEST['inCodFornecedor'] && ($_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3)) {
        include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
        $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
        $obTEmpenhoResponsavelAdiantamento->setDado('exercicio', Sessao::getExercicio());
        $obTEmpenhoResponsavelAdiantamento->setDado('numcgm'   , $_REQUEST['inCodFornecedor']);
        $obTEmpenhoResponsavelAdiantamento->recuperaContrapartidaLancamento($rsContrapartida);
        $inCodContrapartida = $_REQUEST['hdnCodContrapartida'];

        if ($rsContrapartida->getNumLinhas() > 0) {
            $obCmbContrapartida = new Select;
            $obCmbContrapartida->setRotulo    ('Contrapartida');
            $obCmbContrapartida->setTitle     ('Informe a contrapartida.');
            $obCmbContrapartida->setName      ('inCodContrapartida');
            $obCmbContrapartida->setId        ('inCodContrapartida');
            $obCmbContrapartida->setNull      (false);
            $obCmbContrapartida->setValue     ($inCodContrapartida);
            $obCmbContrapartida->setStyle     ('width: 600');
            $obCmbContrapartida->addOption    ('', 'Selecione');
            $obCmbContrapartida->setCampoId   ('conta_contrapartida');
            $obCmbContrapartida->setCampoDesc ('[conta_contrapartida] - [nom_conta]');
            $obCmbContrapartida->preencheCombo($rsContrapartida);
            $obCmbContrapartida->obEvento->setOnChange("montaParametrosGET('verificaFornecedor');");

            $obFormulario = new Formulario;
            $obFormulario->addComponente($obCmbContrapartida);
            $obFormulario->montaInnerHTML();
            $stHtml = $obFormulario->getHTML();

            $js .= " d.getElementById('spnContrapartida').innerHTML = '".$stHtml."'; ";
        } else {
           $js .= "  f.inCodCategoria.options.selectedIndex = 0;
                     d.getElementById('spnContrapartida').innerHTML = '';
                     alertaAviso('@O responsável por adiantamento informado não está cadastrado ou está inativo.','form','erro','".Sessao::getId()."');";
        }
    } else {
        $js = " d.getElementById('spnContrapartida').innerHTML = ''; ";
    }

    echo $js;

    break;

case "unidadeItem":
        $js = "";
        $stJs = "";
        if( $_REQUEST["codItem"] ){
            $stFiltro=" WHERE cod_item=".$_REQUEST["codItem"];
            
            include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
            $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $_REQUEST["codItem"]);
            $obTAlmoxarifadoCatalogoItem->recuperaTodos($rsItem, $stFiltro);
            
            if($rsItem->inNumLinhas==1){
                $value = $rsItem->getCampo('cod_unidade')."-".$rsItem->getCampo('cod_grandeza');

                include_once ( CAM_GA_ADM_MAPEAMENTO."TUnidadeMedida.class.php"          );
                $obTUnidadeMedida = new TUnidadeMedida;
                
                $stFiltro=" WHERE cod_unidade=".$rsItem->getCampo('cod_unidade')." AND cod_grandeza=".$rsItem->getCampo('cod_grandeza');
                $obTUnidadeMedida->recuperaTodos($rsUnidade, $stFiltro);
                if($rsUnidade->inNumLinhas==1){
                    $value=$value."-".$rsUnidade->getCampo('nom_unidade');
                    
                    $js .= "for (var i = 0; i < f.inCodUnidade.options.length; i++)
                    {
                            if (f.inCodUnidade.options[i].value == '".$value."')
                            {
                                    f.inCodUnidade.options[i].selected = 'true';
                                    break;
                            }
                    }\n";
                }
                
            }
        }
        
        SistemaLegado::executaFrameOculto($js);
        echo $stJs;
    break;
case "montaOrgaoUnidade":
    $js = montaOrgaoUnidade($_REQUEST["hdnCodOrgao"], $_REQUEST["hdnCodUnidade"], $_REQUEST["inCodDespesa"]);

    echo $js;
break;
}
?>
