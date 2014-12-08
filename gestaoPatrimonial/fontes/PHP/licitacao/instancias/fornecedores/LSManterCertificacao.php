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
    * Pagina de lista para Cadastro/Certificação
    * Data de Criação   : 03/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.13

    $Id: LSManterCertificacao.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( TLIC."TLicitacaoParticipanteCertificacao.class.php" );

$stPrograma = "ManterCertificacao";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

$pgGera       = "OCGeraCertificadoFornecedor.php";

$obTLicitacaoParticipanteCertificacao = new TLicitacaoParticipanteCertificacao();

$stCaminho = CAM_GP_LIC_INSTANCIAS."fornecedores/";

$stAcao = $request->get('stAcao');

switch ($stAcao) {
    case 'alterar':
        $pgProx = $pgForm; break;
    case 'excluir':
        $pgProx = $pgProc; break;
    case 'anular':
        $pgProx = $pgForm; break;
    case 'consultar':
        $pgProx = $pgForm; break;
}

$filtro = '';
if ($_REQUEST['inCodFornecedor'] || $_REQUEST['inNumCertificacao']) {
    foreach ($_REQUEST as $key => $value) {
        #sessao->transf4['filtro'][$key] = $value;
        $filtro[$key] = $value;
    }
} else {
    if ( Sessao::read('filtro') ) {
        foreach ( Sessao::read('filtro') as $key => $value ) {
            $_REQUEST[$key] = $value;
        }
    }
    Sessao::write('paginando' , true);
}

Sessao::write('filtro' , $filtro);

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

if ($_REQUEST['inCodFornecedor']) {
    $obTLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
}

if ($_REQUEST['inNumCertificacao']) {
    $inNumCertificacao = substr( $_REQUEST['inNumCertificacao'],0,6 );
    $obTLicitacaoParticipanteCertificacao->setDado( 'num_certificacao', $inNumCertificacao );
}

$stFiltro = "";
$stLink   = "";

$stLink .= "&stAcao=".$stAcao;

$obTLicitacaoParticipanteCertificacao->recuperaListaCertificacao( $rsParticipantes,'','order by cgm.nom_cgm' );

while ( !$rsParticipantes->eof() ) {
    $rsParticipantes->setCampo( 'num_certificacao', str_pad( $rsParticipantes->getCampo('num_certificacao'), 6, "0", STR_PAD_LEFT) );
    $rsParticipantes->proximo();
}

$rsParticipantes->setPrimeiroElemento();

$obLista = new Lista();
$obLista->setRecordSet( $rsParticipantes );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Certificação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Fornecedor" );
$obLista->ultimoCabecalho->setWidth( 40 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[num_certificacao]/[exercicio]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[cgm_fornecedor]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addAcao();
if ( ( $stAcao == "alterar" ) || ( $stAcao == "consultar" ) ) {
    $obLista->ultimaAcao->setAcao( $stAcao );
} elseif ($stAcao == "reemitir") {
    $obLista->ultimaAcao->setAcao( "selecionar" );
}

$obLista->ultimaAcao->addCampo("&inCodFornecedor" , "cgm_fornecedor" );
$obLista->ultimaAcao->addCampo("&stExercicio", "exercicio" );
$obLista->ultimaAcao->addCampo("&inNumCertificacao", "num_certificacao" );
$obLista->ultimaAcao->addCampo("&dtDataRegistro", "dt_registro" );
$obLista->ultimaAcao->addCampo("&dtDataVigencia", "final_vigencia" );
$obLista->ultimaAcao->addCampo("&stObservacao", "observacao" );
$obLista->ultimaAcao->addCampo("&stNomFornecedor", "nom_cgm" );
$obLista->ultimaAcao->addCampo("&stDescQuestao" ,"");

if ( ($stAcao == 'alterar') || ($stAcao == 'consultar') ) {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?".Sessao::getId().$stLink );
}
if ($stAcao == "reemitir") {
    $obLista->ultimaAcao->setLink( $stCaminho.$pgGera."?".Sessao::getId().$stLink );
}

$obLista->commitAcao();

$obLista->setAjuda("UC-03.05.13");
$obLista->Show();
