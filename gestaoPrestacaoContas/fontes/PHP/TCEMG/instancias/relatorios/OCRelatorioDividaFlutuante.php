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
  * Página de Formulario
  * Data de Criação: 31/07/2014
  * @author Desenvolvedor: Evandro Melos
  * $Id: OCRelatorioDividaFlutuante.php 59612 2014-09-02 12:00:51Z gelson $
  * $Date: $
  * $Author: $
  * $Rev: $
  *
*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_MPDF;
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioDividaFlutuante.class.php" );
include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioDividaFlutuante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgOculGera = "OCGera".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$boTransacao = new Transacao();
$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado( 'exercicio'   , Sessao::getExercicio() );
$obTOrcamentoEntidade->recuperaEntidades( $rsEntidade, "", "", $boTransacao);

foreach ($rsEntidade->getElementos() as $entidades) {
    $arCodEntidades[]  = $entidades['cod_entidade'];
}

sort($arCodEntidades);
$inCodEntidades = implode(",", $arCodEntidades);
$stDataInicial  = $request->get('stDataInicial');
$stDataFinal    = $request->get('stDataFinal');

$obTTCEMGRelatorioDividaFlutuante = new TTCEMGRelatorioDividaFlutuante();
$obTTCEMGRelatorioDividaFlutuante->setDado( 'exercicio'      , Sessao::getExercicio());
$obTTCEMGRelatorioDividaFlutuante->setDado( 'cod_entidade'   , $inCodEntidades);
$obTTCEMGRelatorioDividaFlutuante->setDado( 'data_inicial'   , $stDataInicial);
$obTTCEMGRelatorioDividaFlutuante->setDado( 'data_final'     , $stDataFinal);

//Gerando os records sets
$obTTCEMGRelatorioDividaFlutuante->recuperaDepositosDividaFlutuante ($rsDepositoDividaFlutuante , $boTransacao);
$obTTCEMGRelatorioDividaFlutuante->recuperaTotaisOrgao              ($rsTotalOrgao              , $boTransacao);
$obTTCEMGRelatorioDividaFlutuante->recuperaRestosPagar              ($rsRestosPagar             , $boTransacao);
$obTTCEMGRelatorioDividaFlutuante->recuperaBalanceteVerificacao     ($rsBalVerificacao);
$arRestosPagar = $rsRestosPagar->getElementos();

//SOMAR TODOS OS ARRAYS
foreach($arRestosPagar as $restos) {
    if($restos['exercicio']        == Sessao::getExercicio() ){
        $artotal['saldo_anterior_p']   = number_format($arRestosPagar[0]['saldo_anterior_p'],2,',','.');
        $artotal['inscricao_p']        = number_format($arRestosPagar[0]['inscricao_p'],2,',','.');
        $artotal['restabelicimento_p'] = number_format($arRestosPagar[0]['restabelicimento_p'],2,',','.');
        $artotal['baixa_p']            = number_format($arRestosPagar[0]['baixa_p'],2,',','.');
        $artotal['cancelamento_p']     = number_format($arRestosPagar[0]['cancelamento_p'],2,',','.');
        $artotal['saldo_atual_p']      = number_format($arRestosPagar[0]['saldo_atual_p'],2,',','.');

        $artotal['saldo_anterior_np']   = number_format($arRestosPagar[0]['saldo_anterior_np'],2,',','.');
        $artotal['inscricao_np']        = number_format($arRestosPagar[0]['inscricao_np'],2,',','.');
        $artotal['restabelicimento_np'] = number_format($arRestosPagar[0]['restabelicimento_np'],2,',','.');
        $artotal['baixa_np']            = number_format($arRestosPagar[0]['baixa_np'],2,',','.');
        $artotal['cancelamento_np']     = number_format($arRestosPagar[0]['cancelamento_np'],2,',','.');
        $artotal['saldo_atual_np']      = number_format($arRestosPagar[0]['saldo_atual_np'],2,',','.');
    } else {
        $artotal['saldo_anterior_p']   = number_format(0.00,2,',','.');
        $artotal['inscricao_p']        = number_format(0.00,2,',','.');
        $artotal['restabelicimento_p'] = number_format(0.00,2,',','.');
        $artotal['baixa_p']            = number_format(0.00,2,',','.');
        $artotal['cancelamento_p']     = number_format(0.00,2,',','.');
        $artotal['saldo_atual_p']      = number_format(0.00,2,',','.');

        $artotal['saldo_anterior_np']   = number_format(0.00,2,',','.');
        $artotal['inscricao_np']        = number_format(0.00,2,',','.');
        $artotal['restabelicimento_np'] = number_format(0.00,2,',','.');
        $artotal['baixa_np']            = number_format(0.00,2,',','.');
        $artotal['cancelamento_np']     = number_format(0.00,2,',','.');
        $artotal['saldo_atual_np']      = number_format(0.00,2,',','.');
    }
}

$arDados['exercicio']               = Sessao::getExercicio();
$arDados['municipio']               = "BOM DESPACHO";
$arDados['data_inicial']            = $stDataInicial;
$arDados['data_final']              = $stDataFinal;
$arDados['total_restos_entidade']   = $artotal;

$arDados['restos_pagar']            = $rsRestosPagar;
$arDados['depositos']               = $rsBalVerificacao;
$arDados['totais_orgao']            = $rsTotalOrgao;
$arDados['totais_contas_devedoras'] = $rsTotalOrgao;

Sessao::write('arDados', $arDados);
Sessao::write('cod_entidade', $inCodEntidades);
Sessao::write('data_inicial', $stDataInicial);
Sessao::write('data_final'  , $stDataFinal);

SistemaLegado::LiberaFrames(true,true);
$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."relatorios/OCGeraRelatorioDividaFlutuante.php";

SistemaLegado::mudaFramePrincipal($stCaminho);
?>