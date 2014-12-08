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
    * Página de Formulario de Inclusao/Alteracao de Conta Corrente

    * Data de Criação   : 07/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: LSProcurarConta.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_MON_NEGOCIO.'RMONContaCorrente.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once $pgJs;

$stCaminho   = CAM_GT_MON_INSTANCIAS."contaCorrente/";
$obRMONConta = new RMONContaCorrente;
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}
//Define arquivos PHP para cada acao
switch ($_REQUEST['stAcao']) {
    case 'alterar'  : $pgProx = $pgForm; break;
    case 'excluir'  : $pgProx = $pgProc; break;
    case 'baixar'   : $pgProx = $pgFormBaixar; break;
    DEFAULT         : $pgProx = $pgForm;
}

//MANTEM FILTRO E PAGINACAO
$boPaginando = (isset($_REQUEST['paginando'])? $_REQUEST['paginando'] : Sessao::read('paginando'));

$arFiltro = Sessao::read('filtro');
if ($_POST || $_GET['pg'] || !$boPaginando) {
    foreach ($_REQUEST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('paginando',$boPaginando);
} else {
    foreach ((array) $arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
    $_GET['pg']  = $_REQUEST['pg' ];
    $_GET['pos'] = $_REQUEST['pos'];
}

$stLink .= '&stAcao='.$_REQUEST['stAcao'];

//USADO QUANDO EXISTIR FILTRO NA FL O VAR LINK DEVE SER RESETADA
if (is_array($link)) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
}

Sessao::write('stLink', $stLink);
Sessao::write('link', $link);

//MONTA O FILTRO
if ($_REQUEST["inCodBancoTxt"]) {
    $obRMONConta->obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST['inCodBancoTxt'] );
}
if ($_REQUEST["stNumAgencia"]) {
    $obRMONConta->obRMONAgencia->setNumAgencia( $_REQUEST['stNumAgencia'] );
}
if ($_REQUEST["stNumeroConta"]) {
    $obRMONConta->setNumeroConta( $_REQUEST['stNumeroConta'] );
}
if ($_REQUEST['boVinculoPlanoBanco']) {
    $obRMONConta->boVinculoPlanoBanco = true;
}

if ($_REQUEST['inCodEntidadeVinculo'] != '') {
    $obRMONConta->inCodEntidadeVinculo = $_REQUEST['inCodEntidadeVinculo'];
}

$stLink .= '&stAcao='.$stAcao;
$obRMONConta->listarContaCorrente($rsLista);
$obLista = new Lista;
$obLista->setRecordSet( $rsLista );
$obLista->setTitulo('Registros de Conta Corrente');
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Banco ');
$obLista->ultimoCabecalho->setWidth(30);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Agência ');
$obLista->ultimoCabecalho->setWidth(30);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Conta Corrente ');
$obLista->ultimoCabecalho->setWidth(60);
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo('[num_banco] - [nom_banco]');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('[num_agencia] - [nom_agencia]');
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo('num_conta_corrente');
$obLista->commitDado();

$obLista->addAcao();
$_REQUEST['stAcao'] = 'SELECIONAR';
$obLista->ultimaAcao->setAcao  ($_REQUEST['stAcao']);
$obLista->ultimaAcao->setFuncao(true);
$obLista->ultimaAcao->setLink  ('JavaScript:window.close();Insere();');
$obLista->ultimaAcao->addCampo ('1', 'num_conta_corrente');
$obLista->ultimaAcao->addCampo ('2', 'nom_banco');
$obLista->ultimaAcao->addCampo ('3', 'num_agencia');
$obLista->ultimaAcao->addCampo ('4', 'nom_agencia');

$obLista->commitAcao();
$obLista->show();

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName ('campoNum');
$obHdnCampoNum->setValue($_REQUEST['campoNum']);

$obFormulario = new Formulario;
$obFormulario->addHidden( $obHdnCampoNum );

if (!$_REQUEST['boVinculoPlanoBanco']) {
    $obBtnFiltro = new Button;
    $obBtnFiltro->setName             ('btnFiltrar');
    $obBtnFiltro->setValue            ('Filtrar');
    $obBtnFiltro->setTipo             ('button');
    $obBtnFiltro->obEvento->setOnClick('filtrar();');
    $obBtnFiltro->setDisabled         (false);
    $botoes = array($obBtnFiltro);
    $obFormulario->defineBarra($botoes, 'left', '');
}

$obFormulario->show();
