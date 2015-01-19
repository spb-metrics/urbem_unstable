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
    * Pagina de Processamento do Centro de Custo
    * Data de Criação   : 22/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.07

    $Id: PRManterCentroCusto.php 61395 2015-01-13 18:48:04Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoCentroDeCustos.class.php"    );
include_once (CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoCentroCusto.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterCentroCusto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

$obRegra = new RAlmoxarifadoCentroDeCustos;
switch ($stAcao) {
    case "incluir":

        $obRegra->setDescricao      ( $_POST['stDescricao']  );
        $obRegra->roUltimaEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
        $obRegra->roUltimaEntidade->setExercicio ( Sessao::getExercicio() );
        $obRegra->obRCGMResponsavel->setNumCGM ( $_POST['inCGMResponsavel'] );
        $obRegra->setVigencia      ( $_POST['dtDataVigencia']  );

        foreach ( Sessao::read('arDotacoes')  as $arTemp ) {
            $obRegra->addDotacao();
            $obRegra->roUltimaDotacao->setCodDespesa( $arTemp['cod_despesa'] );
        }

        $obErro = $obRegra->incluir();
        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm, $obRegra->getCodigo()." - ".$obRegra->getDescricao(),"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;

    case "alterar":

        $obRegra->setCodigo                           ( $_POST['inCodigo']  );
        $obRegra->setDescricao                        ( $_POST['stDescricao']  );
        $obRegra->roUltimaEntidade->setCodigoEntidade ( $_POST['inCodEntidade'] );
        $obRegra->roUltimaEntidade->setExercicio      ( Sessao::getExercicio() );
        $obRegra->obRCGMResponsavel->setNumCGM        ( $_POST['inCGMResponsavel'] );
        $obRegra->setVigencia                         ( $_POST['dtDataVigencia']  );

        if (count(Sessao::read('arDotacoes')) > 0) {
            foreach ( Sessao::read('arDotacoes') as $arTemp ) {
                $obRegra->addDotacao();
                $obRegra->roUltimaDotacao->setCodDespesa( $arTemp['cod_despesa'] );
            }
        }

        $obErro = $obRegra->alterar();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar", $obRegra->getCodigo()." - ".$obRegra->getDescricao(),"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;

   case "excluir":
        $stFiltro = "";
    $obErro = new Erro;
    $obTAlmoxarifadoCentroCusto = new TAlmoxarifadoCentroCusto;
    if (isset($_REQUEST['inCodigo']) != '') {
        $stFiltro = " AND centro_custo.cod_centro = " . $_REQUEST['inCodigo'] . " \n";
    }
    $obErro = $obTAlmoxarifadoCentroCusto->recuperaPermissaoUsuarioExcluir($rsRecordSet, $stFiltro);

    if ($rsRecordSet->getNumLinhas() < 0) {
        $obErro->setDescricao('Centro não pode ser excluído!');
    }
        if ( !$obErro->ocorreu() ) {
            $obRegra->setCodigo    ( $_REQUEST['inCodigo'] );
            $obRegra->setDescricao ( $_REQUEST['stDescricao'] );
            $obErro = $obRegra->excluir();
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", $obRegra->getCodigo()." - ".$obRegra->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","O Item ".$obRegra->inCodigo." - ".$obRegra->getDescricao()." já está sendo usado pelo sistema","n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
