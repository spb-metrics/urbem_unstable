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
    * Página processamento ocuto Pessoal ServidorP
    * Data de Criação   : 14/12/2004
    *

    * @author Analista: Leandro Oliveira.
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 31426 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-10 13:40:16 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once 'OCManterServidorAbaContrato.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterServidor";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOculIdentificacao = "OC".$stPrograma."AbaIdentificacao.php";
$pgOculDocumentacao  = "OC".$stPrograma."AbaDocumentacao.php";
$pgOculContrato      = "OC".$stPrograma."AbaContrato.php";
$pgOculPrevidencia   = "OC".$stPrograma."AbaPrevidencia.php";
$pgOculDependentes   = "OC".$stPrograma."AbaDependentes.php";
$pgOculAtributos     = "OC".$stPrograma."AbaAtributos.php";
$pgJS                = "JS".$stPrograma.".js";

function validaDataCadPis()
{
    if ($_POST['dtCadastroPis'] != "" and $_POST['dtDataNascimento'] != "") {
        if ( SistemaLegado::comparaDatas($_POST['dtDataNascimento'],$_POST['dtCadastroPis']) ) {
            $stJs .= "f.dtCadastroPis.value = '';       \n";
            $stJs .= "alertaAviso('Data de Emissão (".$_POST['dtCadastroPis'].") não pode ser anterior a Data de Nascimento (".$_POST['dtDataNascimento'].")', 'form', 'erro', '".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function validaNumeroPis()
{
    $stJs = "";

    if (trim($_POST['stPisPasep'])!="" and !checkPIS($_POST['stPisPasep'], false)) {
        $stJs .= "f.stPisPasep.value = '';       \n";
        $stJs .= "alertaAviso('Campo PIS/PASEP da guia Documentação é inválido(".$_POST['stPisPasep'].")', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function validaCTPS()
{
    $obErro = new erro;
    if ($_POST['inNumeroCTPS'] == "") {
        $obErro->setDescricao("Campo Número da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stSerieCTPS'] == "" ) {
        $obErro->setDescricao("Campo Série da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['dtDataCTPS'] == "" ) {
        $obErro->setDescricao("Campo Data de Emissão da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stOrgaoExpedidorCTPS'] == "" ) {
        $obErro->setDescricao("Campo Órgão Expedidor da guia Documentação inválido!()");
    }
    if ( !$obErro->ocorreu() and $_POST['stSiglaUF'] == "" ) {
        $obErro->setDescricao("Campo UF da guia Documentação inválido!()");
    }

    return $obErro;
}

function incluirCTPS()
{
    $obErro = validaCTPS();
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
        $rsRecordSet->preenche( $arCTPS );
        $rsRecordSet->setUltimoElemento();
        $inUltimoId = $rsRecordSet->getCampo("inId");
        if ($inUltimoId < 0 or $inUltimoId === "") {
            $inProxId = 0;
        } else {
            $inProxId = $inUltimoId + 1;
        }
        $ultimaDataIncluida = $rsRecordSet->getCampo("dtDataCTPS");
        if ( SistemaLegado::comparaDatas($ultimaDataIncluida,$_REQUEST["dtDataCTPS"]) && (count (Sessao::read("CTPS")) > 0 )) {
            $obErro->setDescricao("A data informada deve ser maior que o da última data cadastrada.");
        }
    }
    if ( !$obErro->ocorreu() and is_array(Sessao::read('CTPS')) ) {
        foreach (Sessao::read('CTPS') as $arCTPS) {
            if( trim($arCTPS['inNumeroCTPS'])         == trim($_POST['inNumeroCTPS']) and
                trim($arCTPS['stSerieCTPS'])          == trim($_POST['stSerieCTPS'])){
                $obErro->setDescricao("Esses dados de CTPS já estão inseridos na lista de CTPS.");
                break;
            }
        }
    }
    if (!$obErro->ocorreu()) {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $rsUF = new RecordSet();
        $obTUF = new TUF();
        $stFiltro = " WHERE sw_uf.cod_uf=".$_POST['stSiglaUF'];
        $obTUF->recuperaTodos($rsUF, $stFiltro);
        if ( !$rsUF->eof()  ) {
            $sigla = $rsUF->getCampo("sigla_uf");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arCTPSs = Sessao::read("CTPS");
        $arElementos['inId']                 = $inProxId;
        $arElementos['inNumeroCTPS']         = $_POST['inNumeroCTPS'];
        $arElementos['stOrgaoExpedidorCTPS'] = $_POST['stOrgaoExpedidorCTPS'];
        $arElementos['stSerieCTPS']          = $_POST['stSerieCTPS'];
        $arElementos['dtDataCTPS']           = $_POST['dtDataCTPS'];
        $arElementos['stSiglaUF'] 		     = $sigla;
        $arElementos['inCodUF']              = $_POST['stSiglaUF'];
        $arCTPSs[]            = $arElementos;
        Sessao::write("CTPS",$arCTPSs);

        $stJs .= listarCTPS();

    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function alterarCTPS()
{
    $obErro = validaCTPS();

    if (!$obErro->ocorreu()) {
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
        $rsUF = new RecordSet();
        $obTUF = new TUF();
        $stFiltro = " WHERE sw_uf.cod_uf=".$_POST['stSiglaUF'];
        $obTUF->recuperaTodos($rsUF, $stFiltro);
        if ( !$rsUF->eof()  ) {
            $sigla = $rsUF->getCampo("sigla_uf");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $rsRecordSet = new Recordset;
        $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
        $rsRecordSet->preenche( $arCTPS );
        $rsRecordSet->setUltimoElemento();
        $ultimaDataIncluida = $rsRecordSet->getCampo("dtDataCTPS");
        if ( SistemaLegado::comparaDatas($ultimaDataIncluida,$_REQUEST["dtDataCTPS"]) && (count (Sessao::read("CTPS")) > 0 )) {
            $obErro->setDescricao("A data informada deve ser maior que o da última data cadastrada.");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $arCTPSs = Sessao::read("CTPS");
        $arElementos['inId']                 = Sessao::read('inId');
        $arElementos['inNumeroCTPS']         = $_POST['inNumeroCTPS'];
        $arElementos['stOrgaoExpedidorCTPS'] = $_POST['stOrgaoExpedidorCTPS'];
        $arElementos['stSerieCTPS']          = $_POST['stSerieCTPS'];
        $arElementos['dtDataCTPS']           = $_POST['dtDataCTPS'];
        $arElementos['stSiglaUF'] 		     = $sigla;
        $arElementos['inCodUF']              = $_POST['stSiglaUF'];
        $arCTPSs[Sessao::read('inId')] = $arElementos;
        Sessao::write("CTPS",$arCTPSs);

        $stJs .= listarCTPS();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."', 'form', 'erro', '".Sessao::getId()."');";
    }

    return $stJs;
}

function excluirCTPS()
{
    $id = $_GET['inLinha'];
    $_id = 0;
    $arCTPSs = Sessao::read("CTPS");
    while ( list( $arId ) = each( $arCTPSs ) ) {
        if ($arCTPSs[$arId]["inId"] != $id) {

            $arElementos['inId']                 = $_id;
            $arElementos['inNumeroCTPS']         = $arCTPSs[$arId]['inNumeroCTPS'];
            $arElementos['stOrgaoExpedidorCTPS'] = $arCTPSs[$arId]['stOrgaoExpedidorCTPS'];
            $arElementos['stSerieCTPS']          = $arCTPSs[$arId]['stSerieCTPS'];
            $arElementos['dtDataCTPS']           = $arCTPSs[$arId]['dtDataCTPS'];
            $arElementos['stSiglaUF']            = $arCTPSs[$arId]['stSiglaUF'];
            $arElementos['inCodUF']              = $arCTPSs[$arId]['inCodUF'];
            $arTMP[] = $arElementos;
            $_id++;
        }
    }
    Sessao::write('CTPS',$arTMP);

    $stJs .= listarCTPS();

    return $stJs;
}

function limparCTPS()
{
    $stJs  = "f.inNumeroCTPS.value                      = '';\n";
    $stJs .= "f.stOrgaoExpedidorCTPS.value              = '';\n";
    $stJs .= "f.stSerieCTPS.value                       = '';\n";
    $stJs .= "f.dtDataCTPS.value                        = '';\n";
    $stJs .= "f.stSiglaUF.value                         = '';\n";

    return $stJs;
}

function listarCTPS()
{
    $rsRecordSet = new Recordset;
    $arCTPS      = ( is_array( Sessao::read('CTPS') ) ) ? Sessao::read('CTPS') : array();
    $rsRecordSet->preenche( $arCTPS );
    $stHtml = "";
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Dados de CTPS" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Número" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Série" );
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Data emissão" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Orgão expedidor" );
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "UF" );
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inNumeroCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stSerieCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dtDataCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stOrgaoExpedidorCTPS" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stSiglaUF" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "ALTERAR" );
        $obLista->ultimaAcao->setFuncao(true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('montaAlterarCTPS',2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao(true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('excluirCTPS',2);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();
        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }
    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('btnAlterar').disabled = true;               \n";
    $stJs .= "d.getElementById('btnIncluir').disabled = false;              \n";
    $stJs .= "d.getElementById('spnCTPS').innerHTML   = '".$stHtml."';";
    $stJs .= limparCTPS();

    return $stJs;

}

function montaAlterarCTPS()
{
    $id = $_GET['inLinha'];
    $arCTPSs = Sessao::read('CTPS');
    while ( list( $arId ) = each( $arCTPSs ) ) {

        if ($arCTPSs[$arId]['inId'] == $id) {
            $numero = trim($arCTPSs[$arId]['inNumeroCTPS']);
            $orgao  = trim($arCTPSs[$arId]['stOrgaoExpedidorCTPS']);
            $serie  = trim($arCTPSs[$arId]['stSerieCTPS']);
            $data   = trim($arCTPSs[$arId]['dtDataCTPS']);
            $uf     = trim($arCTPSs[$arId]['inCodUF']);

            $stJs .= "f.inNumeroCTPS.value = '$numero';";
            $stJs .= "f.stOrgaoExpedidorCTPS.value = '$orgao';";
            $stJs .= "f.stSerieCTPS.value = '$serie';";
            $stJs .= "f.dtDataCTPS.value = '$data';";
            $stJs .= "f.stSiglaUF.value = '$uf';";
            $stJs .= "d.getElementById('btnAlterar').disabled = false;      \n";
            $stJs .= "d.getElementById('btnIncluir').disabled = true;      \n";

            Sessao::write('inId',$id);
       }
    }

    return $stJs;
}

function listarAlterarCTPS()
{
    GLOBAL $inCodServidor;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addRPessoalCTPS();
    $stMensagem = false;
    $obRPessoalServidor->setCodServidor( $inCodServidor );
    $obRPessoalServidor->roRPessoalCTPS->listarCTPS( $rsCTPS, $boTransacao );

    $arCTPS = array();
    $inId = 0;
    while ( !$rsCTPS->eof()  ) {
        $arTemp["inId"]                 = $inId;
        $arTemp["inNumeroCTPS"]         = trim($rsCTPS->getCampo("numero"));
        $arTemp["stOrgaoExpedidorCTPS"] = $rsCTPS->getCampo("orgao_expedidor");
        $arTemp["stSerieCTPS"]          = $rsCTPS->getCampo("serie");
        $arTemp["dtDataCTPS"]           = $rsCTPS->getCampo("dt_emissao");
        $arTemp["stSiglaUF"]            = $rsCTPS->getCampo("sigla");
        $arTemp["inCodUF"]              = $rsCTPS->getCampo("uf_expedicao");

        $arCTPS[]                       = $arTemp;
        $inId++;
        $rsCTPS->proximo();
    }

    Sessao::write('CTPS',$arCTPS);

    $stJs .= listarCTPS();

    return $stJs;
}

function validaDataEmissaoCTPS()
{
    $stValida = comparaComDataNascimento("dtDataCTPS","Data de Emissão");
    if ($stValida != "") {
        $stJs .= $stValida;
    } else {
        if ( $_POST['dtDataCTPS'] != "" and SistemaLegado::comparaDatas($_POST['dtDataNascimento'],$_POST['dtDataCTPS']) ) {
            $stJs .= "f.dtDataCTPS.value = '';  \n";
            $stJs .= "alertaAviso('Data de Emissão (".$_POST['dtDataCTPS'].") não pode ser anterior a Data de Nascimento (".$_POST['dtDataNascimento'].")', 'form', 'erro', '".Sessao::getId()."');";
        }
    }

    return $stJs;
}

function checkPIS($pis, $checkZero=true)
{
    $pis = trim(preg_replace('/[^0-9]/', '', $pis));

    if ($pis === "00000000000" && $checkZero == false) {
        return true;
    }

    if (strlen($pis) != 11 || intval($pis) == 0) {
        return false;
    }

    for ($d = 0, $p = 2, $c = 9; $c >= 0; $c--, ($p < 9) ? $p++ : $p = 2) {
        $d += $pis[$c] * $p;
    }

    return ($pis[10] == (((10 * $d) % 11) % 10));
}

switch ($_POST["stCtrl"]) {
    case "validaNumeroPis":
        $stJs .= validaNumeroPis();
        break;
    case "validaDataCadPis":
        $stJs .= validaDataCadPis();
        break;
    case "incluirCTPS":
        $stJs .= incluirCTPS();
        break;
    case "alterarCTPS":
        $stJs .= alterarCTPS();
        break;
    case "excluirCTPS":
        $stJs .= excluirCTPS();
        break;
    case "limparCTPS":
        $stJs .= limparCTPS();
        break;
    case "montaAlterarCTPS":
        $stJs .= montaAlterarCTPS();
        break;
    case "validaDataEmissaoCTPS":
        $stJs .= validaDataEmissaoCTPS();
        break;
}

if ($stJs) {
    sistemaLegado::executaFrameOculto($stJs);
}

?>
