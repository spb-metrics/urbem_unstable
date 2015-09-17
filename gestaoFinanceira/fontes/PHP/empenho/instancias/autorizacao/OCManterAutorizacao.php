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

    $Id: OCManterAutorizacao.php 63604 2015-09-16 19:11:45Z jean $

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
$js = " ";

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

        $obLista->montaInnerHTML();
        $stHTML = $obLista->getHTML();

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotalAutorizacao = $nuVlTotal;
        $nuVlTotal = number_format($nuVlTotal, 2, ',', '.');
        
        $nuVlReserva = $nuVlTotal ? $nuVlTotal : 0;
        
        $js .= "jq('#spnLista').html('".$stHTML."'); \n";
        $js .= "jq('#Ok').attr('disabled',false); \n";
        if ($codItem) {
            $js .= "jq('#inCodItem').val(''); \n";
            $js .= "jq('#stNomItemCatalogo').html('&nbsp;'); \n";
            $js .= "jq('#stUnidadeMedida').html('&nbsp;'); \n";
        }else{
            $js .= "jq('#stNomItem').html('&nbsp;'); \n";
        }
        $js .= "jq('#nuValorTotal').html('".$nuVlTotal."'); \n";
        $js .= "jq('#nuVlTotalAutorizacao').val('".$nuVlTotalAutorizacao."'); \n";
        $js .= "jq('#nuVlReserva').html('".$nuVlReserva."'); \n";
        $js .= "jq('#hdnVlReserva').val('".$nuVlReserva."'); \n";
        
    } else {
        $js .= "jq('#spnLista').html('&nbsp;'); \n";
        $js .= "jq('#Ok').attr('disabled',false); \n";
        $js .= "jq('#nuValorTotal').html('&nbsp;'); \n";
        $js .= "jq('#nuVlTotalAutorizacao').val(''); \n";
        $js .= "if (jq('#nuVlReserva')) jq('#nuVlReserva').html('&nbsp;'); \n";
        $js .= "jq('#hdnVlReserva').val(''); \n";
        Sessao::remove('arItens');
    }

    return $js;
}

function montaCombo($stNomDespesa)
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($_REQUEST['inCodDespesa'] != '' and $stNomDespesa) {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);

        if (Sessao::read('inCodClassificacao') != ''){
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setClassificacao(Sessao::read('inCodClassificacao'));
        } else {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        }
        
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);
        
        if (Sessao::read('inCodClassificacao') != ''){
            $js .= "jq('#stCodClassificacao_label').html('".$rsClassificacao->getCampo('cod_estrutural')." - ".$rsClassificacao->getCampo('descricao')."');\n";
        } else if ($rsClassificacao->getNumLinhas() > -1) {
            $js .= "jq('#stCodClassificacao').empty().append(new Option('Selecione','')); \n";
            $js .= "jq('#stCodClassificacao').val('0'); \n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo('mascara_reduzida');
                if ($stMascaraReduzidaOld) {
                    if ($stCodEstruturalOld == $_REQUEST['stCodEstrutural']) {
                        $stCodEstrutural = $stCodEstruturalOld;
                    }
                    $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."'";
                    $js .= "jq('#stCodClassificacao').append(new Option(".$stOption.")); \n";
                }
                $inCodContaOld        = $rsClassificacao->getCampo('cod_conta');
                $stCodEstruturalOld   = $rsClassificacao->getCampo('cod_estrutural');
                $stDescricaoOld       = $rsClassificacao->getCampo('descricao');
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = '';
                $rsClassificacao->proximo();
            }

            if ($stCodEstrutural != '') {
                $js .= "jq('#stCodClassificacao').val('".$stCodEstrutural."');\n";
            }

            if ($stMascaraReduzidaOld) {
                $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."'";
                $js .= "jq('#stCodClassificacao').append(new Option(".$stOption.")); \n";

                if ($stCodEstruturalOld == $_REQUEST['stCodEstrutural']) {
                    $jq .= "jq('#stCodClassificacao').val('".$stCodEstruturalOld."');\n";
                }
            }
        } else {
            $js .= "jq('#stCodClassificacao').empty().append(new Option('Selecione','')); \n";
            $jq .= "jq('#stCodClassificacao').val('0');\n";
        }
    } else {
        $js .= "jq('#stCodClassificacao').empty().append(new Option('Selecione','')); \n";
        $jq .= "jq('#stCodClassificacao').val('0');\n";
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
    $stHtml = $obFormulario->getHtml();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("\"","'",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $js1 = "jq('#spnSaldoDotacao').html('".$stHtml."');\n";

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
    
    $js1 = "jq('#spnReserva').html('".$stHtml."');";

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

        $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n";
        $js .= "jq('#inCodUnidadeOrcamento').val('0');\n";

        if (Sessao::read('inCodUnidadeOrcamentaria') != ''){
            $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
            $inCodUnidade = $rsUnidade->getCampo('num_unidade');
            
            $js .= "jq('#inCodUnidadeOrcamento').append(new Option('".$stNomUnidade."','".$inCodUnidade."'));\n";
            $js .= "jq('#inCodUnidadeOrcamento').val('".$inCodUnidade."');\n";
        } else if ($rsUnidade->getNumLinhas() > 0) {
            while (!$rsUnidade->eof()) {
                $inCodUnidade = $rsUnidade->getCampo('num_unidade');
                $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                
                if ($_REQUEST['hdnCodUnidade'] == $rsUnidade->getCampo('num_unidade')) {
                    $numUnidade = $rsUnidade->getCampo('num_unidade');
                }
                
                $js .= "jq('#inCodUnidadeOrcamento').append(new Option('".$stNomUnidade."','".$inCodUnidade."'));\n";
                $rsUnidade->proximo();
            }
            $js .= "jq('#inCodUnidadeOrcamento').val('".$numUnidade."');\n";
        } else {
            $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n";
            $js .= "jq('#inCodUnidadeOrcamento').val('0');\n";
        }
    } else {
        $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n";
        $js .= "jq('#inCodUnidadeOrcamento').val('0');\n";
    }
    return $js;
}

