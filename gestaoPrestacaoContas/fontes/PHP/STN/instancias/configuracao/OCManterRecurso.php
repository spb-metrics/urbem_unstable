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
    * Página de Processamento do frame Oculto
    * Data de Criação  : 15/05/2008

    * @author Analista Gelson W. Golçalves
    * @author Desenvolvedor Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    * $Id: OCManterRecurso.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso : uc-06.01.09

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CAM_GF_ORC_COMPONENTES."ISelectMultiploRecurso.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "JS".$stPrograma.".js";

switch ($_REQUEST['stCtrl']) {
case 'montaDadosUnidade':
    require_once CAM_GF_ORC_COMPONENTES."ISelectUnidade.class.php";
    $obInCodUnidade = new ISelectUnidade;
    $obInCodUnidade->setExercicio( Sessao::getExercicio() );
    $obInCodUnidade->setNumOrgao( $_REQUEST['inCodOrgao'] );
    $rsUnidade = $obInCodUnidade->getRecordSet();

    $js = " var arUnidade = $('inCodUnidade').options;";
    $js .= "\n for (var chave in arUnidade) {
        arUnidade[chave] = null;
    }";
    $js .= "\n arUnidade[0] = new Option('Selecione', '', '');";
    $inCount = 1;
    $arNumUnidade = array();
    while ( !$rsUnidade->eof() ) {
        $js .= "\n arUnidade[".$inCount."] = new Option('".$rsUnidade->getCampo('num_unidade')." - " . $rsUnidade->getCampo('nom_unidade') . "', '".$rsUnidade->getCampo('num_unidade')."', '');";
        $inCount++;
        array_push($arNumUnidade, $rsUnidade->getCampo('num_unidade'));
        $rsUnidade->proximo();
    }
    $inCodigoUnidades = implode (",", $arNumUnidade);
    $js .= "\n arUnidade[".$inCount."] = new Option('Todos', '0', '');";
    $js .= "\n f.inCodigosUnidade.value = '".$inCodigoUnidades."';";

    echo $js;

    break;

