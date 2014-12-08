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
  * Página de Formulario de Configuração de Detalhameto da Adesão a Registro de Preços TCE/MG
  * Data de Criação   : 17/01/2014

  * @author Analista: Eduardo Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes

  * @ignore
  *
  * $Id: LSManterRegistroPreco.php 59612 2014-09-02 12:00:51Z gelson $
  *
  * $Revision: 59612 $
  * $Author: gelson $
  * $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGProcessoAdesaoRegistroPrecos.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroPreco";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once $pgJS;

$stCaminho     = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

$stAcao        = $request->get('stAcao');
$inCodEntidade = $request->get('inCodEntidade');

if ($stAcao == 'alterar') {
    $stCaminho = $stCaminho.$pgForm;
} else {
    $stCaminho = $stCaminho.$pgProc;
}

if (empty($inCodEntidade)){
    SistemaLegado::alertaAviso($pgFilt."?".Sessao::getId()."&stAcao=".$stAcao,"O campo entidade deve ser informado.","","aviso", Sessao::getId(), "../");
}

$rsProcessoAdesao = new RecordSet();
$obTTCEMGProcessoAdesaoRegistroPrecos = new TTCEMGProcessoAdesaoRegistroPrecos();
$obTTCEMGProcessoAdesaoRegistroPrecos->setDado('cod_entidade' , $inCodEntidade);
$obTTCEMGProcessoAdesaoRegistroPrecos->recuperaListaProcesso( $rsProcessoAdesao );

$obLista = new Lista;
$obLista->setRecordSet( $rsProcessoAdesao );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numero do Processo");
$obLista->ultimoCabecalho->setWidth( 6 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data de Abertura" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Número Processo Licitatório" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modalidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nro. Modalidade" );
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[codigo_processo_adesao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[data_abertura_processo_adesao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[codigo_processo_licitacao]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[modalidade]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[numero_modalidade]" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodEntidade"             , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inNroProcessoAdesao"       , "numero_processo_adesao");
$obLista->ultimaAcao->addCampo("&stExercicioProcessoAdesao" , "exercicio_adesao");
$obLista->ultimaAcao->setLink( $stCaminho."?".Sessao::getId()."&stAcao=".$stAcao );
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario();
$obFormulario->show();

?>