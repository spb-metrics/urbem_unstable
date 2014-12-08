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
* Arquivo instância para popup de Servidor
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-13 16:40:12 -0300 (Qua, 13 Jun 2007) $

Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once( CAM_INCLUDES."IncludeClasses.inc.php" );
include_once( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php"       );

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarCgm";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stFncJavaScript  = " function insereCGM(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$_REQUEST["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".value = sNum; \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNum"].".focus(); \n";
//$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$_REQUEST["nomForm"].".".$_REQUEST["campoNom"].".value = sNom; \n";
$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " }                          \n";

$obTCGM = new TCGM;
$obMascara = new Mascara;
$stFiltro = "";
$stLink   = "";

//Definição do filtro de acordo com os valores informados no FL
$stLink .= "&stTipoPessoa=".$_REQUEST["stTipoPessoa"];

if ($_REQUEST["campoNom"]) {
    $stLink .= '&campoNom='.$_REQUEST['campoNom'];
}
if ($_REQUEST["nomForm"]) {
    $stLink .= '&nomForm='.$_REQUEST['nomForm'];
}
if ($_REQUEST["campoNum"]) {
    $stLink .= '&campoNum='.$_REQUEST['campoNum'];
}
if ($_REQUEST["stTipoPessoa"] == "F") {
    $stFiltro .= " AND CGM.numcgm IN (select numcgm from sw_cgm_pessoa_fisica ) ";
}
if ($_REQUEST["stTipoPessoa"] == "J") {
    $stFiltro .= " AND CGM.numcgm IN (select numcgm from sw_cgm_pessoa_juridica ) ";
}
if ( $request->get("stNomeCgm") ) {
//     $stFiltro .= " AND lower(nom_cgm) like lower('".$_REQUEST["stNomeCgm"]."%') ";
    $stFiltro .= " AND lower(nom_cgm) like lower('".$_REQUEST["stNomeCgm"]."%')||'%' ";
    $stLink   .= "&stNomeCgm=".$_REQUEST["stNomeCgm"];
}
if ( $request->get("stCPF") ) {
    $inCPF = $_REQUEST["stCPF"];
    $obMascara->desmascaraDado( $inCPF );
    $stFiltro .= " AND CGM.numcgm in ( select numcgm from sw_cgm_pessoa_fisica where cpf = '".$inCPF."') ";
    $stLink   .= "&stCPF=".$_REQUEST["stCPF"];
}

// SQL Comum ao case 1 e case 2, filtra servidores com contrato.
$stSQL  = " AND cgm.numcgm IN (                               \n";
$stSQL .= "SELECT ps.numcgm                                   \n";
$stSQL .= "  FROM pessoal.servidor ps                     \n";
$stSQL .= "     , pessoal.servidor_contrato_servidor pscs \n";
$stSQL .= " WHERE ps.cod_servidor = pscs.cod_servidor)        \n";

//Filtro setado na pagina que abre a popup
$stLink .= "&inFiltro=".$_REQUEST['inFiltro'];
switch ($_REQUEST['inFiltro']) {

    //CGMs de contratos
    case 1:
        $stFiltro .= $stSQL;
    break;
    //CGMs de contratos não rescindidos
    case 2:
        $stFiltro .= $stSQL;
        $stFiltro .= " AND cgm.numcgm NOT IN (                                           \n";
        $stFiltro .= "     SELECT                                                        \n";
        $stFiltro .= "          ps.numcgm                                                \n";
        $stFiltro .= "     FROM                                                          \n";
        $stFiltro .= "          pessoal.servidor ps                                      \n";
        $stFiltro .= "         ,pessoal.servidor_contrato_servidor pscs                  \n";
        $stFiltro .= "         ,pessoal.contrato_servidor pcs                            \n";
        $stFiltro .= "         ,pessoal.contrato_servidor_caso_causa pcscc               \n";
        $stFiltro .= "     WHERE                                                         \n";
        $stFiltro .= "             pscs.cod_servidor = ps.cod_servidor                   \n";
        $stFiltro .= "         AND pcs.cod_contrato = pscs.cod_contrato                  \n";
        $stFiltro .= "         AND pcscc.cod_contrato = pcs.cod_contrato )               \n";
    break;

    //CGMs de contratos rescindidos
    case 3:
        $stFiltro .= " AND cgm.numcgm IN (                                               \n";
        $stFiltro .= "     SELECT                                                        \n";
        $stFiltro .= "          ps.numcgm                                                \n";
        $stFiltro .= "     FROM                                                          \n";
        $stFiltro .= "          pessoal.servidor ps                                      \n";
        $stFiltro .= "         ,pessoal.servidor_contrato_servidor pscs                  \n";
        $stFiltro .= "         ,pessoal.contrato_servidor pcs                            \n";
        $stFiltro .= "         ,pessoal.contrato_servidor_caso_causa pcscc               \n";
        $stFiltro .= "     WHERE                                                         \n";
        $stFiltro .= "             pscs.cod_servidor = ps.cod_servidor                   \n";
        $stFiltro .= "         AND pcs.cod_contrato = pscs.cod_contrato                  \n";
        $stFiltro .= "         AND pcscc.cod_contrato = pcs.cod_contrato )               \n";

    break;
    //CGMs de pensionistas
    case 4:
        $stFiltro .= " AND cgm.numcgm IN (                                               \n";
        $stFiltro .= "     SELECT                                                        \n";
        $stFiltro .= "          pensionista.numcgm                                       \n";
        $stFiltro .= "     FROM                                                          \n";
        $stFiltro .= "          pessoal.pensionista                      )               \n";
        break;
}//end switch

/*if ($_REQUEST["stCNPJ"]) {
    $inCNPJ = $_REQUEST["stCNPJ"];
    $obMascara->desmascaraDado( $inCNPJ );
    $stFiltro .= " AND CGM.numcgm in ( select numcgm from ".CGM_PESSOA_JURIDICA." where cnpj = '".$inCNPJ."') ";
    $stLink   .= "&stCNPJ=".$_REQUEST["stCNPJ"];
}*/
/*
if ($_REQUEST["stNomeFantasia"]) {
    $stFiltro .= " AND CGM.numcgm in ( select numcgm from ".CGM_PESSOA_JURIDICA." where lower(nom_fantasia) like lower('%".$_REQUEST["stNomeFantasia"]."%') ) ";
    $stLink   .= "&stNomeFantasia=".$_REQUEST["stNomeFantasia"];
}
*/
/*if ($stFiltro) {
    $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
}*/

//faz busca dos CGM's utilizando o filtro setado
$stAcao = isset($stAcao) ? $stAcao : "";
$stLink .= "&stAcao=".$stAcao;
$rsLista = new RecordSet;
//$obTCGM->recuperaRelacionamento( $rsLista, $stFiltro, " ORDER BY CGM.nom_cgm" );
$obTCGM->recuperaRelacionamentoSintetico( $rsLista, $stFiltro, " ORDER BY CGM.nom_cgm" );
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Código ");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nome" );
$obLista->ultimoCabecalho->setWidth( 35 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "numcgm" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_cgm" );
$obLista->commitDado();
//$obLista->addDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereCGM();" );
$obLista->ultimaAcao->addCampo("1","numcgm");
$obLista->ultimaAcao->addCampo("2","nom_cgm");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript );
$obFormulario->show();
?>