function montaOrgaoUnidade($entCodOrgao = '', $entCodUnidade = '', $entCodDespesa = ''){
    global $obREmpenhoAutorizacaoEmpenho;

    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($entCodOrgao);
    $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao);
        
    if ($entCodOrgao != '' && $entCodUnidade != '') {
        $codOrgao = $entCodOrgao;
        $codUnidade = $entCodUnidade;
    } else {
        $boSelected = true;
    }

    $js .= "jq('#inCodOrgao').empty().append(new Option('Selecione','')); \n"; 
    $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione','')); \n";
    if ($boSelected) {
        $js .= "jq('#inCodOrgao').val('0');\n";
        $js .= "jq('#inCodUnidadeOrcamento').val('0');\n";
    }
    
    while (!$rsOrgao->eof()){
        $inCodOrgao   = $rsOrgao->getCampo('num_orgao');
        $stNomOrgao   = $rsOrgao->getCampo('num_orgao').' - '.$rsOrgao->getCampo('nom_orgao');

        $js .= " jq('#inCodOrgao').append(new Option('".trim($stNomOrgao)."','".$inCodOrgao."')); \n";

        if ($codOrgao == $inCodOrgao) {
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($codOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade);
            
            $js .= "jq('#hdnCodOrgao').val('".$inCodOrgao."');\n";
            
            while (!$rsUnidade->eof()){
                
                $inCodUnidade   = $rsUnidade->getCampo('num_unidade');
                $stNomUnidade   = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                
                if($entCodDespesa == ''){
                    $js .= " jq('#inCodUnidadeOrcamento').append(new Option('".$stNomUnidade."','".$inCodUnidade."')); \n"; 
                } else {
                    if($codUnidade == $inCodUnidade){
                        $js .= " jq('#inCodUnidadeOrcamento').append(new Option('".$stNomUnidade."','".$inCodUnidade."')); \n";
                    }
                }

                if($codUnidade == $inCodUnidade){
                    $js .= "jq('#hdnCodUnidade').val('".$inCodUnidade."');\n";
                }
                
                $rsUnidade->proximo();
            }
        }
        $rsOrgao->proximo();
    }

    $js .= "jq('#inCodOrgao').val('".$codOrgao."');\n";
    $js .= "jq('#inCodUnidadeOrcamento').val('".$codUnidade."');\n";
    
    return $js;
}

