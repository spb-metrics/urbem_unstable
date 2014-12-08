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
    * Página de Processamento do Configuração do Cálculo de Benefícios
    * Data de Criação: 27/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Casos de uso: uc-04.05.45
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoBeneficioEvento.class.php"                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTipoEventoBeneficio.class.php"                    );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoBeneficio.class.php"                  );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                        );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoBeneficio";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoConfiguracaoBeneficio = new TFolhaPagamentoConfiguracaoBeneficio;
$obTFolhaPagamentoBeneficioEvento       = new TFolhaPagamentoBeneficioEvento;
$obTFolhaPagamentoBeneficioEvento->obTFolhaPagamentoConfiguracaoBeneficio = &$obTFolhaPagamentoConfiguracaoBeneficio;
$obTFolhaPagamentoTipoEventoBeneficio   = new TFolhaPagamentoTipoEventoBeneficio;
$obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
switch ($stAcao) {
    case "alterar":
        Sessao::setTrataExcecao(true);
        $stFiltro = " WHERE cod_beneficio = 1";
        $obTFolhaPagamentoTipoEventoBeneficio->recuperaTodos($rsTipoEventoBeneficio,$stFiltro);
        $obTFolhaPagamentoConfiguracaoBeneficio->inclusao();

        $stFiltro = " WHERE codigo = '".$_POST["inCodigoEvento"]."'";
        $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

        $obTFolhaPagamentoBeneficioEvento->setDado("cod_evento" ,$rsEvento->getCampo("cod_evento"));
        $obTFolhaPagamentoBeneficioEvento->setDado("cod_tipo"   ,$rsTipoEventoBeneficio->getCampo("cod_tipo"));
        $obTFolhaPagamentoBeneficioEvento->inclusao();
        Sessao::encerraExcecao();
        $stDescricaoEvento = $_POST['inCodigoEvento'] ."-". $rsEvento->getCampo("descricao");
        sistemaLegado::alertaAviso($pgForm,"Evento $stDescricaoEvento","incluir","aviso", Sessao::getId(), "../");
    break;
}
?>
