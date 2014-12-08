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
    * Página de Filtro para exportação de ITBI/IPTU
    * Data de Criação   : 05/06/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso: uc-06.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_ITBI_IPTU_NEGOCIO."RExportacaoGT.class.php";

$stPrograma = "ExportacaoGT";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

SistemaLegado::BloqueiaFrames(true, false);

$obRExportacaoGT = new RExportacaoGT;
$obRExportacaoGT->setSemestre($_REQUEST['cmbSemestre']);

if (in_array("iptu", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLIPTU();
}
if (in_array("itbi_urbano", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLITBIUrbano();
}
if (in_array("itbi_rural", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLITBIRural();
}
if (in_array("pl_valores_urbanos_itbi_iptu", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLPVUrbano();
}
if (in_array("pl_valores_rurais_itbi", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLPVRural();
}
if (in_array("cadastro_logradouro", $_REQUEST['arArquivos'])) {
    $obRExportacaoGT->geraDocumentoXMLCadastroLograodouros();
}

if (count($_REQUEST['arArquivos']) > 1) {
    $obRExportacaoGT->doZipArquivos();
}

SistemaLegado::LiberaFrames(true, false);

SistemaLegado::executaFrameOculto("window.location = '".$pgList."'");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