switch ($stCtrl) {
    case 'buscaOrgaoUnidade':

    $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n"; 

        if ($_REQUEST['inCodOrgao'] != '') {
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade, 'ou.num_orgao, ou.num_unidade');
    
            if ($rsUnidade->getNumLinhas() > -1) {
                while (!$rsUnidade->eof()) {
                    $inCodUnidade = $rsUnidade->getCampo('num_unidade');
                    $stNomUnidade = $rsUnidade->getCampo('num_unidade').' - '.$rsUnidade->getCampo('nom_unidade');
                    
                    $js .= "jq('#inCodUnidadeOrcamento').append(new Option('".$stNomUnidade."','".$inCodUnidade."'));\n"; 
                    $rsUnidade->proximo();
                }
                $js .= "jq('#inCodUnidadeOrcamento').val('".$inCodUnidade."');\n";
            }
        }
    break;

    case 'alterar':
        $js .= montaCombo('true');
        $js .= buscaOrgaoUnidade();
        $js .= montaSpanReserva();

        if ($request->get('inCodDespesa') != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);
            $nuSaldoDotacao = bcadd($nuSaldoDotacao, $request->get('nuVlItemExcluidos'), 4);
            $js .= montaLabel($nuSaldoDotacao);
        }

        $js .= montaLista(Sessao::read('arItens'), false);
        $js .= "LiberaFrames(true,false);\n";
    break;

    case 'buscaDespesa':
        if ($request->get('inCodDespesa') != '' AND $request->get('inCodEntidade') != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($request->get('inCodEntidade'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario($rsDespesa);

            $stNomDespesa = $rsDespesa->getCampo('descricao');

            if (!$stNomDespesa) {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesa($rsDespesa2);
                $stNomDespesa2 = $rsDespesa2->getCampo('descricao');
                if (!$stNomDespesa2) {
                    $js .= "jq('#inCodDespesa').val('');\n";
                    $js .= "jq('#inCodDespesa').focus();\n";
                    $js .= "jq('#stNomDespesa').html('&nbsp;');\n";
                    $js .= "alertaAviso('@Valor inválido. (".$request->get("inCodDespesa").")','form','erro','".Sessao::getId()."');\n";
                } else {
                    $js .= "jq('#inCodDespesa').val('');\n";
                    $js .= "jq('#inCodDespesa').focus();\n";
                    $js .= "jq('#stNomDespesa').html('&nbsp;');\n";
                    $js .= "alertaAviso('@Você não possui permissão para esta dotação. (".$request->get("inCodDespesa").")','form','erro','".Sessao::getId()."');\n";
                }
            } else {
                $js .= "jq('#stNomDespesa').html('".$stNomDespesa."');\n";
            }
        } else {
            $js .= "jq('#stNomDespesa').html('&nbsp;');\n";
        }
    
        if ($request->get("inCodDespesa") != '' AND $stNomDespesa) {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($request->get('inCodDespesa'));
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);

            if ($request->get('inCodDespesa') == $request->get('inCodDespesaAux') and $request->get('stAcao') == 'alterar') {
                $nuSaldoDotacao = bcadd($nuSaldoDotacao, $request->get('nuVlItemExcluidos'), 4);
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
                $js .= " jq('#inCodOrgao').empty().append(new Option('".trim($stNomOrgao)."','".$inCodOrgao."'));\n";
                $js .= " jq('#inCodOrgao').val('".$inCodOrgao."');\n";
                $js .= " jq('#inCodUnidadeOrcamento').empty().append(new Option('".trim($stNomUnidade)."','".$inCodUnidade."'));\n";
                $js .= " jq('#inCodUnidadeOrcamento').val('".$inCodUnidade."');\n";
            }
            
            $js .= montaSpanReserva();
        } else {
            $js .= montaOrgaoUnidade($inNumOrgao, $request->get('inCodUnidadeOrcamento'));
            
            $js .= "jq('#spnSaldoDotacao').html('&nbsp;');\n";
            $js .= "jq('#spnReserva').html('&nbsp;');\n";
        }
        
        $js .= montaCombo($stNomDespesa);
    break;

    case 'buscaClassificacao':
        if ($request->get("stCodClassificacao") != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($request->get('stCodClassificacao'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsClassificacao);
            $stNomClassificacao = $rsClassificacao->getCampo('descricao');
            if (!$stNomClassificacao) {
                $js .= "jq('#stCodClassificacao').val('');\n";
                $js .= "jq('#stCodClassificacao').focus();\n";
                $js .= "jq('#stNomClassificacao').html('&nbsp;');\n";
                $js .= "alertaAviso('@Valor inválido. (".$request->get("stCodClassificacao").")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= "jq('#stNomClassificacao').html('".$stNomClassificacao."';";
            }
        } else $js .= 'jq("#stNomClassificacao").html("&nbsp;");';
    break;

    case 'buscaFornecedor':
        if ($request->get("inCodFornecedor") != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($request->get('inCodFornecedor'));
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->obRCGM->listar($rsCGM);
            $stNomFornecedor = $rsCGM->getCampo('nom_cgm');
            if (!$stNomFornecedor) {
                $js .= "jq('#inCodFornecedor').val('');\n";
                $js .= "jq('#inCodFornecedor').focus();\n";
                $js .= "jq('#stNomFornecedor').html('&nbsp;');\n";
                $js .= "alertaAviso('@Valor inválido. (".$request->get("inCGM").")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'jq("#stNomFornecedor").html("'.$stNomFornecedor.'");';
            }
        } else {
            $js .= 'jq("#stNomFornecedor").html("&nbsp;");';
        }
    break;

    case 'incluiItemPreEmpenho':
        $arItens = Sessao::read('arItens');
        $inCount = sizeof($arItens);
        $nuVlTotal = str_replace('.','',$request->get('nuVlTotal'));
        $nuVlTotal = str_replace(',','.',$nuVlTotal);
        if($_REQUEST['stTipoItem']=='Catalogo'){
            list($inCodUnidade, $inCodGrandeza) = explode("-",$request->get('inCodUnidadeMedida'));
            $stNomUnidade = $request->get('stNomUnidade');
        }else{
            list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$request->get('inCodUnidade'));
        }
        $arItens[$inCount]['num_item']     = $inCount+1;
        if ($_REQUEST['stTipoItem']=='Catalogo') {
            foreach ($arItens as $key => $valor) {
                if ($valor['cod_item'] == $request->get('inCodItem')) {
                    $erro=true;
                }
            }
            $arItens[$inCount]['cod_item']     = $request->get('inCodItem');
            $arItens[$inCount]['nom_item']     = $request->get('stNomItemCatalogo');
        }else{
            $arItens[$inCount]['nom_item']     = $request->get('stNomItem');
        }
        $arItens[$inCount]['complemento']  = trim($request->get('stComplemento'));
        $arItens[$inCount]['quantidade']   = trim($request->get('nuQuantidade'));
        $arItens[$inCount]['vl_unitario']  = $request->get('nuVlUnitario');
        $arItens[$inCount]['cod_unidade']  = $inCodUnidade;
        $arItens[$inCount]['cod_grandeza'] = $inCodGrandeza;
        $arItens[$inCount]['nom_unidade']  = $stNomUnidade;
        $arItens[$inCount]['vl_total']     = $nuVlTotal;
        $arItens[$inCount]['cod_material'] = trim($request->get('inCodMaterial'));
        if($erro){
            $js = "alertaAviso('Item(".$request->get('inCodItem').") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            $js .= "jq('#Ok').prop('disabled',false);\n";
        }else{
            $js = montaLista( $arItens, false);
            Sessao::write('arItens', $arItens);
        }
    break;

    case 'alterarItemPreEmpenho':
        $arItens = array();
        $arItens = Sessao::read('arItens');
    
        foreach ($arItens as $valor) {
            if ($valor['num_item'] == $request->get('num_item')) {
                $stUnidade = $valor['cod_unidade'].'-'.$valor['cod_grandeza'].'-'.$valor['nom_unidade'];
                $js .= "jq('#hdnNumItem').val('".$request->get('num_item')."');";
                if ($request->get('cod_item')) {
                    $js .= "jq('#inCodItem').val('".$valor['cod_item']."');\n";
                    $js .= "jq('#HdninCodItem').val('".$valor['cod_item']."');\n";
                    $js .= "jq('#stNomItemCatalogo').val('".$valor["nom_item"]."');\n";
                    $js .= "jq('#stNomItemCatalogo').html('".$valor["nom_item"]."');\n";
                    $js .= "jq('#inCodUnidadeMedida').val('".$valor["cod_unidade"]."-". $valor["cod_grandeza"]."');\n";
                    $js .= "jq('#stNomUnidade').val('".$valor["nom_unidade"]."');\n";
                } else {
                    $js .= "jq('#stNomItem').val('".htmlentities($valor["nom_item"], ENT_QUOTES)."');";
                }
                $js .= "jq('#stComplemento').val('".htmlentities($valor["complemento"], ENT_QUOTES)."');";
                $js .= "jq('#nuQuantidade').val('".number_format($valor['quantidade'],2,',','.')."');";
                $js .= "jq('#inCodUnidade').val('".$stUnidade."');";
                $js .= "jq('#nuVlUnitario').val('".$valor['vl_unitario']."');";
                $js .= "jq('#nuVlTotal').val('".number_format($valor['vl_total'],2,',','.')."');";
                $js .= "jq('#btnIncluir').val('Alterar');";
                $js .= "jq('#btnIncluir').attr('onclick','return alterarItem()');\n";
                $js .= "jq('#stNomItem').val(f.stNomItem.value.unescapeHTML());";
                $js .= "jq('#stComplemento').val(f.stComplemento.value.unescapeHTML());";
            }
        }
    break;

    case 'alteradoItemPreEmpenho':
        $arItens = array();
        $arItens = Sessao::read('arItens');
        foreach ($arItens as $key => $valor) {
            if ($valor['num_item'] == $request->get('hdnNumItem')) {
                for($i=0;$i<count($arItens);$i++){
                    if($request->get('stTipoItem')=='Catalogo'&&($arItens[$i]['cod_item'] == $request->get('inCodItem'))&&($arItens[$i]['num_item'] != $request->get('hdnNumItem'))){
                        $erro=true;      
                    }
                }
                
                if(!$erro){
                    if($request->get('stTipoItem')=='Catalogo'){
                        list($inCodUnidade, $inCodGrandeza) = explode("-",$request->get('inCodUnidadeMedida'));
                        $stNomUnidade = $request->get('stNomUnidade');
                        $arItens[$key]['cod_item']    = $_POST['inCodItem'];
                        $arItens[$key]['nom_item']    = stripslashes($request->get('stNomItemCatalogo'));
                    }else{
                        list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$request->get('inCodUnidade'));
                        $arItens[$key]['nom_item'   ]  = stripslashes($request->get('stNomItem'));
                    }
                    $arItens[$key]['complemento']  = stripslashes($request->get('stComplemento'));
                    $arItens[$key]['quantidade' ]  = $request->get('nuQuantidade');
                    $arItens[$key]['cod_unidade']  = $inCodUnidade;
                    $arItens[$key]['cod_grandeza'] = $inCodGrandeza;
                    $arItens[$key]['nom_unidade']  = $stNomUnidade;
                    $arItens[$key]['vl_unitario']  = $request->get('nuVlUnitario');
        
                    $nuVlTotal = str_replace('.','',$request->get('nuVlTotal'));
                    $nuVlTotal = str_replace(',','.',$nuVlTotal);
        
                    $arItens[$key]['vl_total'] = $nuVlTotal;
                    break;
                }
            }
            else{
                if($request->get('stTipoItem')=='Catalogo'&&($valor['cod_item'] == $request->get('inCodItem'))){
                    $erro = true;    
                }
            }
        }
        
        if($erro){
            $js .= "alertaAviso('Item(".$request->get('inCodItem').") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            $js .= "jq('#Ok').prop('disabled',false);\n";
            $js .= "jq('#btnIncluir').attr('onclick','return incluirItem()');\n";
        } else {
            Sessao::write('arItens', $arItens);
            $js .= "jq('#btnIncluir').attr('onclick','return incluirItem()');\n";        
            $js .= "montaLista(".Sessao::read('arItens').")\n";
        }
        
    break;

    case 'excluirItemPreEmpenho':
        $arTEMP = array();
        $inCount = 0;
        $arItens = Sessao::read('arItens');

        for($i=0;$i<count($arItens);$i++){
            if($arItens[$i]['num_item']!=$request->get('inNumItem')){
                $arTEMP[$inCount]['num_item']     = $inCount+1;
                
                if($request->get('stTipoItem')=='Catalogo'){
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
        $js .= montaLista($arItens, false);
        Sessao::write('arItens', $arItens);

        if(count($arTEMP)==0){
            $js .= "jq('#stTipoItemRadio1').prop('disabled',false);\n";
            $js .= "jq('#stTipoItemRadio2').prop('disabled',false);\n";
        }
    break;

    case 'montaListaItemPreEmpenho':
        $js  = montaLista(Sessao::read('arItens'), false);
        $js .= montaCombo('');
    break;

    case 'montaListaItemPreEmpenhoAnular':
        $js  = montaLista(Sessao::read('arItens'), false);
    break;

    case 'limpar' :
        Sessao::remove('arItens');
        $js .= montaLista(Sessao::read('arItens'), false);
    break;

    case 'recuperaOrgao':
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM(Sessao::read('numCgm'));
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao, $stOrder);
    
        $js .= "jq('#inCodOrgao').empty().append(new Option('Selecione','')); \n"; 
    
        $js .= "jq('#inCodUnidadeOrcamento').empty();\n";

        while (!$rsOrgao->eof()) {
            $js .= "jq('#inCodOrgao').append(new Option('".$rsOrgao->getCampo("num_orgao").' - '.$rsOrgao->getCampo("nom_orgao")."','".$rsOrgao->getCampo("num_orgao")."'));\n";
            $rsOrgao->proximo();
        }
        $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n";
    break;

    case 'buscaDtAutorizacao':
        if ($request->get("inCodEntidade") != '') {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade($request->get('inCodEntidade'));
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData($rsMaiorData);
    
            if (!$obErro->ocorreu()) {
                $stDtAutorizacao = $rsMaiorData->getCampo('data_autorizacao');
                if ($stDtAutorizacao) {
                    $js .= "jq('#stDtAutorizacao').val('".$stDtAutorizacao."');\n";
                    $js .= "jq('#stDtAutorizacao').focus();\n";
                } else {
                    $js .= "jq('#stDtAutorizacao').html('01/01/".date('Y')."');\n";
                }
            }
    
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obRUsuario->obRCGM->setNumCGM( Sessao::read('numCgm') );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $request->get("inCodEntidade") );
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario( $rsOrgao, $stOrder );
    
            $js .= "jq('#inCodOrgao').empty().append(new Option('Selecione',''));\n";
            $js .= "jq('#inCodUnidadeOrcamento').empty().append(new Option('Selecione',''));\n";
    
            while (!$rsOrgao->eof()) {
                $js .= "jq('#inCodOrgao').append(new Option('".$rsOrgao->getCampo('num_orgao')." - ".trim($rsOrgao->getCampo('nom_orgao'))."','".$rsOrgao->getCampo('num_orgao')."'));\n";
                $rsOrgao->proximo();
            }
        } else {
            $js .= "jq('#stDtAutorizacao').val('".date('d/m/Y')."');\n";
        }
        $js .= "LiberaFrames(true,false);";
    break;

    case 'verificaFornecedor':
        if ($request->get('inCodFornecedor') && $request->get('inCodContrapartida') && ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)) {
            $boPendente = false;
            if ($request->get('stDtAutorizacao')) {
                include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
                $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
                $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$request->get('inCodFornecedor'));
                $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$request->get('inCodContrapartida'));
                $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);
    
                if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                    while (!$rsVerificaEmpenho->eof()) {
                        if (SistemaLegado::comparaDatas($request->get('stDtAutorizacao'),$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                               $boPendente = true;
                        }
                        $rsVerificaEmpenho->Proximo();
                    }
                }
            }
    
            if ($boPendente) {
                $js .= " alertaAviso('@O responsável por adiantamento informado possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
            } else {
                $js .= " alertaAviso('','','','".Sessao::getId()."'); ";
            }
        }
    break;

    case 'buscaContrapartida':
        if ($request->get('inCodFornecedor') && ($request->get('inCodCategoria') == 2 || $request->get('inCodCategoria') == 3)) {
            include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado('exercicio', Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado('numcgm'   , $request->get('inCodFornecedor'));
            $obTEmpenhoResponsavelAdiantamento->recuperaContrapartidaLancamento($rsContrapartida);
            $inCodContrapartida = $request->get('hdnCodContrapartida');
    
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
    
                $js .= "jq('#spnContrapartida').html('".$stHtml."');\n";
            } else {
                $js .= "jq('#inCodCategoria').prop('selectedIndex',0);\n";
                $js .= "jq('#spnContrapartida').html('&nbsp;');\n";
                $js .= "alertaAviso('@O responsável por adiantamento informado não está cadastrado ou está inativo.','form','erro','".Sessao::getId()."');\n";
            }
        } else {
            $jq .= "jq('#spnContrapartida').html('&nbsp;');\n";
        }
    break;

    case "unidadeItem":
            if( $request->get("codItem") ){
                $stFiltro=" WHERE cod_item=".$request->get("codItem");
                
                include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCatalogoItem.class.php" );
                $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem;
                $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $request->get("codItem"));
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
    break;

    case "montaOrgaoUnidade":
        $js = montaOrgaoUnidade($request->get("hdnCodOrgao"), $request->get("hdnCodUnidade"), $request->get("inCodDespesa"));
    break;
}

if ($js) {
    echo $js;
}

?>

