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
    * Paginae Oculta de Empenho
    * Data de Criação   : 17/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: OCManterEmpenho.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 31087 $
    $Name$
    $Author: grasiele $
    $Date: 2008-03-27 11:23:31 -0300 (Qui, 27 Mar 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.04
                    uc-02.01.08

*/

header ("Content-Type: text/html; charset=utf-8");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoAutorizacaoEmpenho.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenhoAutorizacao.class.php';
include_once CAM_GF_EMP_NEGOCIO.'REmpenhoEmpenho.class.php';
include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoPreEmpenho.class.php';
include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoParticipanteDocumentos.class.php';

//Define o nome dos arquivos PHP
$stPrograma = 'ManterEmpenho';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgPror = 'PO'.$stPrograma.'.php';

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());

$obREmpenhoEmpenho = new REmpenhoEmpenho;
$obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());

function montaLista($arRecordSet, $boExecuta = true)
{
    for($i=0;$i<count($arRecordSet);$i++){
            if(isset($arRecordSet[$i]['cod_item'])&&$arRecordSet[$i]['cod_item']!='')
                $codItem = true;
            break;
    }
        
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );
    $rsLista->addFormatacao('vl_total'   , 'NUMERIC_BR');

    $obLista = new Lista;
    $obLista->setMostraPaginacao(false);
    $obLista->setRecordSet($rsLista);
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('&nbsp;');
    $obLista->ultimoCabecalho->setWidth(5);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Descrição');
    $obLista->ultimoCabecalho->setWidth(55);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Valor Unitário');
    $obLista->ultimoCabecalho->setWidth(15);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Quantidade');
    $obLista->ultimoCabecalho->setWidth(10);
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo('Valor Total');
    $obLista->ultimoCabecalho->setWidth(15);
    $obLista->commitCabecalho();

    $obLista->addDado();
    if ($codItem)
        $obLista->ultimoDado->setCampo( "[cod_item] - [nom_item]" );
    else
        $obLista->ultimoDado->setCampo( "nom_item" );
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

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace("\n" ,"" ,$stHTML );
    $stHTML = str_replace(chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace("  " ,"" ,$stHTML );
    $stHTML = str_replace("'","\\'",$stHTML );
    $stHTML = str_replace("\\\'","\\'",$stHTML );

    $nuVlTotal = 0;
    foreach ($arRecordSet as $value) {
        $vl_total = str_replace('.','',$value['vl_total']);
        $vl_total = str_replace(',','.',$vl_total);
        $nuVlTotal += $value['vl_total'];
    }
    $nuVlTotal = number_format($nuVlTotal,2,',','.');

    if ($boExecuta) {
        echo "d.getElementById('spnLista').innerHTML = '".$stHTML."';\n
              d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."';\n
              f.nuVlReserva.value='".$nuVlTotal."';";
    } else {
        return $stHTML;
    }
}

