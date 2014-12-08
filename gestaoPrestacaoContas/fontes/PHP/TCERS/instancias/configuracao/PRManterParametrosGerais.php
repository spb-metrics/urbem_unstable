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
set_time_limit(0);
/**
    * Página de Formulario de Ajustes Gerais Exportacao - TCE-RS
    * Data de Criação   : 11/07/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 59612 $
    $Name$
    $Author: gelson $
    $Date: 2014-09-02 09:00:51 -0300 (Ter, 02 Set 2014) $

    * Casos de uso: uc-02.08.15
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:20  hboaventura
Ticket#10234#

Revision 1.3  2006/07/19 17:51:27  cako
Bug #6013#

Revision 1.2  2006/07/19 17:49:53  cako
Bug #6013#

Revision 1.1  2006/07/17 14:30:48  cako
Bug #6013#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCERS_MAPEAMENTO."TExportacaoTCERSConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterParametrosGerais";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTExportacao = new TExportacaoTCERSConfiguracao();

    $obErro = new Erro;

    $obTransacao = new Transacao;
    $obTransacao->begin();
    $boTransacao = $obTransacao->getTransacao();

        $obTExportacao->setDado("parametro", "orgao_unidade_prefeitura" );
        $obTExportacao->setDado( "valor", $_POST['inCodExecutivo'] );
        $obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTExportacao->alteracao( $boTransacao );
        } else $obErro = $obTExportacao->inclusao( $boTransacao );

        $obTExportacao->setDado("parametro", "orgao_unidade_camara" );
        $obTExportacao->setDado( "valor", $_POST['inCodLegislativo'] );
        $obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTExportacao->alteracao( $boTransacao );
        } else $obErro = $obTExportacao->inclusao( $boTransacao );

        $obTExportacao->setDado("parametro", "orgao_unidade_rpps" );
        $obTExportacao->setDado( "valor", ($_POST['inCodRPPS']?$_POST['inCodRPPS']:$_POST['inCodExecutivo']) );
        $obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTExportacao->alteracao( $boTransacao );
        } else $obErro = $obTExportacao->inclusao( $boTransacao );

        $obTExportacao->setDado("parametro", "orgao_unidade_outros" );
        $obTExportacao->setDado( "valor", ($_POST['inCodOutros']?$_POST['inCodOutros']:$_POST['inCodExecutivo']) );
        $obErro = $obTExportacao->recuperaPorChave( $rsRecordSet, $boTransacao );
        if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
            $obErro = $obTExportacao->alteracao( $boTransacao );
        } else $obErro = $obTExportacao->inclusao( $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $obTransacao->commitAndClose();
    } else {
        $obTransacao->rollbackAndClose();
    }

    if ( !$obErro->ocorreu() ) {
        SistemaLegado::alertaAviso($pgForm,"parâmetros atualizados", "incluir", "aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }

?>
