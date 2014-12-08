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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioBeneficiario.class.php");
include_once (CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

$arBeneficiario = Sessao::read('arBeneficiario');

if (empty($arBeneficiario) || (count($arBeneficiario) == 0)) {
    SistemaLegado::exibeAviso("Você deve inserir ao menos um registro na tabela","incluir","incluir_n");
    SistemaLegado::LiberaFrames(true,False);
    die;
}

$obTBeneficioBeneficiario = new TBeneficioBeneficiario();
$obTBeneficioBeneficiario ->setDado('cod_contrato', $arBeneficiario[0]['inContrato']);
$obTBeneficioBeneficiario->limpaBeneficios();

foreach ($arBeneficiario as $value) {
    $obTBeneficioBeneficiario = new TBeneficioBeneficiario();
    $obTBeneficioBeneficiario->setDado('cod_contrato'      , $value['inContrato']);
    $obTBeneficioBeneficiario->setDado('cgm_fornecedor'    , $value['inCGMFornecedor']);
    $obTBeneficioBeneficiario->setDado('cod_modalidade'    , $value['inModalidade']);
    $obTBeneficioBeneficiario->setDado('cod_tipo_convenio' , $value['inTipo']);
    $obTBeneficioBeneficiario->setDado('cgm_beneficiario'  , $value['inCGMBeneficiario']);
    $obTBeneficioBeneficiario->setDado('grau_parentesco'   , $value['inGrauParentesco']);
    $obTBeneficioBeneficiario->setDado('codigo_usuario'    , $value['inCodUsuario']);
    $obTBeneficioBeneficiario->setDado('dt_inicio'         , $value['dtInicioBeneficio']);
    $obTBeneficioBeneficiario->setDado('dt_fim'            , $value['dtFimBeneficio']);
    $obTBeneficioBeneficiario->setDado('valor'             , $value['vlDesconto']);
    $obTBeneficioBeneficiario->setDado('timestamp'         , date('Y-m-d H:i:s'));

    $obTBeneficioBeneficiario->inclusao();
}

//Limpa tela principal e esvazia sessão
Sessao::remove('arBeneficiario');
$js = "<script>window.parent.frames['telaPrincipal'].limparBeneficio();</script>";
$js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('inContrato').value = '';</script>";
$js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('inNomCGM').innerHTML = '';</script>";
$js.= "<script>window.parent.frames['telaPrincipal'].document.getElementById('spnBeneficiario').innerHTML = '';</script>";
echo $js;

SistemaLegado::exibeAviso("Benefício salvo","incluir","incluir_n");