case "montaDadosRecurso":
    $js = " var arDisponivel = $('inCodRecursoDisponivel').options;";
    $js .= "\n for (var chave in arDisponivel) {
        arDisponivel[chave] = null;
    }";
    $js .= "\n var arSelecionado = $('inCodRecursoSelecionado').options;";
    $js .= "\n for (var chave in arSelecionado) {
        arSelecionado[chave] = null;
    }";

    if ($_REQUEST['stAcao'] == '1') {
        $js .= "\n var arDisponivel2 = $('inCodRecursoDisponivel2').options;";
        $js .= "\n for (var chave in arDisponivel2) {
            arDisponivel2[chave] = null;
        }";
        $js .= "\n var arSelecionado2 = $('inCodRecursoSelecionado2').options;";
        $js .= "\n for (var chave in arSelecionado2) {
            arSelecionado2[chave] = null;
        }";
    }

    if ($_REQUEST['inCodEntidade'] != "" && $_REQUEST['inCodOrgao'] != "" && $_REQUEST['inCodUnidade'] != "") {
        $pos = strpos($_REQUEST['inCodUnidade'], ",");

        if ($pos === false) {
            $stFiltro = " AND NOT EXISTS( SELECT 1
                                            FROM   stn.vinculo_recurso, stn.vinculo_stn_recurso
                                           WHERE  recurso.exercicio = vinculo_recurso.exercicio
                                             AND  recurso.cod_recurso = vinculo_recurso.cod_recurso
                                             AND  vinculo_recurso.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                             AND  vinculo_recurso.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                             AND  vinculo_recurso.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                             AND  vinculo_recurso.cod_vinculo  = " . $_REQUEST['stAcao'] . "
                                             AND  vinculo_recurso.cod_vinculo  = vinculo_stn_recurso.cod_vinculo
                                             AND  vinculo_recurso.cod_tipo = 2)";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 = substr_replace($stFiltro, "1)", -2);
            }

            $stFiltro .= " AND EXISTS ( SELECT 1
                                         FROM orcamento.despesa
                                        WHERE despesa.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                          AND despesa.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                          AND despesa.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                          AND despesa.cod_recurso = recurso.cod_recurso
                                          AND despesa.exercicio   = recurso.exercicio)";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 .= " AND EXISTS ( SELECT 1
                                         FROM orcamento.despesa
                                        WHERE despesa.cod_entidade = " . $_REQUEST['inCodEntidade'] . "
                                          AND despesa.num_unidade  = " . $_REQUEST['inCodUnidade'] . "
                                          AND despesa.num_orgao    = " . $_REQUEST['inCodOrgao'] . "
                                          AND despesa.cod_recurso = recurso.cod_recurso
                                          AND despesa.exercicio   = recurso.exercicio)";
            }
        } else {
            $stFiltro = " TRUE ";

            if ($_REQUEST['stAcao'] == '1') {
                $stFiltro2 = $stFiltro;
            }
        }

        // Para quando for FUNDEB
        if ($_REQUEST['stAcao'] == '1') {
            $obISelectMultiplRecurso2 = new ISelectMultiploRecurso;
            $obISelectMultiplRecurso2->setName("inCodRecurso2");
            $obISelectMultiplRecurso2->setExercicio( Sessao::getExercicio() );
            $obISelectMultiplRecurso2->setFiltro( $stFiltro2 );
            $obISelectMultiplRecurso2->montaRecordSet();

            if ($obISelectMultiplRecurso2->getRecordsetLista1()->getNumLinhas() < 1) {
                $rsRecursoDisponivel2 = $obISelectMultiplRecurso2->getRecordsetLista2();
            } else {
                $rsRecursoDisponivel2 = $obISelectMultiplRecurso2->getRecordsetLista1();
            }

            $inCount = 0;
            while ( !$rsRecursoDisponivel2->eof() ) {
                $js .= "\n arDisponivel2[".$inCount."] = new Option('".$rsRecursoDisponivel2->getCampo('cod_recurso')." - ".$rsRecursoDisponivel2->getCampo('nom_recurso')."', '".$rsRecursoDisponivel2->getCampo('cod_recurso')."', '');";
                $inCount++;
                $rsRecursoDisponivel2->proximo();
            }
        }
        //-------------
        $obISelectMultiplRecurso = new ISelectMultiploRecurso;
        $obISelectMultiplRecurso->setExercicio( Sessao::getExercicio() );
        $obISelectMultiplRecurso->setFiltro( $stFiltro );
        $obISelectMultiplRecurso->montaRecordSet();

        if ($obISelectMultiplRecurso->getRecordsetLista1()->getNumLinhas() < 1) {
            $rsRecursoDisponivel = $obISelectMultiplRecurso->getRecordsetLista2();
        } else {
            $rsRecursoDisponivel = $obISelectMultiplRecurso->getRecordsetLista1();
        }

        $inCount = 0;
        while ( !$rsRecursoDisponivel->eof() ) {
            $js .= "\n arDisponivel[".$inCount."] = new Option('".$rsRecursoDisponivel->getCampo('cod_recurso')." - ".$rsRecursoDisponivel->getCampo('nom_recurso')."', '".$rsRecursoDisponivel->getCampo('cod_recurso')."', '');";
            $inCount++;
            $rsRecursoDisponivel->proximo();
        }

        require_once CAM_GPC_STN_MAPEAMENTO."TSTNVinculoRecurso.class.php";

        //Para quando for FUNDEB
        if ($_REQUEST['stAcao'] == '1') {
            $obTSTNVinculoRecurso2 = new TSTNVinculoRecurso;
            $obTSTNVinculoRecurso2->setDado('exercicio'   , Sessao::getExercicio()        );
            $obTSTNVinculoRecurso2->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
            $obTSTNVinculoRecurso2->setDado('num_orgao'   , $_REQUEST['inCodOrgao']   );
            $obTSTNVinculoRecurso2->setDado('num_unidade' , $_REQUEST['inCodUnidade'] );

            $obTSTNVinculoRecurso2->recuperaVinculoRecurso( $rsRecursoSelecionado2, 'AND cod_vinculo='.$_REQUEST['stAcao'].' AND cod_tipo=1');

            $inCount = 0;
            while ( !$rsRecursoSelecionado2->eof() ) {
                $js .= "\n arSelecionado2[".$inCount."] = new Option('".$rsRecursoSelecionado2->getCampo('cod_recurso')." - " . $rsRecursoSelecionado2->getCampo('nom_recurso') . "', '".$rsRecursoSelecionado2->getCampo('cod_recurso')."', '');";
                $inCount++;
                $rsRecursoSelecionado2->proximo();
            }
        }
        //---------------
        $obTSTNVinculoRecurso = new TSTNVinculoRecurso;
        $obTSTNVinculoRecurso->setDado('exercicio'   , Sessao::getExercicio()        );
        $obTSTNVinculoRecurso->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTSTNVinculoRecurso->setDado('num_orgao'   , $_REQUEST['inCodOrgao']   );
        $obTSTNVinculoRecurso->setDado('num_unidade' , $_REQUEST['inCodUnidade'] );

        $obTSTNVinculoRecurso->recuperaVinculoRecurso( $rsRecursoSelecionado, 'AND cod_vinculo='.$_REQUEST['stAcao'].' AND cod_tipo=2');

        $inCount = 0;
        while ( !$rsRecursoSelecionado->eof() ) {
            $js .= "\n arSelecionado[".$inCount."] = new Option('".$rsRecursoSelecionado->getCampo('cod_recurso')." - " . $rsRecursoSelecionado->getCampo('nom_recurso') . "', '".$rsRecursoSelecionado->getCampo('cod_recurso')."', '');";
            $inCount++;
            $rsRecursoSelecionado->proximo();
        }
    }

    echo $js;

    break;
}

?>
