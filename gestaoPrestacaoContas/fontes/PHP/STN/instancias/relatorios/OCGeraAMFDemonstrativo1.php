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
    * Página Oculto : AMF Demonstrativo 1
    * Data de Criação   : 13/07/2009

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GF_LDO_MAPEAMENTO.'TLDOIndicadores.class.php';

$preview = new PreviewBirt(6, 36, 39);
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

$stAcao = $request->get('stAcao');

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado('exercicio', Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaEntidades($rsEntidade);

$stExercicio = $_REQUEST['stExercicio'];
$obTLDOIndicadores = new TLDOIndicadores;
for ($inCount = 0; $inCount <= 2; $inCount++) {
    $stExercicioFiltro = $stExercicio+$inCount;
    $stFiltro  = " WHERE exercicio = '".$stExercicioFiltro."'";
    $stFiltro .= "   AND cod_tipo_indicador = ".$_REQUEST['inCodPIB'];
    $obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);

    if ($rsIndicadores->getNumLinhas() < 1) {
        SistemaLegado::alertaAviso('FLModelosAMF.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Não existe PIB cadastrado para o exercício '.$stExercicioFiltro.'!','','aviso', Sessao::getId(), '../');
    }

    $stFiltro  = " WHERE exercicio = '".$stExercicioFiltro."'";
    $stFiltro .= "   AND cod_tipo_indicador = ".$_REQUEST['inCodInflacao'];
    $obTLDOIndicadores->recuperaTodos($rsIndicadores, $stFiltro);
    if ($rsIndicadores->getNumLinhas() < 1) {
        SistemaLegado::alertaAviso('FLModelosAMF.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Não existe Inflação cadastrado para o exercício '.$stExercicioFiltro.'!','','aviso', Sessao::getId(), '../');
    }

}

$preview->addParametro('ano_referencia', $_REQUEST['stExercicio']);
$preview->addParametro('cod_ppa'       , $_REQUEST['inCodPPA']);
$preview->addParametro('cod_pib'       , $_REQUEST['inCodPIB']);
$preview->addParametro('cod_inflacao'  , $_REQUEST['inCodInflacao']);

while (!$rsEntidade->EOF()) {
    if (eregi('prefeitura.*', $rsEntidade->getCampo('nom_cgm')) || (count($_REQUEST['inCodEntidade']) > 1)) {
        $preview->addParametro('poder'       , 'Executivo');
        $preview->addParametro('nom_entidade', $rsEntidade->getCampo('nom_cgm'));
    }
    $rsEntidade->proximo();
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();