function montaCombo()
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($_REQUEST['inCodDespesa'] != "") {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);
        $stCodClassificacao = $rsConta->getCampo('cod_estrutural');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);

        if ($rsClassificacao->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                if ($stMascaraReduzidaOld) {
                    if ($stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld))) {
                        $selected = "";
                        if ($stCodEstruturalOld == $_REQUEST["stCodEstrutural"]) {
                            $selected = "selected";
                        }
                        $stOption = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";
                        $js .= "f.stCodClassificacao.options[$inContador] = new Option( $stOption ); \n";
                        $inContador++;
                    }
                }
                $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
                $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
                $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = "";
                $rsClassificacao->proximo();
            }
            if ($stMascaraReduzidaOld) {
                if ($stCodEstruturalOld == $_REQUEST['stCodEstrutural']) {
                    $selected = "selected";
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
    if ($flSaldoDotacao == null) {
        $flSaldoDotacao = '&nbsp;';
    } else {
        $flSaldoDotacao = number_format($flSaldoDotacao,2,',','.');
    }
    $js1.= "d.getElementById('nuSaldoAnterior').innerHTML = '".$flSaldoDotacao."';";

    return $js1;
}

function montaListaDiverso($arRecordSet, $boExecuta = true)
{
	$codUf = SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio());
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
        if ($_REQUEST['stTipoItem']=='Catalogo') {
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

        if ($_REQUEST['stAcao'] != 'anular') {
            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo('&nbsp;');
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();
        }
        if ($_REQUEST['stTipoItem']=='Catalogo') {
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
        if ($_REQUEST['stAcao'] != 'anular') {
            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao('ALTERAR');
            $obLista->ultimaAcao->setFuncaoAjax(true);
            $obLista->ultimaAcao->setLink("JavaScript:alterarEmpenho('alterarItemPreEmpenhoDiverso');");
            $obLista->ultimaAcao->addCampo('1', 'num_item');
            if ($_REQUEST['stTipoItem']=='Catalogo') {
                $obLista->ultimaAcao->addCampo('2', 'cod_item');    
            }
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao('EXCLUIR');
            $obLista->ultimaAcao->setFuncao(true);
            $obLista->ultimaAcao->setLink("JavaScript:excluirItem('excluirItemPreEmpenhoDiverso');");
            $obLista->ultimaAcao->addCampo('1', 'num_item');
            $obLista->commitAcao();
        }
        $obLista->montaHTML();

        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\r\n" ,"" ,$stHTML );
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        foreach ($arRecordSet as $value) {
            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $value['vl_total'];
        }
        $nuVlTotal = number_format($nuVlTotal,2,',','.');

        $stLista    = "d.getElementById('spnLista').innerHTML = '".$stHTML."'; ";
        $stLista   .= "f.Ok.disabled = false; ";
        if ($_REQUEST['stTipoItem']=='Catalogo') {
            $stLista .= "d.getElementById('inCodItem').value = ''; ";
            $stLista .= "d.getElementById('stNomItemCatalogo').innerHTML = '&nbsp;'; ";
            $stLista .= "d.getElementById('stUnidadeMedida').innerHTML = '&nbsp;'; ";
        }else{
            $stLista .= "d.getElementById('stNomItem').innerHTML = '&nbsp;'; ";
        }
        $stVlTotal  = "d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."'; ";
        $stVlTotal .= "d.getElementById('hdnVlReserva').value= '".$nuVlTotal."'; ";
    } else {
        $stLista    = "d.getElementById('spnLista').innerHTML = ''; ";
        $stLista   .= "f.Ok.disabled = false; ";
        $stVlTotal  = "d.getElementById('nuValorTotal').innerHTML='&nbsp;'; ";
        $stVlTotal .= "d.getElementById('hdnVlReserva').value= ''; ";
        Sessao::remove('arItens');
    }

    if ($boExecuta) {
        SistemaLegado::executaFrameOculto($stLista.$stVlTotal);
    } else {
        return $stLista.$stVlTotal;
    }
}

function montaComboDiverso()
{
    global $obREmpenhoAutorizacaoEmpenho;
    if ($_REQUEST['inCodDespesa'] != "") {
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST['inCodDespesa']);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa($rsConta);
        $stCodClassificacao = $rsConta->getCampo('cod_estrutural');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao($stCodClassificacao);
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa('');
        $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarCodEstruturalDespesa($rsClassificacao);
        $obREmpenhoAutorizacaoEmpenho->checarFormaExecucaoOrcamento($stFormaExecucao);

        if ($rsClassificacao->getNumLinhas() > -1) {
            $inContador = 1;
            $js .= "limpaSelect(f.stCodClassificacao,0); \n";
            $js .= "f.stCodClassificacao.options[0] = new Option('Selecione','', 'selected');\n";
            while (!$rsClassificacao->eof()) {
                $stMascaraReduzida = $rsClassificacao->getCampo("mascara_reduzida");
                if ($stMascaraReduzidaOld) {

                    if ($stMascaraReduzidaOld != substr($stMascaraReduzida,0,strlen($stMascaraReduzidaOld))) {
                        $selected = "";
                        if ($stCodEstruturalOld == $_REQUEST["stCodEstrutural"]) {
                            $selected = "selected";
                        }

                        $arOptions[]['reduzido']                  = $stMascaraReduzidaOld;
                        $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";

                        $inContador++;
                    }
                }
                $inCodContaOld        = $rsClassificacao->getCampo("cod_conta");
                $stCodEstruturalOld   = $rsClassificacao->getCampo("cod_estrutural");
                $stDescricaoOld       = $rsClassificacao->getCampo("descricao");
                $stMascaraReduzidaOld = $stMascaraReduzida;
                $stMascaraReduzida    = "";
                $rsClassificacao->proximo();
            }
            if ($stMascaraReduzidaOld) {
                if ($stCodEstruturalOld == $_REQUEST['stCodEstrutural']) {
                    $selected = "selected";
                }
                $arOptions[]['reduzido'] = $stMascaraReduzidaOld;
                $arOptions[count($arOptions)-1]['option'] = "'".$stCodEstruturalOld.' - '.$stDescricaoOld."','".$stCodEstruturalOld."','".$selected."'";

            }

            // Remove Contas Sintéticas
            if (is_array($arOptions)) {
                $count = 0;
                for ($x=0; $x<count($arOptions); $x++) {
                    for ($y=0; $y<count($arOptions) ; $y++) {
                        $estruturalX = str_replace('.', '', $arOptions[$x]['reduzido']);
                        $estruturalY = str_replace('.', '', $arOptions[$y]['reduzido']);

                        if ((strpos($estruturalY,$estruturalX)!==false) && ($estruturalX !== $estruturalY)) {
                            $count++;
                        }
                    }
                    if ($count>=1) {
                        unset($arOptions[$x]);
                    }
                    $count = 0;
                }
                if ($stFormaExecucao) {
                    $inContador = 1;
                } else {
                    $inContador = 0;
                }

                asort($arOptions);
                foreach ($arOptions as $option) {
                    $js .= "f.stCodClassificacao.options[".$inContador++."] = new Option(". $option['option'] ."); \n";
                }
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

function montaLabelDiverso($flSaldoDotacao)
{
    $flSaldoDotacao = number_format($flSaldoDotacao ,2,',','.');

    $obHdnSaldo = new Hidden;
    $obHdnSaldo->setName ("flVlSaldo");
    $obHdnSaldo->setValue($flSaldoDotacao);

    $obLblSaldo = new Label;
    $obLblSaldo->setRotulo("Saldo da Dotação");
    $obLblSaldo->setValue ($flSaldoDotacao);

    $obFormulario = new Formulario;
    $obFormulario->addHidden($obHdnSaldo);
    $obFormulario->addComponente($obLblSaldo);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $js1 = "d.getElementById('spnSaldoDotacao').innerHTML = '".$stHtml."';";

    return $js1;
}

function validaDataFornecedor($inCodFornecedor)
{
    $rsLicitacaoDocumentos = new RecordSet;
    $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos;
    $stSql = " AND cgm.numcgm = ".$inCodFornecedor." \n";
    $obTLicitacaoParticipanteDocumentos->recuperaDocumentoParticipante($rsRecordSet, $stSql);

    while (!$rsRecordSet->eof()) {
        $comparaData = SistemaLegado::comparaDatas($rsRecordSet->getCampo("dt_validade"),date('d/m/Y'));
        if (!$comparaData) {
            echo "jq('#boMsgValidadeFornecedor').val('true');";
        }
        $rsRecordSet->proximo();
    }
}

$inCodEntidade = $_REQUEST["inCodEntidade"];
switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista(Sessao::read('arItens'));
    break;
     case 'verificaFornecedor':
        if ($_REQUEST['inCodFornecedor'] != "") {
            validaDataFornecedor($_REQUEST['inCodFornecedor']);
            if ($_REQUEST['inCodFornecedor'] && $_REQUEST['inCodContrapartida'] && ( $_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3)) {
                $boPendente = false;
                include_once( TEMP."TEmpenhoResponsavelAdiantamento.class.php");
                $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
                $obTEmpenhoResponsavelAdiantamento->setDado('exercicio',Sessao::getExercicio());
                $obTEmpenhoResponsavelAdiantamento->setDado('numcgm',$_REQUEST['inCodFornecedor']);
                $obTEmpenhoResponsavelAdiantamento->setDado('conta_contrapartida',$_REQUEST['inCodContrapartida']);
                $obTEmpenhoResponsavelAdiantamento->consultaEmpenhosFornecedor($rsVerificaEmpenho);

                if ($rsVerificaEmpenho->getNumLinhas() > 0) {
                    while (!$rsVerificaEmpenho->eof()) {
                        if (SistemaLegado::comparaDatas($_REQUEST['stDtEmpenho'],$rsVerificaEmpenho->getCampo('dt_prazo_prestacao'))) {
                               $boPendente = true;
                        }
                        $rsVerificaEmpenho->Proximo();
                    }
                    if ($boPendente) {
                        echo " alertaAviso('@O responsável por adiantamento informado possui prestação de contas pendentes.','form','erro','".Sessao::getId()."'); ";
                    } else {
                        echo " alertaAviso('','','','".Sessao::getId()."'); ";
                    }
                }
            }
        }

    break;

    case 'buscaContrapartida':
        if ($_REQUEST['inCodFornecedor'] && ( $_REQUEST['inCodCategoria'] == 2 || $_REQUEST['inCodCategoria'] == 3)) {
            include_once TEMP.'TEmpenhoResponsavelAdiantamento.class.php';
            $obTEmpenhoResponsavelAdiantamento = new TEmpenhoResponsavelAdiantamento();
            $obTEmpenhoResponsavelAdiantamento->setDado("exercicio", Sessao::getExercicio());
            $obTEmpenhoResponsavelAdiantamento->setDado("numcgm"   , $_REQUEST['inCodFornecedor']);
            $obTEmpenhoResponsavelAdiantamento->recuperaContrapartidaLancamento($rsContrapartida);

            if ($rsContrapartida->getNumLinhas() > 0) {
                $obCmbContrapartida = new Select;
                $obCmbContrapartida->setRotulo    ('Contrapartida'                      );
                $obCmbContrapartida->setTitle     ('Informe a contrapartida.'           );
                $obCmbContrapartida->setName      ('inCodContrapartida'                 );
                $obCmbContrapartida->setId        ('inCodContrapartida'                 );
                $obCmbContrapartida->setNull      (false                                );
                $obCmbContrapartida->setValue     ($inCodContrapartida                  );
                $obCmbContrapartida->setStyle     ('width: 600'                         );
                $obCmbContrapartida->addOption    ('', 'Selecione'                      );
                $obCmbContrapartida->setCampoId   ('conta_contrapartida'                );
                $obCmbContrapartida->setCampoDesc ("[conta_contrapartida] - [nom_conta]");
                $obCmbContrapartida->preencheCombo($rsContrapartida                     );
                $obCmbContrapartida->obEvento->setOnChange("montaParametrosGET('verificaFornecedor','inCodFornecedor,inCodContrapartida,inCodCategoria');");

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obCmbContrapartida );
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

    case 'buscaDespesa':
        if ($_POST["inCodDespesa"] != "" and $inCodEntidade != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_POST["inCodDespesa"]);
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_POST["inCodEntidade"]);
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->consultarDotacao($rsDespesa);
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);
            $stNomDespesa = $rsDespesa->getCampo('descricao');

            if (!$stNomDespesa) {
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.inCodDespesa.focus();';
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDespesa"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomDespesa").innerHTML = "'.$stNomDespesa.'";';
                $js .= 'd.getElementById("inCodOrgao").innerHTML   = "'.$rsDespesa->getCampo("num_orgao")  .' - '.trim($rsDespesa->getCampo("nom_orgao")  ).'";';
                $js .= 'd.getElementById("inCodUnidade").innerHTML = "'.$rsDespesa->getCampo("num_unidade").' - '.trim($rsDespesa->getCampo("nom_unidade")).'";';
            }
        } else {
            $js .= 'd.getElementById("stNomDespesa").innerHTML = "&nbsp;";';
        }
        $js .= montaLabel($nuSaldoDotacao);
        $js .= montaCombo();
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaDataEmpenho':
        if ($_POST["stDtEmpenho"] != "" and $inCodEntidade != "") {
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);

            $stMaiorData = $rsMaiorData->getCampo('dataempenho');

            $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
            if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$_POST["stDtEmpenho"])) {
                $js .= "f.stDtEmpenho.value='" . $rsMaiorData->getCampo( "dataempenho" ) . "';";
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEmpenho.focus();';
                $js .= "alertaAviso('@Data de Empenho deve ser maior ou igual a ".$rsMaiorData->getCampo('dataempenho')." !','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'verificaDataEmpenhoAutorizacao':
        if ($_POST["stDtEmpenho"] != "" and $inCodEntidade != "") {
            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->listarMaiorData($rsMaiorData ,'',$boTransacao, $_REQUEST['stDtAutorizacao']);

            $stMaiorData = $rsMaiorData->getCampo('dataempenho');

            $stDataAtual = date("d") . "/" . date("m") . "/" . date("Y");
            if (SistemaLegado::comparaDatas($rsMaiorData->getCampo( "dataempenho" ),$_POST["stDtEmpenho"])) {
                $js .= "f.stDtEmpenho.value='" . $rsMaiorData->getCampo( "dataempenho" ) . "';";
                $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtEmpenho.focus();';
                $js .= "alertaAviso('@Data de Empenho deve ser maior ou igual a ".$rsMaiorData->getCampo('dataempenho')." !','form','erro','".Sessao::getId()."');";
            }
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaDtEmpenho':
        $js  = "LiberaFrames(true,false);\n";
        include_once CAM_GF_EMP_NEGOCIO.'REmpenhoConfiguracao.class.php';
        $obErro = new Erro;
        $obREmpenhoConfiguracao = new REmpenhoConfiguracao();
        $obREmpenhoConfiguracao->consultar();

        $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
        if ($obREmpenhoConfiguracao->getNumeracao() == 'P') {
            if ($_REQUEST['inCodEntidade'] != "") {
                if ($inCodEntidade) {
                    $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($inCodEntidade);
                    $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
                    $dtUltimaDataEmpenho = "01/01/".Sessao::getExercicio();
                    if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                        if ($rsUltimoEmpenho->getCampo("dt_empenho")!="") {
                            $dtUltimaDataEmpenho = SistemaLegado::dataToBr($rsUltimoEmpenho->getCampo("dt_empenho"));
                        }
                    }
                    $js .= "f.dtUltimaDataEmpenho.value = '$dtUltimaDataEmpenho';";

                    if (!$obErro->ocorreu()) {
                        $obErro = $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);
                        if (!$obErro->ocorreu()) {
                            $stDtEmpenho = $rsMaiorData->getCampo( "dataempenho" );
                            if ($stDtEmpenho) {
                                $js .= "f.stDtEmpenho.value='" . $stDtEmpenho . "';\n";
                                $js .= "f.inCodDespesa.focus();\n";
                            } else {
                                $js .= "f.stDtEmpenho.value='01/01/" . Sessao::getExercicio() . "';\n";
                            }
                        }
                    }
                } else {
                    $js .= "f.stDtEmpenho.value='" . date("d/m/Y") . "';\n";
                }
            }
        } else {
            $obErro = $obREmpenhoEmpenho->recuperaUltimoEmpenho($rsUltimoEmpenho);
            $dtUltimaDataEmpenho = "01/01/".Sessao::getExercicio();
            if (!$obErro->ocorreu() && $rsUltimoEmpenho->getNumLinhas() >= 1) {
                if ($rsUltimoEmpenho->getCampo("dt_empenho")!="") {
                    $dtUltimaDataEmpenho = SistemaLegado::dataToBr($rsUltimoEmpenho->getCampo("dt_empenho"));
                }
            }
            $js .= "f.dtUltimaDataEmpenho.value='$dtUltimaDataEmpenho';";
            if (!$obErro->ocorreu) {
                $obErro = $obREmpenhoEmpenho->listarMaiorData($rsMaiorData);
                if (!$obErro->ocorreu()) {
                    $stDtEmpenho = $rsMaiorData->getCampo('dataempenho');
                    if ($stDtEmpenho) {
                        $js .= "f.stDtEmpenho.value='" . $stDtEmpenho . "';\n";
                        $js .= "f.inCodDespesa.focus();\n";
                    } else {
                         $js .= "f.stDtEmpenho.value='01/01/" . Sessao::getExercicio() . "';\n";
                    }
                }
            }
        }

        echo $js;
    break;

    case "buscaOrgaoUnidadeDiverso":
        if ($_REQUEST['inCodOrgao'] != "") {
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao( $_REQUEST['inCodOrgao']);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario( $rsUnidade, "ou.num_orgao, ou.num_unidade");
            if ($rsUnidade->getNumLinhas() > -1) {
                $inContador = 1;
                $js .= "limpaSelect(f.inCodUnidadeOrcamento,0); \n";
                $js .= "f.inCodUnidadeOrcamento.options[0] = new Option('Selecione','', 'selected');\n";
                while (!$rsUnidade->eof()) {
                    $inCodUnidade = $rsUnidade->getCampo("num_unidade");
                    $stNomUnidade = $rsUnidade->getCampo("num_unidade")." - ".$rsUnidade->getCampo("nom_unidade");
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
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaDespesaDiverso':
        if ($_REQUEST["inCodDespesa"] != "" and $inCodEntidade != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDespesa"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodEntidade"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

            $stNomDespesa = $rsDespesa->getCampo('descricao');

            if (!$stNomDespesa) {
                $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesa($rsDespesa2);
                $stNomDespesa2 = $rsDespesa2->getCampo('descricao');

                if (!$stNomDespesa2) {
                    $js .= "f.inCodDespesa.value='';";
                    $js .= "f.inCodDespesa.focus();";
                    $js .= "d.getElementById('stNomDespesa').innerHTML='';";
                    $js .= "alertaAviso('@Valor inválido. (" . $_REQUEST['inCodDespesa'] . ")','form','erro','" . Sessao::getId() . "');";
                } else {
                    $js .= "f.inCodDespesa.value='';";
                    $js .= "f.inCodDespesa.focus();";
                    $js .= "d.getElementById('stNomDespesa').innerHTML='&nbsp;';";
                    $js .= "alertaAviso('@Você não possui permissão para esta dotação. (" . $_REQUEST['inCodDespesa'] . ")', 'form', 'erro', '" . Sessao::getId() . "');";
                }
                $js .= "d.getElementById('stOrgaoOrcamento').innerHTML='';";
                $js .= "f.hdnOrgaoOrcamento.value='';";
                $js .= "d.getElementById('stUnidadeOrcamento').innerHTML='';";
                $js .= "f.hdnUnidadeOrcamento.value='';";
            } else {
                $stNomDespesa = $rsDespesa->getCampo( "descricao" );
                $js .= "d.getElementById('stNomDespesa').innerHTML='" . $stNomDespesa . "';";
                $js .= montaComboDiverso();
            }
        } else $js .= "d.getElementById('stNomDespesa').innerHTML='&nbsp;';";

        if ($_REQUEST["inCodDespesa"] != '' and $stNomDespesa) {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST["inCodDespesa"]);
            $obREmpenhoAutorizacaoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->consultaSaldoAnterior($nuSaldoDotacao);

            $js .= montaLabelDiverso($nuSaldoDotacao);

            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa($_REQUEST["inCodDespesa"]);
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio(Sessao::getExercicio());
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario($rsDespesa);

            $inNumOrgao   = $rsDespesa->getCampo('num_orgao');
            $inNumUnidade = $rsDespesa->getCampo('num_unidade');

            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($inNumOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($inNumUnidade);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario($rsOrgao);
            $obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarUnidadeDespesaEntidadeUsuario($rsUnidade);

            $inCodOrgao   = $rsOrgao->getCampo('num_orgao');
            $stNomOrgao   = $rsOrgao->getCampo('nom_orgao');
            $inCodUnidade = $rsUnidade->getCampo('num_unidade');
            $stNomUnidade = $rsUnidade->getCampo('nom_unidade');

            $js .= "d.getElementById('stOrgaoOrcamento').innerHTML='" . $inCodOrgao . " - " . $stNomOrgao . "';";
            $js .= "f.hdnOrgaoOrcamento.value='" . $inCodOrgao . "';";
            $js .= "d.getElementById('stUnidadeOrcamento').innerHTML='" . $inCodUnidade . " - " . $stNomUnidade . "';";
            $js .= "f.hdnUnidadeOrcamento.value='" . $inCodUnidade . "';";
        } else {
            $js .= "d.getElementById('spnSaldoDotacao').innerHTML='';";
        }
        $js .= montaComboDiverso();
        $js .= "LiberaFrames(true,false);";

        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaClassificacaoDiverso':
        if ($_POST["stCodClassificacao"] != "") {
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $_POST["stCodClassificacao"] );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
            $obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarRelacionamentoContaDespesa( $rsClassificacao );
            $stNomClassificacao = $rsClassificacao->getCampo( "descricao" );
            if (!$stNomClassificacao) {
                $js .= "f.stCodClassificacao.value='';";
                $js .= "f.stCodClassificacao.focus();";
                $js .= "d.getElementById('stNomClassificacao').innerHTML='&nbsp;'";
                $js .= "alertaAviso('@Valor inválido. (" . $_POST["stCodClassificacao"] . ")', 'form', 'erro', '" . Sessao::getId() . "');";
            } else {
                $js .= "d.getElementById('stNomClassificacao').innerHTML='" . $stNomClassificacao . "';";
            }
        } else {
            $js .= "d.getElementById('stNomClassificacao').innerHTML='&nbsp;';";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'buscaFornecedorDiverso':
        if ($_REQUEST["inCodFornecedor"] != "") {
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM($_REQUEST["inCodFornecedor"]);
            $obREmpenhoAutorizacaoEmpenho->obRCGM->listar($rsCGM);
            $stNomFornecedor = trim($rsCGM->getCampo('nom_cgm'));
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else {$js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';}
        echo $js;
    break;

    case 'incluiItemPreEmpenhoDiverso':
        $inCount   = sizeof(Sessao::read('arItens'));
        $nuVlTotal = str_replace('.','',$_POST['nuVlTotal']);
        $nuVlTotal = str_replace(',','.',$nuVlTotal);
        if($_REQUEST['stTipoItem']=='Catalogo'){
            list($inCodUnidade, $inCodGrandeza) = explode("-",$_POST['inCodUnidadeMedida']);
            $stNomUnidade = $_POST['stNomUnidade'];
        }else{
            list($inCodUnidade, $inCodGrandeza, $stNomUnidade) = explode("-",$_POST['inCodUnidade']);
        }
        $arItens = Sessao::read('arItens');
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
        $arItens[$inCount]['complemento']  = $_POST['stComplemento'];
        $arItens[$inCount]['quantidade']   = $_POST['nuQuantidade'];
        $arItens[$inCount]['vl_unitario']  = $_POST['nuVlUnitario'];
        $arItens[$inCount]['cod_unidade']  = $inCodUnidade;
        $arItens[$inCount]['cod_grandeza'] = $inCodGrandeza;
        $arItens[$inCount]['nom_unidade']  = $stNomUnidade;
        $arItens[$inCount]['vl_total']     = $nuVlTotal;
        
        if($erro){
            $js = "alertaAviso('Item(".$_POST['inCodItem'].") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            SistemaLegado::executaFrameOculto($js);
        }else{
            Sessao::write('arItens', $arItens);
            $stHTML = montaListaDiverso( Sessao::read('arItens') );
        }
    break;

    case 'excluirItemPreEmpenhoDiverso':
        $arTEMP = array();
        $inCount = 0;
        $arItens = array();
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
                $inCount++;
            }
        }
        Sessao::write('arItens', $arTEMP);
        montaListaDiverso(Sessao::read('arItens'));
        if(count($arTEMP)==0){
            $js .= "d.getElementById('stTipoItemRadio1').disabled = false;";
            $js .= "d.getElementById('stTipoItemRadio2').disabled = false;";
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case 'montaListaItemPreEmpenhoDiverso':
        $js  = montaListaDiverso(Sessao::read('arItens'), false);
        $js .= montaCombo();
        SistemaLegado::executaFrameOculto($js);
    break;

    case 'alterarDiverso':
        $js  = montaLista(Sessao::read('arItens'), false);
        $js .= montaCombo();
    break;

    case "alterarItemPreEmpenhoDiverso":
        $arItens = array();
        $arItens = Sessao::read('arItens');

        foreach ($arItens as $valor) {
            if ($valor['num_item'] == $_REQUEST['num_item']) {
                $stJs .= "f.hdnNumItem.value='".$_REQUEST['num_item']."';";
                if ($_REQUEST['cod_item']) {
                    $stJs .= "f.inCodItem.value= '".$valor['cod_item']."';";
                    $stJs .= "f.HdninCodItem.value= '".$valor['cod_item']."';";
                    $stJs .= "f.stNomItemCatalogo.value ='".$valor["nom_item"]."';";
                    $stJs .= "d.getElementById('stNomItemCatalogo').innerHTML ='".$valor["nom_item"]."';";
                    $stJs .= "f.inCodUnidadeMedida.value= '".$valor["cod_unidade"]."-". $valor["cod_grandeza"]."';";
                    $stJs .= "f.stNomUnidade.value= '".$valor["nom_unidade"]."';";
                }else{
                    $stJs .= "f.stNomItem.value='".$valor["nom_item"]."';";
                }
                $stJs .= "f.stComplemento.value='".htmlentities($valor["complemento"], ENT_QUOTES)."';";
                $stJs .= "f.nuQuantidade.value='".$valor["quantidade"]."';";
                $stJs .= "f.nuVlUnitario.value='".$valor["vl_unitario"]."';";
                $stJs .= "f.nuVlTotal.value='".number_format($valor["vl_total"],2,',','.')."';";
                $stJs .= "f.btnIncluir.value='Alterar';";
                $stJs .= "f.btnIncluir.setAttribute('onclick','return alterarItem()');";
                $stJs .= "f.stNomItem.value = f.stNomItem.value.unescapeHTML();";
                $stJs .= "f.stComplemento.value = f.stComplemento.value.unescapeHTML();\n";
                
                $value = $valor["cod_unidade"]."-". $valor["cod_grandeza"]."-". $valor["nom_unidade"];
                $stJs .= "f.inCodUnidade.value='".$value."';";
                $stJs .= 'window.parent.frames["telaPrincipal"].document.frm.inCodItem.focus();';
            }
        }
        echo $stJs;
    break;

    case "alteradoItemPreEmpenhoDiverso":
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
		                $arItens[$key]['nom_item'   ] = stripslashes($_REQUEST["stNomItem"]);
		            }
		            
		            $arItens[$key]['complemento'] = stripslashes($_REQUEST["stComplemento"]);
		            $arItens[$key]['quantidade' ] = $_REQUEST["nuQuantidade"];
		            $arItens[$key]['cod_unidade'] = $inCodUnidade;
		            $arItens[$key]['vl_unitario'] = $_REQUEST["nuVlUnitario"];
		            $arItens[$key]['nom_unidade']  = $stNomUnidade;
		            $arItens[$key]['cod_grandeza'] = $inCodGrandeza;

		            $nuVlTotal = str_replace('.','',$_REQUEST["nuVlTotal"]);
		            $nuVlTotal = str_replace(',','.',$nuVlTotal);

		            $arItens[$key]['vl_total'] = $nuVlTotal;
		            break;
				}
            }else{
                if($_REQUEST['stTipoItem']=='Catalogo'&&($valor['cod_item'] == $_POST['inCodItem'])){
                    $erro=true;    
                }
            }
        }
        
        if($erro){
            $js = "alertaAviso('Item(".$_POST['inCodItem'].") Já Incluso na Lista.','frm','erro','".Sessao::getId()."'); \n";
            SistemaLegado::executaFrameOculto($js);
        }else{
            Sessao::write('arItens', $arItens);
            $stJs.= "f.btnIncluir.setAttribute('onclick','return incluirItem()');";
    
            echo montaListaDiverso(Sessao::read('arItens'));
            SistemaLegado::executaFrameOculto($stJs);
        }
    break;

    case 'buscaEmpenho':
        if ($_REQUEST["inCodigoEmpenho"] && $_REQUEST["inCodEntidade"]) {
            Sessao::remove('arItens');

            $obREmpenhoEmpenho = new REmpenhoEmpenho;

            $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
            $obREmpenhoEmpenho->setExercicio(Sessao::getExercicio());
            $obREmpenhoEmpenho->setCodEmpenhoInicial($_REQUEST["inCodigoEmpenho"]);
            $obREmpenhoEmpenho->setCodEmpenhoFinal($_REQUEST["inCodigoEmpenho"]);
            $obREmpenhoEmpenho->setSituacao(5);

            $obREmpenhoEmpenho->listar($rsLista);

            if ($rsLista->getNumLinhas() > 0) {
                $obREmpenhoEmpenho->setCodEmpenho($_REQUEST["inCodigoEmpenho"]);
                $obREmpenhoEmpenho->consultar();
                $stNomFornecedor = ($rsLista->getCampo('nom_fornecedor')) ? str_replace( "'","\'",$rsLista->getCampo("nom_fornecedor")):'&nbsp;';
                $js .= "d.getElementById('stNomFornecedor').innerHTML='".$stNomFornecedor."';";

                $stNomCategoria = $obREmpenhoEmpenho->getNomCategoria();
                $inCodDespesa   = $obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
                $stNomDespesa   = $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->getDescricao();
                $inCodHistorico = $obREmpenhoEmpenho->obREmpenhoHistorico->getCodHistorico();
                $stNomHistorico = str_replace ( '\\','',$obREmpenhoEmpenho->obREmpenhoHistorico->getNomHistorico());
                $stCodClassificacao = $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getMascClassificacao();
                $stNomClassificacao = $obREmpenhoEmpenho->obROrcamentoClassificacaoDespesa->getDescricao();

                $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
                $obTEmpenhoPreEmpenho->setDado("exercicio", Sessao::getExercicio());
                $obTEmpenhoPreEmpenho->setDado("cod_despesa", $inCodDespesa);
                $obErro = $obTEmpenhoPreEmpenho->recuperaSaldoAnterior($rsRecordSet, $stOrder, $boTransacao);
                if (!$obErro->ocorreu()) {
                    $nuValorSaldoAnterior = $rsRecordSet->getCampo('saldo_anterior');
                }

                $inNumUnidade = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNumeroUnidade();
                $stNomUnidade = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->getNomUnidade();
                $inNumOrgao   = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
                $stNomOrgao   = $obREmpenhoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->getNomeOrgao();
                $inCodFornecedor = $obREmpenhoEmpenho->obRCGM->getNumCGM();
                $stNomFornecedor = $obREmpenhoEmpenho->obRCGM->getnomCGM();

                $stNomCategoria = $obREmpenhoEmpenho->getNomCategoria();
                $inCodCategoria = $obREmpenhoEmpenho->getcodCategoria();
                $stDescricao = $obREmpenhoEmpenho->getDescricao();
                $stDtVencimento = $obREmpenhoEmpenho->getDtVencimento();
                $stDtEmpenho = $obREmpenhoEmpenho->getDtEmpenho();
                $inCodContrapartida = $obREmpenhoEmpenho->getCodContrapartida();
                $stNomContrapartida = $obREmpenhoEmpenho->getNomContrapartida();
                $inCodTipo = $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();
                $stNomTipo = $obREmpenhoEmpenho->obREmpenhoTipoEmpenho->getCodTipo();

                $flVlSaldo = number_format($nuValorSaldoAnterior,2,',','.');

                $obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->listarOrgaoDespesaEntidadeUsuario( $rsOrgao );
                $hdnOrgaoOrcamento   = $rsOrgao->getCampo('num_orgao');
                $hdnUnidadeOrcamento = $obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();

                if ($inCodDespesa != $inCodDespesaAnterior) {
                    $inCodDespesaAnterior =  $obREmpenhoEmpenho->obROrcamentoDespesa->getCodDespesa();
                }

                $arChaveAtributo =  array( "cod_pre_empenho" => $obREmpenhoEmpenho->getCodPreEmpenho(),
                                           "exercicio"       => Sessao::getExercicio());
                $obREmpenhoEmpenho->obRCadastroDinamico->setChavePersistenteValores($arChaveAtributo);
                $obREmpenhoEmpenho->obRCadastroDinamico->recuperaAtributosSelecionadosValores($rsAtributos);

                $arAtributosModificados = array();
                $arAtributosOriginais = $rsAtributos->arElementos;

                for ($w=0; $w < count($arAtributosOriginais); $w++) {
                    if ($arAtributosOriginais[$w]['cod_atributo'] == 101) { // Atributo : Modalidade
                        $obLblModalidade = new Label;
                        $obLblModalidade->setRotulo($arAtributosOriginais[$w]['nom_atributo']);
                        $arAux = explode("[][][]", $arAtributosOriginais[$w]['valor_padrao_desc']);
                        $obLblModalidade->setValue ($arAtributosOriginais[$w]['valor'] . ' - ' .  $arAux[($arAtributosOriginais[$w]['valor'] - 1)]);

                        $obHdnModalidade = new Hidden;
                        $obHdnModalidade->setName ("Atributo_" . $arAtributosOriginais[$w]['cod_atributo'] . "_" . $arAtributosOriginais[$w]['cod_cadastro']);
                        $obHdnModalidade->setValue($arAtributosOriginais[$w]['valor']);
                    } elseif ($arAtributosOriginais[$w]['cod_atributo'] == 103) { // Atributo : TipoCredor
                        $obLblTipoCredor = new Label;
                        $obLblTipoCredor->setRotulo($arAtributosOriginais[$w]['nom_atributo'] );
                        $arAux = explode("[][][]", $arAtributosOriginais[$w]['valor_padrao_desc']);
                        $obLblTipoCredor->setValue ($arAtributosOriginais[$w]['valor'] . ' - ' .  $arAux[($arAtributosOriginais[$w]['valor'] - 1)]);

                        $obHdnTipoCredor = new Hidden;
                        $obHdnTipoCredor->setName ("Atributo_" . $arAtributosOriginais[$w]['cod_atributo'] . "_" . $arAtributosOriginais[$w]['cod_cadastro']);
                        $obHdnTipoCredor->setValue($arAtributosOriginais[$w]['valor']);
                    } else {
                        if ($arAtributosOriginais[$w]['cod_atributo'] == 100) {	// Atributo : Complementar
                            $arAtributosOriginais[$w]['valor'] = 1;
                        }
                        $arAtributosModificados[] = $arAtributosOriginais[$w];
                    }
                }
                $rsAtributos->arElementos = $arAtributosModificados;

                $obHdnBoComplementar = new Hidden;
                $obHdnBoComplementar->setName ('obHdnBoComplementar');
                $obHdnBoComplementar->setValue(1);

                $obHdnCodigoCategoria = new Hidden;
                $obHdnCodigoCategoria->setName ('inCodCategoria');
                $obHdnCodigoCategoria->setValue($inCodCategoria);

                $obHdnContrapartida = new Hidden;
                $obHdnContrapartida->setName ('inCodContrapartida');
                $obHdnContrapartida->setValue($inCodContrapartida);

                $obHdnCodDespesa = new Hidden;
                $obHdnCodDespesa->setName ('inCodDespesa');
                $obHdnCodDespesa->setValue($inCodDespesa);

                $obHdnCodClassificacao = new Hidden;
                $obHdnCodClassificacao->setName ('stCodClassificacao');
                $obHdnCodClassificacao->setValue($stCodClassificacao);

                $obHdnCodFornecedor = new Hidden;
                $obHdnCodFornecedor->setName ('inCodFornecedor');
                $obHdnCodFornecedor->setValue($inCodFornecedor);

                $obHdnCodHistorico = new Hidden;
                $obHdnCodHistorico->setName ('inCodHistorico');
                $obHdnCodHistorico->setValue($inCodHistorico);

                $obHdnCodTipo = new Hidden;
                $obHdnCodTipo->setName ('inCodTipo');
                $obHdnCodTipo->setValue($inCodTipo);

                $obHdnNomTipo = new Hidden;
                $obHdnNomTipo->setName ('stNomTipo');
                $obHdnNomTipo->setValue($stNomTipo);

                $obHdnOrgaoOrcamento = new Hidden;
                $obHdnOrgaoOrcamento->setName ('HdnOrgaoOrcamento');
                $obHdnOrgaoOrcamento->setValue($hdnOrgaoOrcamento);

                $obHdnUnidadeOrcamento = new Hidden;
                $obHdnUnidadeOrcamento->setName ('HdnUnidadeOrcamento');
                $obHdnUnidadeOrcamento->setValue($hdnUnidadeOrcamento);

                $obHdnVlSaldoConta = new Hidden;
                $obHdnVlSaldoConta->setName ('flVlSaldo');
                $obHdnVlSaldoConta->setValue($flVlSaldo);

                $obHdnCodContrapartida = new Hidden;
                $obHdnCodContrapartida->setName ('inCodContrapartida');
                $obHdnCodContrapartida->setValue($inCodContrapartida);

                $obHdnNomDespesa = new Hidden;
                $obHdnNomDespesa->setName ('stNomDespesa');
                $obHdnNomDespesa->setValue($stNomDespesa);

                $obHdnCodDespesaAnterior = new Hidden;
                $obHdnCodDespesaAnterior->setName ('inCodDespesaAnterior');
                $obHdnCodDespesaAnterior->setValue($inCodDespesaAnterior);

                $obLblDotacao = new Label;
                $obLblDotacao->setRotulo('Dotação Orçamentária');
                $obLblDotacao->setId    ('stNomDespesa');
                $obLblDotacao->setValue ($inCodDespesa." - ".$stNomDespesa);

                $obLblDesdobramento = new Label;
                $obLblDesdobramento->setRotulo('Desdobramento');
                $obLblDesdobramento->setId    ('stNomClassificacao');
                $obLblDesdobramento->setValue ($stCodClassificacao.' - '.$stNomClassificacao);

                $obLblSaldoDotacao = new Label;
                $obLblSaldoDotacao->setRotulo('Saldo Dotação');
                $obLblSaldoDotacao->setId    ('flSaldoDotacao');
                $obLblSaldoDotacao->setValue (number_format($nuValorSaldoAnterior,2,',','.'));

                $obLblOrgaoOrcamento = new Label;
                $obLblOrgaoOrcamento->setRotulo('Órgão Orçamentário');
                $obLblOrgaoOrcamento->setId    ('stOrgaoOrcamento');
                $obLblOrgaoOrcamento->setValue ($inNumOrgao." - ".$stNomOrgao);

                $obLblUnidadeOrcamento = new Label;
                $obLblUnidadeOrcamento->setRotulo('Unidade Orçamentária');
                $obLblUnidadeOrcamento->setId    ('stUnidadeOrcamento');
                $obLblUnidadeOrcamento->setValue ($inNumUnidade." - ".$stNomUnidade);

                $obLblFornecedor = new Label;
                $obLblFornecedor->setRotulo('Credor');
                $obLblFornecedor->setValue ($inCodFornecedor.' - '.$stNomFornecedor);

                $obLblCategoria = new Label;
                $obLblCategoria->setRotulo('Categoria do Empenho');
                $obLblCategoria->setId    ('stNomCategoria');
                $obLblCategoria->setValue ($stNomCategoria);

                if ($inCodCategoria == 2 || $inCodCategoria == 3) {
                    $obLblContrapartida = new Label;
                    $obLblContrapartida->setRotulo('Contrapartida');
                    $obLblContrapartida->setValue ($inCodContrapartida.' - '.$stNomContrapartida);
                }

                // Define Objeto TextArea para Descricao
                $obTxtDescricao = new TextArea;
                $obTxtDescricao->setName         ('stDescricao');
                $obTxtDescricao->setId           ('stDescricao');
                $obTxtDescricao->setValue        ($stDescricao);
                $obTxtDescricao->setRotulo       ('Descrição do Empenho');
                $obTxtDescricao->setTitle        ('Informe a descrição do empenho.');
                $obTxtDescricao->setNull         (true);
                $obTxtDescricao->setRows         (6);
                $obTxtDescricao->setCols         (100);
                $obTxtDescricao->setMaxCaracteres(640);

                // Define objeto Data para validade final
                $obDtEmpenho = new Data;
                $obDtEmpenho->setName              ('stDtEmpenho');
                $obDtEmpenho->setValue             ($stDtEmpenho);
                $obDtEmpenho->setRotulo            ('Data de Empenho');
                $obDtEmpenho->setTitle             ('Informe a data do empenho.');
                $obDtEmpenho->setNull              (false);
                $obDtEmpenho->obEvento->setOnBlur  ('validaDataEmpenho();');
                $obDtEmpenho->obEvento->setOnChange("montaParametrosGET('verificaFornecedor');");

                // Define objeto Data para validade final
                $obDtValidadeFinal = new Data;
                $obDtValidadeFinal->setName              ('stDtVencimento');
                $obDtValidadeFinal->setValue             ($stDtVencimento);
                $obDtValidadeFinal->setRotulo            ('Data de Vencimento');
                $obDtValidadeFinal->setTitle             ('');
                $obDtValidadeFinal->setNull              (false);
                $obDtValidadeFinal->obEvento->setOnChange('validaVencimento();');

                $obLblHistorico = new Label;
                $obLblHistorico->setRotulo('Histórico');
                $obLblHistorico->setId    ('stNomHistorico');
                $obLblHistorico->setValue ($inCodHistorico.' - '.$stNomHistorico);

                // Atributos Dinamicos
                $obMontaAtributos = new MontaAtributos;
                $obMontaAtributos->setTitulo   ('Atributos');
                $obMontaAtributos->setName     ('Atributo_');
                $obMontaAtributos->setRecordSet($rsAtributos);

                $obFormulario = new Formulario;
                $obFormulario->addHidden($obHdnBoComplementar);
                $obFormulario->addHidden($obHdnContrapartida);
                $obFormulario->addHidden($obHdnCodigoCategoria);
                $obFormulario->addHidden($obHdnCodDespesa);
                $obFormulario->addHidden($obHdnNomDespesa);
                $obFormulario->addHidden($obHdnCodDespesaAnterior);
                $obFormulario->addHidden($obHdnCodClassificacao);
                $obFormulario->addHidden($obHdnCodFornecedor);
                $obFormulario->addHidden($obHdnCodHistorico);
                $obFormulario->addHidden($obHdnCodTipo);
                $obFormulario->addHidden($obHdnNomTipo);
                $obFormulario->addHidden($obHdnOrgaoOrcamento);
                $obFormulario->addHidden($obHdnUnidadeOrcamento);
                $obFormulario->addHidden($obHdnVlSaldoConta);
                $obFormulario->addHidden($obHdnCodContrapartida);
                if (isset($obHdnTipoCredor)) {
                    $obFormulario->addHidden($obHdnTipoCredor);
                }
                if (isset($obHdnModalidade)) {
                    $obFormulario->addHidden($obHdnModalidade);
                }
                $obFormulario->addComponente($obLblDotacao);
                $obFormulario->addComponente($obLblDesdobramento);
                $obFormulario->addComponente($obLblSaldoDotacao);
                $obFormulario->addComponente($obLblOrgaoOrcamento);
                $obFormulario->addComponente($obLblUnidadeOrcamento);
                $obFormulario->addComponente($obLblFornecedor);
                $obFormulario->addComponente($obLblCategoria);
                if ($inCodCategoria == 2 || $inCodCategoria == 3) {
                    $obFormulario->addComponente($obLblContrapartida);
                }
                $obFormulario->addComponente($obTxtDescricao );
                $obFormulario->addComponente($obDtEmpenho );
                $obFormulario->addComponente($obDtValidadeFinal );
                $obFormulario->addComponente($obLblHistorico );

                $obMontaAtributos->geraFormulario($obFormulario);
                validaDataFornecedor($inCodFornecedor);

                if (isset($obLblTipoCredor)) {
                    $obFormulario->addComponente($obLblTipoCredor);
                }
                if (isset($obLblModalidade)) {
                    $obFormulario->addComponente($obLblModalidade);
                }
                $obFormulario->montaInnerHTML();
                $stHtml = $obFormulario->getHTML();

                $js .= "d.getElementById('spnEmpenho').innerHTML = '".$stHtml."';";
                $js .= "montaParametrosGET('buscaDtEmpenho');";
                $js .= "d.getElementById('spnLista').innerHTML = '';";
            } else {
                $js .= "f.inCodigoEmpenho.value='';";
                $js .= "d.getElementById( 'stNomFornecedor' ).innerHTML = '&nbsp;';";
                $js .= "d.getElementById( 'spnEmpenho' ).innerHTML = '';";
                $js .= "alertaAviso('Empenho informado está anulado ou não existe.','frm','erro','".Sessao::getId()."'); \n";
            }
        } else {
            Sessao::remove('arItens');
            $js .= "f.inCodigoEmpenho.value='';";
            $js .= "d.getElementById('stNomFornecedor').innerHTML='&nbsp;';";
            $js .= "d.getElementById( 'spnEmpenho' ).innerHTML = '';";
            $js .= "d.getElementById('spnLista').innerHTML = '';";
            $js .= "alertaAviso('É necessário informar uma entidade.','frm','erro','".Sessao::getId()."');";
        }

        echo $js;
    break;

    case 'buscaFundamentacaoLegal':
        include_once CAM_GPC_TGO_MAPEAMENTO.'TTCMGOFundamentacaoLegal.php';

        /* Monta combo com fundamentações legais */
        $obFundamentacaoLegal = new TTCMGOFundamentacaoLegal();
        $obFundamentacaoLegal->recuperaTodos($rsFundamentacaoLegal);

        $obCmbFundamentacaoLegal = new Select;
        $obCmbFundamentacaoLegal->setRotulo('Fundamentação legal');
        $obCmbFundamentacaoLegal->setTitle('Fundamentação legal conforme art.24 e 25 da Lei 8.666 / 93');
        $obCmbFundamentacaoLegal->setName('inFundamentacaoLegal');
        $obCmbFundamentacaoLegal->setId('inFundamentacaoLegal');
        $obCmbFundamentacaoLegal->setStyle('width: 520');
        $obCmbFundamentacaoLegal->setCampoId   ('cod_fundamentacao');
        $obCmbFundamentacaoLegal->setCampoDesc ('descricao');
        $obCmbFundamentacaoLegal->addOption('', 'Selecione');
        $obCmbFundamentacaoLegal->preencheCombo($rsFundamentacaoLegal);
        $obCmbFundamentacaoLegal->setNull(false);

        // Define Objeto TextArea para Justificativa
        $obTxtJustificativa = new TextArea;
        $obTxtJustificativa->setName  ('stJustificativa');
        $obTxtJustificativa->setId    ('stJustificativa');
        $obTxtJustificativa->setValue ($stComplemento);
        $obTxtJustificativa->setRotulo('Justificativa');
        $obTxtJustificativa->setTitle ('Justificativa para contratação mediante dispensa ou inexigibilidade.');
        $obTxtJustificativa->setNull  (false);
        $obTxtJustificativa->setRows  (3);
        $obTxtJustificativa->setCols  (250);
        $obTxtJustificativa->setMaxCaracteres (250);

        // Define Objeto TextArea para Complemento
        $obTxtRazao = new TextArea;
        $obTxtRazao->setName  ('stRazao');
        $obTxtRazao->setId    ('stRazao');
        $obTxtRazao->setValue ($stComplemento);
        $obTxtRazao->setRotulo('Razão da escolha');
        $obTxtRazao->setTitle ('Razão da escolha do fornecedor ou executante quando contratação mediante dispensa ou inexigibilidade.');
        $obTxtRazao->setNull  (false);
        $obTxtRazao->setRows  (3);
        $obTxtRazao->setCols  (245);
        $obTxtRazao->setMaxCaracteres (245);

        $obFormulario = new Formulario;
        $obFormulario->addComponente($obCmbFundamentacaoLegal);
        $obFormulario->addComponente($obTxtJustificativa);
        $obFormulario->addComponente($obTxtRazao);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();

        $js = "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnFundamentacaoLegal').innerHTML = '".$stHtml."';</script>";

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
    
    case "limparOrdem":
        Sessao::remove('arItens');    
    break;
}

?>
